<?php

/**
 *  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */

require_once 'Customweb/Core/Exception/CastException.php';
require_once 'PayEngineCw/Util.php';
require_once 'Customweb/Core/Http/Response.php';
require_once 'PayEngineCw/ConfigurationAdapter.php';
require_once 'PayEngineCw/PaymentMethodWrapper.php';
require_once 'PayEngineCw/OrderContext.php';
require_once 'Customweb/Util/Country.php';
require_once 'PayEngineCw/Entity/ExternalCheckoutContext.php';
require_once 'Customweb/Payment/Authorization/OrderContext/Address/Default.php';
require_once 'Customweb/Payment/ExternalCheckout/AbstractCheckoutService.php';



/**
 *
 * @author Nico Eigenmann
 * @Bean
 */
class PayEngineCw_ExternalCheckoutService extends Customweb_Payment_ExternalCheckout_AbstractCheckoutService {

	public function loadContext($contextId, $cache = true){
		return PayEngineCw_Entity_ExternalCheckoutContext::getContextById($contextId, $cache);
	}

	protected function updateShippingMethodOnContext(Customweb_Payment_ExternalCheckout_IContext $context, Customweb_Core_Http_IRequest $request){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		
		$parameters = $request->getParameters();
		
		$selectedMethod = array();
		$chosenMethod = array();
		
		if (isset($parameters['shipping_method']) && is_array($parameters['shipping_method'])) {
			// Validate Shipping Methods
			WC()->shipping->calculate_shipping(WC()->cart->get_shipping_packages());
			$packages = WC()->shipping->get_packages();
			foreach ($packages as $i => $package) {
				if (!isset($package['rates'][$parameters['shipping_method'][$i]])) {
					throw new Exception(__("Not a valid shipping method", "woocommerce_payenginecw"));
				}
				if (isset($parameters['shipping_method'][$i])) {
					$selectedMethod[$i] = wc_clean($parameters['shipping_method'][$i]);
				}
			}
		}
		else {
			throw new Exception(__("Please select a shipping method.", "woocommerce_payenginecw"));
		}
		$context->setSelectedShippingMethods($selectedMethod);
		WC()->session->set('chosen_shipping_methods', $selectedMethod);
		$this->refreshContext($context);
		$this->getEntityManager()->persist($context);
	}

