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
require_once 'Customweb/Core/Util/Rand.php';
require_once 'Customweb/Payment/Authorization/Recurring/IAdapter.php';
require_once 'PayEngineCw/Util.php';
require_once 'PayEngineCw/AbstractCartOrderContext.php';
require_once 'PayEngineCw/CartUtil.php';


/**
 * This class implements a order context based on user data and the current cart.
 * This order context should never be persisted!
 * 
 * @author hunziker
 *
 */
class PayEngineCw_CartOrderContext extends PayEngineCw_AbstractCartOrderContext {

	public function __construct($userData, Customweb_Payment_Authorization_IPaymentMethod $paymentMethod, $userId = null){
		global $woocommerce;
		
		$sessionHandler = $woocommerce->session;
		
		if(method_exists($sessionHandler, 'get')){
			$checkoutId = $sessionHandler->get('PayEngineCwCheckoutId', null);
			if($checkoutId === null) {
				$checkoutId = Customweb_Core_Util_Rand::getUuid();
				$sessionHandler->set('PayEngineCwCheckoutId', $checkoutId);
			}
		}
		else{
			$checkoutId = $sessionHandler->PayEngineCwCheckoutId;
			if($checkoutId === null) {
				$checkoutId = Customweb_Core_Util_Rand::getUuid();
				$sessionHandler->PayEngineCwCheckoutId = $checkoutId;
			}
		}

		$this->checkoutId = $checkoutId;
		
		$this->cart = $woocommerce->cart;
		$this->cart->calculate_totals();
		
		if (!isset($userData['billing_country']) || $userData['billing_country'] == '') {
			$wcCountries = new WC_Countries();
			$allowedCountries = $wcCountries->get_allowed_countries();
			if (count($allowedCountries) == 1) {
				reset($allowedCountries);
				$userData['billing_country'] = key($allowedCountries);
			}
		}
		
		$this->userData = $userData;
		$this->currencyCode = get_woocommerce_currency();
		$this->paymentMethod = $paymentMethod;
		$this->orderAmount = $this->cart->total;
		$this->language = get_bloginfo('language');
		
		if ($userId === null) {
			$this->userId = get_current_user_id();
		}
		else {
			$this->userId = $userId;
		}
	}

	public function isNewSubscription(){
		$result = false;
		
		if( class_exists('WC_Subscriptions_Cart') && WC_Subscriptions_Cart::cart_contains_subscription() &&
					 ('yes' != get_option(WC_Subscriptions_Admin::$option_prefix . '_turn_off_automatic_payments', 'no')));
			$adapter = PayEngineCw_Util::getAuthorizationAdapter(
					Customweb_Payment_Authorization_Recurring_IAdapter::AUTHORIZATION_METHOD_NAME);
			if ($adapter->isPaymentMethodSupportingRecurring($this->getPaymentMethod())) {
				$result = true;
			}
		
		
		return $result;
	}

	public function getInvoiceItems(){
		return PayEngineCw_CartUtil::getInoviceItemsFromCart($this->cart);
	}

	public function getShippingMethod(){
		return $this->cart->shipping_label;
	}

	protected function isShipToBilling(){
		if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.1.0') >= 0) {
			if (isset($this->userData['ship_to_different_address']) && $this->userData['ship_to_different_address'] == '1') {
				return false;
			}
			else {
				return true;
			}
		}
		if (isset($this->userData['shiptobilling']) && $this->userData['shiptobilling'] == '1') {
			return true;
		}
		else {
			return false;
		}
	}
}