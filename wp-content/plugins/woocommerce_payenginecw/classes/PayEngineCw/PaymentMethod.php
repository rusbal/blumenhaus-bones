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
require_once 'Customweb/Payment/Authorization/Recurring/IAdapter.php';
require_once 'PayEngineCw/Util.php';
require_once 'PayEngineCw/AbstractPaymentMethod.php';
require_once 'PayEngineCw/RecurringOrderContextNew.php';
require_once 'Customweb/Core/Stream/Input/File.php';
require_once 'Customweb/Core/Http/ContextRequest.php';
require_once 'PayEngineCw/RecurringTransactionContext.php';
require_once 'PayEngineCw/RecurringOrderContext.php';
require_once 'PayEngineCw/PaymentMethodWrapper.php';



/**
 *         		  	 			   		
 * This class handlers the main payment interaction with the
 * PayEngineCw server.
 */
class PayEngineCw_PaymentMethod extends PayEngineCw_AbstractPaymentMethod {

	protected function getMethodSettings(){
		return array();
	}

	public function __construct(){
		$this->class_name = substr(get_class($this), 0, 39);
		
		$this->id = $this->class_name;
		$this->method_title = $this->admin_title;
		
		// Load the form fields.
		$this->form_fields = $this->createMethodFormFields();
		
		// Load the settings.
		$this->init_settings();
		
		parent::__construct();
		
		// Workaround: When some setting is stored all PayEngineCw methods are
		// deactivated. With this check we allow the storage only in case the class
		// is called from the payment_gateways tab.
		

		if ((isset($_SERVER['QUERY_STRING']) &&
				 (stristr($_SERVER['QUERY_STRING'], 'tab=payment_gateways') || stristr($_SERVER['QUERY_STRING'], 'tab=checkout'))) ||
				 (isset($_GET['tab']) && ($_GET['tab'] == 'payment_gateways' || $_GET['tab'] == 'checkout'))) {
			if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.0.0') >= 0) {
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(
					$this,
					'process_admin_options' 
				));
			}
			else {
				add_action('woocommerce_update_options', array(
					&$this,
					'process_admin_options' 
				));
			}
		}
		
		
		if ($this->getPaymentMethodConfigurationValue('enabled') == 'yes') {
			$adapter = PayEngineCw_Util::getAuthorizationAdapter(
					Customweb_Payment_Authorization_Recurring_IAdapter::AUTHORIZATION_METHOD_NAME);
			if ($adapter->isPaymentMethodSupportingRecurring($this)) {
				$this->supports = array(
					'subscriptions',
					'products',
					'subscription_cancellation',
					'subscription_reactivation',
					'subscription_suspension',
					'subscription_amount_changes',
					'subscription_date_changes',
					'multiple_subscriptions',
					'product_variation' ,
				);
			}
		}
		if (class_exists('WC_Subscriptions') && version_compare(WC_Subscriptions::$version, '2.0') < 0) {
			add_action('scheduled_subscription_payment_' . $this->id, array(
				$this,
				'scheduledSubscriptionPayment' 
			), 10, 3);
		}
		else if (class_exists('WC_Subscriptions') && version_compare(WC_Subscriptions::$version, '2.0') >= 0) {
			add_action('woocommerce_scheduled_subscription_payment_' . $this->id, array(
				$this,
				'scheduledSubscriptionPaymentNew' 
			), 10, 3);
		}
		
	}

	public function getPaymentMethodConfigurationValue($key, $languageCode = null){
		$settingsArray = array_merge($this->createMethodFormFields(), $this->getMethodSettings());
		if (!isset($settingsArray[$key])) {
			return null;
		}
		if (isset($settingsArray[$key]['cwType']) && $settingsArray[$key]['cwType'] == 'file') {
			$value = $this->settings[$key];
			if (isset($value['path']) && file_exists($value['path'])) {
				return new Customweb_Core_Stream_Input_File($value['path']);
			}
			else {
				$resolver = PayEngineCw_Util::getAssetResolver();
				if (!empty($value)) {
					return $resolver->resolveAssetStream($value);
				}
			}
		}
		elseif (isset($settingsArray[$key]['cwType']) && $settingsArray[$key]['cwType'] == 'multiselect') {
			$value = $this->settings[$key];
			if (empty($value)) {
				return array();
			}
			return $value;
		}
		elseif (isset($this->settings[$key])) {
			return $this->settings[$key];
		}
		else {
			return null;
		}
	}

	public function existsPaymentMethodConfigurationValue($key, $languageCode = null){
		$settingsArray = array_merge($this->createMethodFormFields(), $this->getMethodSettings());
		if (isset($settingsArray[$key])) {
			
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Generate the HTML output for the settings form.
	 */
	public function admin_options(){
		$output = '<h3>' . __($this->admin_title, 'woocommerce_payenginecw') . '</h3>';
		$output .= '<p>' . $this->getBackendDescription() . '</p>';
		
		$output .= '<table class="form-table">';
		
		echo $output;
		
		$this->generate_settings_html();
		
		echo '</table>';
	}

	function generate_select_html($key, $data){
		// We need to override this method, because we need to get
		// the order status, after we defined the form fields. The
		// terms are not accessible before.
		if (isset($data['is_order_status']) && $data['is_order_status'] == true) {
			if (isset($data['options']) && is_array($data['options'])) {
				$data['options'] = $this->getOrderStatusOptions($data['options']);
			}
			else {
				$data['options'] = $this->getOrderStatusOptions();
			}
		}
		return parent::generate_select_html($key, $data);
	}
	
	
	public function scheduledSubscriptionPayment($amountToCharge, $order, $productId){
		global $payenginecw_recurring_process_failure;
		$payenginecw_recurring_process_failure = NULL;
		try {
			$adapter = PayEngineCw_Util::getAuthorizationAdapter(
					Customweb_Payment_Authorization_Recurring_IAdapter::AUTHORIZATION_METHOD_NAME);
			
			$orderContext = new PayEngineCw_RecurringOrderContext($order, new PayEngineCw_PaymentMethodWrapper($this), $amountToCharge, 
					$productId);
			$dbTransaction = $this->newDatabaseTransaction($orderContext);
			$transactionContext = new PayEngineCw_RecurringTransactionContext($dbTransaction, $orderContext);
			$transaction = $adapter->createTransaction($transactionContext);
			$dbTransaction->setTransactionObject($transaction);
			PayEngineCw_Util::getEntityManager()->persist($dbTransaction);
		}
		catch (Exception $e) {
			$errorMessage = __('Subscription Payment Failed with error:', 'woocommerce_payenginecw') . $e->getMessage();
			$payenginecw_recurring_process_failure = $errorMessage;
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order($order, $product_id);
			return;
		}
		try {
			$adapter->process($transaction);
			PayEngineCw_Util::getTransactionHandler()->persistTransactionObject($transaction);
			
			if (!$transaction->isAuthorized()) {
				$message = current($transaction->getErrorMessages());
				throw new Exception($message);
			}
			
			WC_Subscriptions_Manager::process_subscription_payments_on_order($order);
		}
		catch (Exception $e) {
			PayEngineCw_Util::getTransactionHandler()->persistTransactionObject($transaction);
			$errorMessage = __('Subscription Payment Failed with error:', 'woocommerce_payenginecw') . $e->getMessage();
			$payenginecw_recurring_process_failure = $errorMessage;
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order($order, $product_id);
		}
	}

	public function scheduledSubscriptionPaymentNew($amountToCharge, $order){
		global $payenginecw_recurring_process_failure;
		$payenginecw_recurring_process_failure = NULL;
		if(PayEngineCw_Util::getAuthorizedTransactionByPostId($order->id) != null){
			return;	
		}
		try {			
			$adapter = PayEngineCw_Util::getAuthorizationAdapter(
					Customweb_Payment_Authorization_Recurring_IAdapter::AUTHORIZATION_METHOD_NAME);
			
			$orderContext = new PayEngineCw_RecurringOrderContextNew($order, new PayEngineCw_PaymentMethodWrapper($this), 
					$amountToCharge);
			$order->update_status('wc-pend-'.substr(hash('sha1', 'payenginecw'), 0 , 10));
			$dbTransaction = $this->newDatabaseTransaction($orderContext);
			$transactionContext = new PayEngineCw_RecurringTransactionContext($dbTransaction, $orderContext);
			$transaction = $adapter->createTransaction($transactionContext);
			$dbTransaction->setTransactionObject($transaction);
			PayEngineCw_Util::getEntityManager()->persist($dbTransaction);
		}
		catch (Exception $e) {
			$errorMessage = __('Subscription Payment Failed with error:', 'woocommerce_payenginecw') . $e->getMessage();
			$payenginecw_recurring_process_failure = $errorMessage;
			$subscriptions = wcs_get_subscriptions_for_order($order->id, array(
				'order_type' => array(
					'parent',
					'renewal' 
				) 
			));
			foreach ($subscriptions as $subscription) {
				if (wcs_is_subscription($subscription->id)) {
					$subscription->payment_failed();
				}
			}
			return;
		}
		try {
			$adapter->process($transaction);
			if (!$transaction->isAuthorized()) {
				$message = current($transaction->getErrorMessages());
				throw new Exception($message);
			}
			PayEngineCw_Util::getTransactionHandler()->persistTransactionObject($transaction);
		}
		catch (Exception $e) {
			$errorMessage = __('Subscription Payment Failed with error:', 'woocommerce_payenginecw') . $e->getMessage();
			$payenginecw_recurring_process_failure = $errorMessage;
			$subscriptions = wcs_get_subscriptions_for_order($order->id, 
					array(
						'order_type' => array(
							'parent',
							'renewal' 
						) 
					));
			foreach ($subscriptions as $subscription) {
				if (wcs_is_subscription($subscription->id)) {
					$subscription->payment_failed();
				}
			}
			PayEngineCw_Util::getTransactionHandler()->persistTransactionObject($transaction);
		}
	}
	
	public function process_admin_options(){
		global $woocommerce_payenginecw_isProcesssing;
		if ($woocommerce_payenginecw_isProcesssing) {
			return true;
		}
		$woocommerce_payenginecw_isProcesssing = true;
		$result = parent::process_admin_options();
		if($result){
			//So WPML adds the title and description to the string translations
			apply_filters( 'woocommerce_settings_api_sanitized_fields_' . strtolower( str_replace( 'WC_Gateway_', '', $this->id ) ), $this->sanitized_fields );
		}
		return $result;
	}

	public function validate_file_field($key){
		$value = $this->get_option($key);
		$settingsArray = $this->getMethodSettings();
		$setting = $settingsArray[$key];
		
		$filename = get_class($this) . '_' . $key;
		$fieldName = 'woocommerce_' . get_class($this) . '_' . $key;
		$parsedBody = Customweb_Core_Http_ContextRequest::getInstance()->getParsedBody();
		
		if (isset($parsedBody[$fieldName . '_reset']) && $parsedBody[$fieldName . '_reset'] == 'reset') {
			return $setting['default'];
		}
		
		if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] != 0) {
			return $value;
		}
		$upload_dir = wp_upload_dir();
		$name = basename($_FILES[$fieldName]['name']);
		
		$fileExtension = pathinfo($name, PATHINFO_EXTENSION);
		if (!file_exists($upload_dir['basedir'] . '/woocommerce_payenginecw')) {
			$oldmask = umask(0);
			mkdir($upload_dir['basedir'] . '/woocommerce_payenginecw', 0777, true);
			umask($oldmask);
		}
		$allowedFileExtensions = $setting['allowedFileExtensions'];
		
		if (!empty($allowedFileExtensions) && !in_array($fileExtension, $allowedFileExtensions)) {
			woocommerce_payenginecw_admin_show_message(
					'Only the following file extensions are allowed for setting "' . $setting['title'] . '": ' . implode(', ', $allowedFileExtensions), 
					'error');
			return $value;
		}
		$targetPath = $upload_dir['basedir'] . '/woocommerce_payenginecw/' . $filename . '.' . $fileExtension;
		$rs = move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetPath);
		if ($rs) {
			chmod($targetPath, 0777);
			return array(
				'name' => $name,
				'path' => $targetPath 
			);
		}
		else {
			woocommerce_payenginecw_admin_show_message('Unable to upload file for setting "' . $setting['title'] . '".', 'error');
			return $value;
		}
	}

	public function generate_file_html($key, $data){
		$field = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title' => '',
			'disabled' => false,
			'class' => '',
			'css' => '',
			'placeholder' => '',
			'type' => 'file',
			'desc_tip' => false,
			'description' => '',
			'custom_attributes' => array() 
		);
		
		$data = wp_parse_args($data, $defaults);
		
		ob_start();
		?>