	private function getShippingMethodNameFromContext(PayEngineCw_Entity_ExternalCheckoutContext $context){
		if (!(WC()->cart->needs_shipping())) {
			return __("Free Shipping", "woocommerce_payenginecw");
		}
		$selectedMethods = $context->getSelectedShippingMethods();
		if (count($selectedMethods) > 1) {
			return __("Multiple Shipping Methods", "woocommerce_payenginecw");
		}
		elseif (count($selectedMethods) == 1) {
			$available = WC_Shipping::instance()->load_shipping_methods();
			$methodKey = $selectedMethods[0];
			
			if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.6.0') >= 0) {
				$splitted = explode(':', $methodKey);
				$methodKey = $splitted[0];
			}
			
			//WooShip compatibility 
			if (substr($methodKey, 0, strlen('wooship')) == 'wooship') {
				$methodKey = 'wooship';
			}
			
			if (isset($available[$methodKey])) {
				$shippingMethod = $available[$methodKey];
				return $shippingMethod->get_title();
			}
			//Woocommerce Advanced Shipping compatibility
			if (isset($available['advanced_shipping'])) {
				if (is_numeric($methodKey) && get_post_type($methodKey) == 'was' && get_post_status($methodKey) == 'publish') {
					$wasMeta = get_post_meta($methodKey, '_was_shipping_method', true);
					$name = __("Shipping", "woocommerce_payenginecw");
					if (!empty($wasMeta['shipping_title'])) {
						$name = $wasMeta['shipping_title'];
					}
					return $name;
				}
			}
		}
		return null;
	}

	protected function extractShippingName(Customweb_Payment_ExternalCheckout_IContext $context, Customweb_Core_Http_IRequest $request){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		return $this->getShippingMethodNameFromContext($context);
	}

	protected function refreshContext(Customweb_Payment_ExternalCheckout_AbstractContext $context){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		if ($context->getBillingAddress() != null || $context->getShippingAddress() != null) {
			$customer = new WC_Customer();
			//Creates and loads customer from Session
			if ($context->getBillingAddress() != null) {
				$customer->set_address($context->getBillingAddress()->getStreet());
				$customer->set_address_2('');
				$customer->set_city($context->getBillingAddress()->getCity());
				$customer->set_country($context->getBillingAddress()->getCountryIsoCode());
				$customer->set_postcode($context->getBillingAddress()->getPostCode());
				$customer->set_state($context->getBillingAddress()->getState());
			}
			if ($context->getShippingAddress() != null) {
				$customer->set_shipping_address($context->getShippingAddress()->getStreet());
				$customer->set_shipping_address_2('');
				$customer->set_shipping_city($context->getShippingAddress()->getCity());
				$customer->set_shipping_country($context->getShippingAddress()->getCountryIsoCode());
				$customer->set_shipping_postcode($context->getShippingAddress()->getPostCode());
				$customer->set_shipping_state($context->getShippingAddress()->getState());
			}
			$customer->save_data();
		}
		if ($context->getPaymentMethod() != null) {
			WC()->session->set('chosen_payment_method', 'PayEngineCw_' . $context->getPaymentMethod()->class_name);
		}
		$context->updateFromCart(WC()->cart);
		
		$defaultMethods = array();
		if (WC()->cart->needs_shipping()) {
			$packages = WC()->shipping->get_packages();
			foreach ($packages as $i => $package) {
				if (isset($package['rates']) && count($package['rates']) >= 1) {
					$cheapestCost = false;
					$cheapestMethod = false;
					
					foreach ($package['rates'] as $method_id => $method) {
						if ($method->cost < $cheapestCost || !is_numeric($cheapestCost)) {
							$cheapestCost = $method->cost;
							$cheapestMethod = $method_id;
						}
					}
					$defaultMethods[$i] = $cheapestMethod;
				}
			}
		}
		$selectedShipping = $context->getSelectedShippingMethods();
		if (empty($selectedShipping)) {
			$selectedShipping = array();
		}
		$selectedShipping += $defaultMethods;
		if (empty($selectedShipping)) {
			WC()->session->set('chosen_shipping_methods', $selectedShipping);
		}
		$context->setSelectedShippingMethods($selectedShipping);
		$context->setShippingMethodName($this->getShippingMethodNameFromContext($context));
	}

	protected function updateUserSessionWithCurrentUser(Customweb_Payment_ExternalCheckout_AbstractContext $context){
		if (is_user_logged_in()) {
			return;
		}
		$email = $context->getCustomerEmailAddress();
		$user = get_user_by('email', $email);
		if ($user !== false) {
			wp_set_current_user($user->ID, $user->user_login);
			wp_set_auth_cookie($user->ID);
			do_action('wp_login', $user->user_login);
			$this->refreshContext($context);
		}
		
		//guest checkout
	}

	protected function createTransactionContextFromContext(Customweb_Payment_ExternalCheckout_IContext $context){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		
		if (!defined('WOOCOMMERCE_CHECKOUT')) {
			define('WOOCOMMERCE_CHECKOUT', true);
		}
		
		// Prevent timeout
		@set_time_limit(0);
		
		do_action('woocommerce_before_checkout_process');
		
		do_action('woocommerce_checkout_process');
		
		$paymentMethod = $context->getPaymentMethod();
		
		WC()->session->set('chosen_shipping_methods', $context->getSelectedShippingMethods());
		WC()->session->set('chosen_payment_method', $paymentMethod->class_name);
		
		WC()->cart->calculate_totals();
		
		$customer_id = apply_filters('woocommerce_checkout_customer_id', get_current_user_id());
		
		// Do a final stock check at this point
		do_action('woocommerce_check_cart_items');
		
		// Abort if errors are present
		if (wc_notice_count('error') > 0) {
			throw new Exception();
		}
		
		//Do the same as create_order in WC_Checkout
		try {
			global $wpdb;
			// Start transaction if available
			$wpdb->query('START TRANSACTION');
			$additionalValue = $context->getAdditionalValues();
			$order_data = array(
				'status' => apply_filters('woocommerce_default_order_status', 'pending'),
				'customer_id' => $customer_id,
				'customer_note' => isset($additionalValue['order-note']) ? $additionalValue['order-note'] : '' 
			);
			
			$order = wc_create_order($order_data);
			
			if (is_wp_error($order)) {
				throw new Exception(sprintf(__('Error %d: Unable to create order. Please try again.', 'woocommerce'), 400));
			}
			else {
				$order_id = $order->id;
				do_action('woocommerce_new_order', $order_id);
			}
			
			// Store the line items to the new/resumed order
			foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
				$item_id = $order->add_product($values['data'], $values['quantity'], 
						array(
							'variation' => $values['variation'],
							'totals' => array(
								'subtotal' => $values['line_subtotal'],
								'subtotal_tax' => $values['line_subtotal_tax'],
								'total' => $values['line_total'],
								'tax' => $values['line_tax'],
								'tax_data' => $values['line_tax_data'] 
							) // Since 2.2
 
						));
				
				if (!$item_id) {
					throw new Exception(sprintf(__('Error %d: Unable to create order. Please try again.', 'woocommerce'), 402));
				}
				
				// Allow plugins to add order item meta
				do_action('woocommerce_add_order_item_meta', $item_id, $values, $cart_item_key);
			}
			
			// Store fees
			foreach (WC()->cart->get_fees() as $fee_key => $fee) {
				$item_id = $order->add_fee($fee);
				
				if (!$item_id) {
					throw new Exception(sprintf(__('Error %d: Unable to create order. Please try again.', 'woocommerce'), 403));
				}
				
				// Allow plugins to add order item meta to fees
				do_action('woocommerce_add_order_fee_meta', $order_id, $item_id, $fee, $fee_key);
			}
			
			if (WC()->cart->needs_shipping()) {
				$shippingMethods = $context->getSelectedShippingMethods();
				// Store shipping for all packages
				foreach (WC()->shipping->get_packages() as $package_key => $package) {
					if (isset($package['rates'][$shippingMethods[$package_key]])) {
						$item_id = $order->add_shipping($package['rates'][$shippingMethods[$package_key]]);
						
						if (!$item_id) {
							throw new Exception(sprintf(__('Error %d: Unable to create order. Please try again.', 'woocommerce'), 404));
						}
						
						// Allows plugins to add order item meta to shipping
						do_action('woocommerce_add_shipping_order_item', $order_id, $item_id, $package_key);
					}
				}
			}
			
			// Store tax rows
			foreach (array_keys(WC()->cart->taxes + WC()->cart->shipping_taxes) as $tax_rate_id) {
				if ($tax_rate_id &&
						 !$order->add_tax($tax_rate_id, WC()->cart->get_tax_amount($tax_rate_id), WC()->cart->get_shipping_tax_amount($tax_rate_id)) &&
						 apply_filters('woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated') !== $tax_rate_id) {
					throw new Exception(sprintf(__('Error %d: Unable to create order. Please try again.', 'woocommerce'), 405));
				}
			}
			
			// Store coupons
			foreach (WC()->cart->get_coupons() as $code => $coupon) {
				if (!$order->add_coupon($code, WC()->cart->get_coupon_discount_amount($code), WC()->cart->get_coupon_discount_tax_amount($code))) {
					throw new Exception(sprintf(__('Error %d: Unable to create order. Please try again.', 'woocommerce'), 406));
				}
			}
			
			$billing = $this->convertAddressToArray($context->getBillingAddress(), true, true);
			if (!isset($billing['email']) || $billing['email' == '']) {
				$billing['email'] = $context->getCustomerEmailAddress();
			}
			
			$order->set_address($billing, 'billing');
			$order->set_address($this->convertAddressToArray($context->getShippingAddress()), 'shipping');
			$order->set_payment_method($paymentMethod);
			$order->set_total(WC()->cart->shipping_total, 'shipping');
			$order->set_total(WC()->cart->get_cart_discount_total(), 'cart_discount');
			$order->set_total(WC()->cart->get_cart_discount_tax_total(), 'cart_discount_tax');
			$order->set_total(WC()->cart->tax_total, 'tax');
			$order->set_total(WC()->cart->shipping_tax_total, 'shipping_tax');
			$order->set_total(WC()->cart->total);
			
			// If we got here, the order was created without problems!
			$wpdb->query('COMMIT');
		}
		catch (Exception $e) {
			// There was an error adding order data!
			$wpdb->query('ROLLBACK');
			throw new $e->getMessage();
		}
		
		$orderContext = new PayEngineCw_OrderContext($order, new PayEngineCw_PaymentMethodWrapper($paymentMethod));
		$dbTransaction = $paymentMethod->newDatabaseTransaction($orderContext);
		return $paymentMethod->newTransactionContext($dbTransaction, $orderContext);
	}

	public function authenticate(Customweb_Payment_ExternalCheckout_IContext $context, $emailAddress, $successUrl){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		if ($context->getBillingAddress() === null) {
			$billingAddress = new Customweb_Payment_Authorization_OrderContext_Address_Default();
			$billingAddress->setFirstName('First')->setLastName('Last')->setCity('unkown')->setStreet('unkown 1')->setCountryIsoCode('DE')->setPostCode(
					'10000');
			$context->setBillingAddress($billingAddress);
		}
		if (is_user_logged_in()) {
			/**
			 *
			 * @var $user WP_User
			 */
			$user = wp_get_current_user();
			if (empty($emailAddress)) {
				$emailAddress = $user->get('user_email');
			}
			$context->setCustomerEmailAddress($emailAddress);
			$this->refreshContext($context);
			$this->getEntityManager()->persist($context);
			return Customweb_Core_Http_Response::redirect($successUrl);
		}
		
		$externalCheckout = PayEngineCw_ConfigurationAdapter::getExternalCheckoutAccountCreation();
		
		if ($externalCheckout === 'skip_selection') {
			if (!empty($emailAddress)) {
				$context->setCustomerEmailAddress($emailAddress);
				$this->refreshContext($context);
				$this->getEntityManager()->persist($context);
				return Customweb_Core_Http_Response::redirect($successUrl);
			}
		}
		
		$context->setAuthenticationEmailAddress($emailAddress);
		$context->setAuthenticationSuccessUrl($successUrl);
		$this->getEntityManager()->persist($context);
		$url = PayEngineCw_Util::getPluginUrl('externalLogin', 
				array(
					'payenginecw-context-id' => $context->getContextId(),
					'token' => $context->getSecurityToken() 
				));
		
		return Customweb_Core_Http_Response::redirect($url);
	}

	public function renderShippingMethodSelectionPane(Customweb_Payment_ExternalCheckout_IContext $context, $errorMessages){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		if (!WC()->cart->needs_shipping()) {
			return;
		}
		WC()->cart->calculate_shipping();
		$shipping = WC_Shipping::instance();
		
		$packages = WC()->shipping->get_packages();
		$selectedMethods = $context->getSelectedShippingMethods();
		
		ob_start();
		if (count($packages) == 1) {
			$selectedMethod = isset($selectedMethods[0]) ? $selectedMethods[0] : '';
			$package = current($packages);
			PayEngineCw_Util::includeTemplateFile('external_shipping_single', 
					array(
						'package' => $package,
						'availableMethods' => $package['rates'],
						'index' => 0,
						'selectedMethod' => $selectedMethod 
					));
		}
		else {
			foreach ($packages as $i => $package) {
				$selectedMethod = isset($selectedMethods[$i]) ? $selectedMethods[$i] : '';
				PayEngineCw_Util::includeTemplateFile('external_shipping_row', 
						array(
							'package' => $package,
							'availableMethods' => $package['rates'],
							'index' => $i,
							'selectedMethod' => $selectedMethod 
						));
			}
		}
		$rows = ob_get_clean();
		ob_start();
		PayEngineCw_Util::includeTemplateFile('external_shipping_table', array(
			'rows' => $rows,
			'errorMessage' => $errorMessages 
		));
		return ob_get_clean();
	}

	public function getPossiblePaymentMethods(Customweb_Payment_ExternalCheckout_IContext $context){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		
		$paymentMethods = array();
		foreach (PayEngineCw_Util::getPaymentMethods(false) as $method) {
			$paymentMethods[] = PayEngineCw_Util::getPaymentMehtodInstance($method);
		}
		
		return $paymentMethods;
	}

	public function renderReviewPane(Customweb_Payment_ExternalCheckout_IContext $context, $renderConfirmationFormElements, $errorMessage){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		
		$cart = WC()->cart;
		$context->updateFromCart($cart);
		$context->setVerificationHash(
				PayEngineCw_Entity_ExternalCheckoutContext::computeVerificationHash($context->getInvoiceItems(), 
						$context->getOrderAmountInDecimals(), $context->getCurrencyCode()));
		$this->getEntityManager()->persist($context);
		$variables = array();
		if (!empty($errorMessage)) {
			$variables['errorMessage'] = $errorMessage;
		}
		if ($context->getBillingAddress() != null) {
			$billing = array();
			$billing['name'] = $context->getBillingAddress()->getFirstName() . ' ' . $context->getBillingAddress()->getLastName();
			$billing['street'] = $context->getBillingAddress()->getStreet();
			$billing['city'] = $context->getBillingAddress()->getCity();
			$stateCode = $context->getBillingAddress()->getState();
			if (!empty($stateCode)) {
				$stateCode = Customweb_Util_Country::getStateByCode($context->getBillingAddress()->getCountryIsoCode(), $stateCode);
				$billing['state'] = $stateCode['name'];
			}
			$billing['postCode'] = $context->getBillingAddress()->getPostCode();
			$country = Customweb_Util_Country::getCountryByCode($context->getBillingAddress()->getCountryIsoCode());
			$billing['country'] = $country['name'];
			$variables['billing'] = $billing;
		}
		if ($context->getShippingAddress() != null) {
			$shipping = array();
			$shipping['name'] = $context->getShippingAddress()->getFirstName() . ' ' . $context->getShippingAddress()->getLastName();
			$shipping['street'] = $context->getShippingAddress()->getStreet();
			$shipping['city'] = $context->getShippingAddress()->getCity();
			$stateCode = $context->getShippingAddress()->getState();
			if (!empty($stateCode)) {
				$stateCode = Customweb_Util_Country::getStateByCode($context->getShippingAddress()->getCountryIsoCode(), $stateCode);
				$shipping['state'] = $stateCode['name'];
			}
			$shipping['postCode'] = $context->getShippingAddress()->getPostCode();
			$country = Customweb_Util_Country::getCountryByCode($context->getShippingAddress()->getCountryIsoCode());
			$shipping['country'] = $country['name'];
			$variables['shipping'] = $shipping;
		}
		
		$cartVariables = array();
		if ($context->getPaymentMethodMachineName() != null) {
			WC()->session->set('chosen_payment_method', 'PayEngineCw_' . $context->getPaymentMethodMachineName());
		}
		
		$cartVariables['lineItems'] = $this->prepareCartItemsArray($cart);
		$cartVariables['coupons'] = $this->prepareCouponsArray($cart);
		$cartVariables['fees'] = $this->prepareFeesArray($cart);
		$cartVariables['totalAmount'] = $this->formatOrderTotal($cart);
		$cartVariables['subTotal'] = $this->formatSubOrderTotal($cart);
		if ($cart->needs_shipping()) {
			$cartVariables['shipping'] = $this->formatSubShippingTotal($cart);
		}
		$cartVariables['taxes'] = $this->formatTaxesArray($cart);
		
		ob_start();
		PayEngineCw_Util::includeTemplateFile('external_confirm_cart', $cartVariables);
		$variables['orderReview'] = ob_get_clean();
		
		$variables['showConfirm'] = $renderConfirmationFormElements;
		
		ob_start();
		PayEngineCw_Util::includeTemplateFile('external_confirm', $variables);
		return ob_get_clean();
	}

	public function validateReviewForm(Customweb_Payment_ExternalCheckout_IContext $context, Customweb_Core_Http_IRequest $request){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		if (WC()->cart->needs_shipping()) {
			$shipping = $context->getSelectedShippingMethods();
			if ($shipping === null) {
				throw new Exception(__('Please select a shipping method', 'woocommerce_payenginecw'));
			}
			foreach ($shipping as $i => $method) {
				if (empty($method)) {
					throw new Exception(__('Please select a shipping method', 'woocommerce_payenginecw'));
				}
			}
		}
		$context->updateFromCart(WC()->cart);
		$currentCartHash = PayEngineCw_Entity_ExternalCheckoutContext::computeVerificationHash($context->getInvoiceItems(), 
				$context->getOrderAmountInDecimals(), $context->getCurrencyCode());
		if ($currentCartHash != $context->getVerificationHash()) {
			throw new Exception(__('Cart has been modified after displaying order review', 'woocommerce_payenginecw'));
		}
		$parameters = $request->getParameters();
		if ((wc_get_page_id('terms') > 0) && (!isset($parameters['terms']) || strtolower($parameters['terms']) != 'on')) {
			throw new Exception(__('You must accept our Terms &amp; Conditions.', 'woocommerce'));
		}
	}

	public function renderAdditionalFormElements(Customweb_Payment_ExternalCheckout_IContext $context, $errorMessage){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		$variables = array();
		if (!empty($errorMessage)) {
			$variables['errorMessage'] = $errorMessage;
		}
		$variables['values'] = $context->getAdditionalValues();
		ob_start();
		PayEngineCw_Util::includeTemplateFile('external_additional', $variables);
		return ob_get_clean();
	}

	public function processAdditionalFormElements(Customweb_Payment_ExternalCheckout_IContext $context, Customweb_Core_Http_IRequest $request){
		if (!($context instanceof PayEngineCw_Entity_ExternalCheckoutContext)) {
			throw new Customweb_Core_Exception_CastException('PayEngineCw_Entity_ExternalCheckoutContext');
		}
		
		$parameters = $request->getParameters();
		$additional = array();
		if (isset($parameters['order-note'])) {
			$additional['order-note'] = $parameters['order-note'];
		}
		$context->setAdditionalValues($additional);
		$this->getEntityManager()->persist($context);
	}

	protected function prepareCartItemsArray($cart){
		$items = array();
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
			
			if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
				$tmpItem = array();
				$tmpItem['name'] = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
				$tmpItem['quantity'] = $cart_item['quantity'];
				$tmpItem['total'] = apply_filters('woocommerce_cart_item_subtotal', $cart->get_product_subtotal($_product, $cart_item['quantity']), 
						$cart_item, $cart_item_key);
			}
			$items[] = $tmpItem;
		}
		return $items;
	}

	protected function prepareCouponsArray($cart){
		$coupons = array();
		foreach ($cart->get_coupons() as $code => $coupon) {
			$tmpCoupon = array();
			$tmpCoupon['name'] = apply_filters('woocommerce_cart_totals_coupon_label', 
					esc_html(__('Coupon:', 'woocommerce') . ' ' . $coupon->code), $coupon);
			
			$amount = $cart->get_coupon_discount_amount($coupon->code, $cart->display_cart_ex_tax);
			if ($amount > 0) {
				$discount_html = '-' . wc_price($amount);
			}
			else {
				$discount_html = '';
			}
			$value = array();
			$value[] = apply_filters('woocommerce_coupon_discount_amount_html', $discount_html, $coupon);
			
			if ($coupon->enable_free_shipping()) {
				$value[] = __('Free shipping coupon', 'woocommerce');
			}
			
			// get rid of empty array elements
			$value = array_filter($value);
			$value = implode(', ', $value);
			
			$tmpCoupon['amount'] = apply_filters('woocommerce_cart_totals_coupon_html', $value, $coupon);
			
			$coupons[$code] = $tmpCoupon;
		}
		return $coupons;
	}

	protected function prepareFeesArray($cart){
		$fees = array();
		foreach ($cart->get_fees() as $fee) {
			$tmpFee = array();
			$tmpFee['name'] = esc_html($fee->name);
			$amount = $fee->amount;
			if ('excl' != $cart->tax_display_cart) {
				$amount += $fee->tax;
			}
			$tmpFee['amount'] = apply_filters('woocommerce_cart_totals_fee_html', wc_price($amount), $fee);
			$fees[] = $tmpFee;
		}
		return $fees;
	}

	protected function formatOrderTotal($cart){
		$total = '<strong>' . $cart->get_total() . '</strong> ';
		
		// If prices are tax inclusive, show taxes here
		if (wc_tax_enabled() && WC()->cart->tax_display_cart == 'incl') {
			$tax_string_array = array();
			
			if (get_option('woocommerce_tax_total_display') == 'itemized') {
				foreach ($cart->get_tax_totals() as $code => $tax)
					$tax_string_array[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
			}
			else {
				$tax_string_array[] = sprintf('%s %s', wc_price($cart->get_taxes_total(true, true)), WC()->countries->tax_or_vat());
			}
			
			if (!empty($tax_string_array))
				$total .= '<small class="includes_tax">' . sprintf(__('(Includes %s)', 'woocommerce'), implode(', ', $tax_string_array)) .
						 '</small>';
		}
		return $total;
	}

	protected function formatSubOrderTotal($cart){
		// Display varies depending on settings
		if ($cart->tax_display_cart == 'excl') {
			$cart_subtotal = wc_price($cart->subtotal_ex_tax);
			if ($cart->tax_total > 0 && $cart->prices_include_tax) {
				$cart_subtotal .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
		else {
			$cart_subtotal = wc_price($cart->subtotal);
			
			if ($cart->tax_total > 0 && !$cart->prices_include_tax) {
				$cart_subtotal .= ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		}
		return apply_filters('woocommerce_cart_subtotal', $cart_subtotal, false, $cart);
	}

	protected function formatSubShippingTotal($cart){
		if ($cart->shipping_total > 0) {
			// Display varies depending on settings
			if ($cart->tax_display_cart == 'excl') {
				
				$return = wc_price($cart->shipping_total);
				
				if ($cart->shipping_tax_total > 0 && $cart->prices_include_tax) {
					$return .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
				
				return $return;
			}
			else {
				
				$return = wc_price($cart->shipping_total + $cart->shipping_tax_total);
				
				if ($cart->shipping_tax_total > 0 && !$cart->prices_include_tax) {
					$return .= ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
				
				return $return;
			}
		}
		else {
			return __('Free', 'woocommerce_payenginecw');
		}
	}

	protected function formatTaxesArray($cart){
		$taxes = array();
		if ($cart->tax_display_cart === 'excl') {
			if (get_option('woocommerce_tax_total_display') === 'itemized') {
				foreach ($cart->get_tax_totals() as $code => $tax) {
					$tmpTaxes = array();
					$tmpTaxes['name'] = esc_html($tax->label);
					$tmpTaxes['amount'] = wc_price($tax->amount);
					$taxes[$code] = $tmpTaxes;
				}
			}
			else {
				$tmpTaxes = array();
				$tmpTaxes['name'] = esc_html(WC()->countries->tax_or_vat());
				$tmpTaxes['amount'] = wc_price($cart->get_taxes_total());
				$taxes['all'] = $tmpTaxes;
			}
		}
		return $taxes;
	}

	private function convertAddressToArray(Customweb_Payment_Authorization_OrderContext_IAddress $address, $addEmail = false, $addPhone = false){
		$addressArray = array();
		if ($addEmail && $address->getEMailAddress() != null) {
			$addressArray['email'] = $address->getEMailAddress();
		}
		if ($addPhone && $address->getPhoneNumber() != null) {
			$addressArray['phone'] = $address->getPhoneNumber();
		}
		if ($address->getFirstName() != null) {
			$addressArray['first_name'] = $address->getFirstName();
		}
		if ($address->getLastName() != null) {
			$addressArray['last_name'] = $address->getLastName();
		}
		if ($address->getCompanyName() != null) {
			$addressArray['company'] = $address->getCompanyName();
		}
		if ($address->getStreet() != null) {
			$addressArray['address_1'] = $address->getStreet();
		}
		if ($address->getPostCode() != null) {
			$addressArray['postcode'] = $address->getPostCode();
		}
		if ($address->getState() != null) {
			$addressArray['state'] = $address->getState();
		}
		if ($address->getCountryIsoCode() != null) {
			$addressArray['country'] = $address->getCountryIsoCode();
		}
		if ($address->getCity() != null) {
			$addressArray['city'] = $address->getCity();
		}
		return $addressArray;
	}
}