<tr valign="top">
	<th scope="row" class="titledesc"><label
		for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
					<?php echo $this->get_tooltip_html( $data ); ?>
				</th>
	<td class="forminp">
					<?php
		
		$value = $this->get_option($key);
		if (isset($value['name'])) {
			$filename = $value['name'];
		}
		else {
			
			$filename = $value;
		}
		echo __('Current File: ', 'woocommerce_payenginecw') . esc_attr($filename);
		?><br />
		<fieldset>
			<legend class="screen-reader-text">
				<span><?php echo wp_kses_post( $data['title'] ); ?></span>
			</legend>
			<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
		</fieldset> <input type="checkbox"
		name="<?php echo esc_attr( $field.'_reset' ); ?>" value="reset" /><?php echo __('Reset', 'woocommerce_payenginecw'); ?><br />
	</td>
</tr>
<?php
		return ob_get_clean();
	}

	protected function getOrderStatusOptions($statuses = array()){
		if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.0') >= 0) {
			$orderStatuses = wc_get_order_statuses();
			foreach ($statuses as $k => $value) {
				$orderStatuses[$k] = __($value, 'woocommerce_payenginecw');
			}
			return $orderStatuses;
		}
		else {
			return parent::getOrderStatusOptions($statuses);
		}
	}

	protected function getCompatibilityFormFields(){
		require_once (ABSPATH . 'wp-admin/includes/plugin.php');
		$extra = '';
		if (is_plugin_active('woocommerce-german-market/WooCommerce-German-Market.php')) {
			$extra .= '<div class="payenginecw-requires-second-run"></div>';
		}
		return $extra;
	}

	/**
	 * This method is called when the payment is submitted.
	 *
	 * @param int $order_id
	 */
	public function process_payment($order_id){
		global $woocommerce;
		
		$order = PayEngineCw_Util::loadOrderObjectById($order_id);
		
		// Bugfix to prevent the deletion of the cart, when the user goes back to the shop.
		if (isset($woocommerce)) {
			unset($woocommerce->session->order_awaiting_payment);
		}
		
		$order->add_order_note(
				__('The customer is now in the payment process of ConCardis.', 'woocommerce_payenginecw'));
		$order->update_status('wc-pend-'.substr(hash('sha1', 'payenginecw'), 0 , 10));
		
		$aliasTransactionId = $this->getCurrentSelectedAlias();
		if (is_ajax()) {
			try {
				$result = $this->processShopPayment($order_id, $aliasTransactionId);
				if (is_array($result)) {
					if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.4.0') >= 0 &&
							 function_exists('wp_send_json')) {
						wp_send_json($result);
					}
					else {
						echo '<!--WC_START-->' . json_encode($result) . '<!--WC_END-->';
					}
					
					die();
				}
				else {
					wp_send_json(
							array(
								'result' => 'success',
								'data' => $result .
										 "<script type=\"text/javascript\"> var backToCheckoutCw = jQuery('#payenginecw-back-to-checkout'); jQuery('form.checkout').replaceWith(jQuery('#payenginecw-payment-container')); jQuery('#payenginecw-payment-container').after(backToCheckoutCw); jQuery('.woocommerce-info').remove(); jQuery('.cw-external-checkouts').remove(); jQuery('html, body').animate({ scrollTop: (jQuery('#payenginecw-payment-container').offset().top-150) }, '1000');</script>" 
							));
					die();
				}
			}
			catch (Exception $e) {
				$this->showError($e->getMessage());
			}
		}
		else {
			wp_redirect(
					PayEngineCw_Util::getPluginUrl("payment", 
							array(
								'cwoid' => $order_id,
								'cwot' => PayEngineCw_Util::computeOrderValidationHash($order_id),
								'cwpmc' => get_class($this),
								'cwalias' => $aliasTransactionId 
							)));
			die();
		}
	}

	protected function destroyCheckoutId(){
		global $woocommerce;
		$sessionHandler = $woocommerce->session;
		if($sessionHandler != null){
			if(method_exists($sessionHandler, 'set')){
				$sessionHandler->set('PayEngineCwCheckoutId', null);
			}
			else{
				$sessionHandler->PayEngineCwCheckoutId = null;
			}
		}
	}
	
/**
	 * This method is invoked to check if the payment method is available for checkout.
	 */
	public function is_available(){
		global $woocommerce;
		
		$available = parent::is_available();
		
		if ($available !== true) {
			return false;
		}
		
		if (isset($woocommerce) && $woocommerce->cart != null) {
			if (isset($woocommerce->cart->disableValidationCw) && $woocommerce->cart->disableValidationCw) {
				return true;
			}
			if (!isset($woocommerce->cart->totalCalculatedCw)) {
				$woocommerce->cart->calculate_totals();
			}
			
			$orderTotal = $woocommerce->cart->total;
			if ($orderTotal < $this->getPaymentMethodConfigurationValue('min_total')) {
				return false;
			}
			if ($this->getPaymentMethodConfigurationValue('max_total') > 0 && $this->getPaymentMethodConfigurationValue('max_total') < $orderTotal) {
				return false;
			}
			
			$orderContext = $this->getCartOrderContext();
			if ($orderContext !== null) {
				$paymentContext = PayEngineCw_Util::getPaymentCustomerContext($orderContext->getCustomerId());
				
				$adapter = PayEngineCw_Util::getAuthorizationAdapterByContext($orderContext);
				$result = true;
				try {
					$adapter->preValidate($orderContext, $paymentContext);
				}
				catch (Exception $e) {
					$result = false;
				}
				PayEngineCw_Util::persistPaymentCustomerContext($paymentContext);
				return $result;
			}
		}
		return true;
	}
	
}
