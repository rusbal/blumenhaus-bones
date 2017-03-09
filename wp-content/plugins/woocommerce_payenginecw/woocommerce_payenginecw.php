<?php

/**
 * Plugin Name: WooCommerce PayEngineCw
 * Plugin URI: http://www.customweb.ch
 * Description: This plugin adds the PayEngineCw payment gateway to your WooCommerce.
 * Version: 4.0.87
 * Author: customweb GmbH
 * Author URI: http://www.customweb.ch
 */

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

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit();
}

// Load Language Files
load_plugin_textdomain('woocommerce_payenginecw', false, basename(dirname(__FILE__)) . '/translations');

require_once dirname(__FILE__) . '/lib/loader.php';
require_once 'classes/PayEngineCw/Util.php';
require_once 'PayEngineCw/TranslationResolver.php';

require_once 'PayEngineCw/ContextRequest.php';
require_once 'Customweb/Core/Exception/CastException.php';
require_once 'PayEngineCw/Util.php';
require_once 'PayEngineCw/ConfigurationAdapter.php';
require_once 'PayEngineCw/Cron.php';
require_once 'PayEngineCw/Entity/ExternalCheckoutContext.php';
require_once 'PayEngineCw/Dispatcher.php';
require_once 'Customweb/Payment/ExternalCheckout/IProviderService.php';
require_once 'Customweb/Util/Rand.php';
require_once 'Customweb/Payment/ExternalCheckout/IContext.php';



if (is_admin()) {
	// Get all admin functionality
	require_once PayEngineCw_Util::getBasePath() . '/admin.php';
}
/**
 * Register plugin activation hook
 */
register_activation_hook(__FILE__, array(
	'PayEngineCw_Util',
	'installPlugin' 
));

/**
 * Register plugin deactivation hook
 */
register_deactivation_hook(__FILE__, array(
	'PayEngineCw_Util',
	'uninstallPlugin' 
));

/**
 * Add the payment methods with a filter
 */
add_filter('woocommerce_payment_gateways', array(
	'PayEngineCw_Util',
	'addPaymentMethods' 
));

if (!is_admin()) {

	function woocommerce_payenginecw_add_frontend_css(){
		wp_register_style('woocommerce_payenginecw_frontend_styles', plugins_url('resources/css/frontend.css', __FILE__));
		wp_enqueue_style('woocommerce_payenginecw_frontend_styles');
		
		wp_register_script('payenginecw_frontend_script', plugins_url('resources/js/frontend.js', __FILE__), array(
			'jquery' 
		));
		wp_enqueue_script('payenginecw_frontend_script');
		wp_localize_script('payenginecw_frontend_script', 'woocommerce_payenginecw_ajax', 
				array(
					'ajax_url' => admin_url('admin-ajax.php') 
				));
	}
	add_action('wp_enqueue_scripts', 'woocommerce_payenginecw_add_frontend_css');
}

/**
 * Adds error message during checkout to the top of the page
 * WP action: wp_head
 */
function woocommerce_payenginecw_add_errors(){
	if (!function_exists('is_ajax') || is_ajax()) {
		return;
	}
	if (isset($_GET['payenginecwftid']) && isset($_GET['payenginecwftt'])) {
		$dbTransaction = PayEngineCw_Util::getTransactionById($_GET['payenginecwftid']);
		$validateHash = PayEngineCw_Util::computeTransactionValidateHash($dbTransaction);
		if ($validateHash == $_GET['payenginecwftt']) {
			woocommerce_payenginecw_add_message(current($dbTransaction->getTransactionObject()->getErrorMessages()));
		}
	}
	if (isset($_GET['payenginecwove'])) {
		woocommerce_payenginecw_add_message($_GET['payenginecwove']);
	}
	
	if (isset($_GET['old-context-id'])) {
		$context = PayEngineCw_Entity_ExternalCheckoutContext::getContextById($_GET['old-context-id']);
		if ($context->getState() == Customweb_Payment_ExternalCheckout_IContext::STATE_FAILED && $context->getCookieKey() == $_GET['verifyKey']) {
			woocommerce_payenginecw_add_message($context->getFailedErrorMessage());
		}
	}
	
}
add_action('wp_head', 'woocommerce_payenginecw_add_errors');

/**
 * Calls the function to add error message, depending on shop plugin version
 *
 * @param string $errorMessage
 */
function woocommerce_payenginecw_add_message($errorMessage){
	
	if (!function_exists('wc_add_notice')) {
		global $woocommerce;
		$woocommerce->add_error($errorMessage);
	}
	else {
		wc_add_notice($errorMessage, 'error');
	}
	

}

/**
 * Add new order status to shop system
 * Order status is WP Post Status
 * WP action: init
 * WP filter: wc_order_statuses
 */
function woocommerce_payenginecw_create_order_status(){
	$name = 'wc-pend-'.substr(hash('sha1', 'payenginecw'), 0 , 10); 

	register_post_status($name, 
			array(
				'label' => 'ConCardis pending',
				'public' => true,
				'exclude_from_search' => false,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('ConCardis pending <span class="count">(%s)</span>', 
						'ConCardis pending <span class="count">(%s)</span>') 
			));
//Keep old status, 
	$oldName = 'wc-'.substr(hash('sha1', 'payenginecw'), 0 , 10).'-pend'; 
	register_post_status($oldName, 
			array(
				'label' => 'ConCardis pending (Legacy)',
				'public' => false,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => false,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('ConCardis pending (Legacy) <span class="count">(%s)</span>', 
						'ConCardis pending (Legacy)<span class="count">(%s)</span>') 
			));
}

// Add to list of WC Order statuses
function woocommerce_payenginecw_add_order_status( $order_statuses ) {
	$name = 'wc-pend-'.substr(hash('sha1', 'payenginecw'), 0 , 10); 
	$oldName = 'wc-'.substr(hash('sha1', 'payenginecw'), 0 , 10).'-pend';
	
	$order_statuses[$name] = 'ConCardis pending';
	$order_statuses[$oldName] = 'ConCardis pending (Legacy)';
	return $order_statuses; 
}

if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.0') >= 0) {
	add_action('init', 'woocommerce_payenginecw_create_order_status');
	add_filter( 'wc_order_statuses', 'woocommerce_payenginecw_add_order_status' );
}


if(is_admin()){
	function woocommerce_payenginecw_orderstatus_css(){
		$orderStatusKey = 'pend-'.substr(hash('sha1', 'payenginecw'), 0 , 10);
		$orderStatusCss  = '.widefat .column-order_status mark.'.$orderStatusKey.':after {
			    content: "\e031";
	    		color: #ffba00;
				font-family: WooCommerce;
			    speak: none;
			    font-weight: 400;
			    font-variant: normal;
			    text-transform: none;
			    line-height: 1;
			    -webkit-font-smoothing: antialiased;
			    margin: 0;
			    text-indent: 0;
			    position: absolute;
			    top: 0;
			    left: 0;
			    width: 100%;
			    height: 100%;
			    text-align: center
		}';
		wp_add_inline_style('wp-admin', $orderStatusCss);
	}
	add_action('admin_enqueue_scripts', 'woocommerce_payenginecw_orderstatus_css');
}



/**
 * Add action to modify billing/shipping form during checkout
 */
add_action('woocommerce_before_checkout_billing_form', array(
	'PayEngineCw_Util',
	'actionBeforeCheckoutBillingForm' 
));
add_action('woocommerce_before_checkout_shipping_form', array(
	'PayEngineCw_Util',
	'actionBeforeCheckoutShippingForm' 
));

/**
 * Add Cron hooks and actions
 */
function createPayEngineCwCronInterval($schedules){
	$schedules['PayEngineCwCronInterval'] = array(
		'interval' => 120,
		'display' => __('PayEngineCw Interval', 'woocommerce_payenginecw') 
	);
	return $schedules;
}

function createPayEngineCwCron(){
	$timestamp = wp_next_scheduled('PayEngineCwCron');
	if ($timestamp == false) {
		wp_schedule_event(time() + 120, 'PayEngineCwCronInterval', 'PayEngineCwCron');
	}
}

function deletePayEngineCwCron(){
	wp_clear_scheduled_hook('PayEngineCwCron');
}

function runPayEngineCwCron(){
	PayEngineCw_Cron::run();
}

//Cron Functions to pull update
register_activation_hook(__FILE__, 'createPayEngineCwCron');
register_deactivation_hook(__FILE__, 'deletePayEngineCwCron');

add_filter('cron_schedules', 'createPayEngineCwCronInterval');
add_action('PayEngineCwCron', 'runPayEngineCwCron');

/**
 * Action to add payment information to order confirmation page, and email
 */
add_action('woocommerce_thankyou', array(
	'PayEngineCw_Util',
	'thankYouPageHtml' 
));
add_action('woocommerce_email_before_order_table', array(
	'PayEngineCw_Util',
	'orderEmailHtml' 
), 10, 3);


/**
 * Updates the payment fields of the payment methods
 * WP action: wp_ajax_woocommerce_payenginecw_update_payment_form
 * WP action: wp_ajax_nopriv_woocommerce_payenginecw_update_payment_form
 */
function woocommerce_payenginecw_ajax_update_payment_form(){
	if (!isset($_POST['payment_method'])) {
		die();
	}
	$length = strlen('PayEngineCw');
	if (substr($_POST['payment_method'], 0, $length) != 'PayEngineCw') {
		die();
	}
	try {
		$paymentMethod = PayEngineCw_Util::getPaymentMehtodInstance($_POST['payment_method']);
		$paymentMethod->payment_fields();
		die();
	}
	catch (Exception $e) {
		die();
	}
}
add_action('wp_ajax_woocommerce_payenginecw_update_payment_form', 'woocommerce_payenginecw_ajax_update_payment_form');
add_action('wp_ajax_nopriv_woocommerce_payenginecw_update_payment_form', 'woocommerce_payenginecw_ajax_update_payment_form');

/**
 * Form fields validation through ajax call -> prevents creating an order if validation fails
 * WP action: wp_ajax_woocommerce_payenginecw_validate_payment_form
 * WP action: wp_ajax_nopriv_woocommerce_payenginecw_validate_payment_form
 */
function woocommerce_payenginecw_validate_payment_form(){
	$result = array(
		'result' => 'failure',
		'message' => '<ul class="woocommerce-error"><li>' . __('Invalid Request', 'woocommercepayenginecw') .
		'</li></ul>'
	);
	if (!isset($_POST['payment_method'])) {
		echo json_encode($result);
		die();
	}
	$length = strlen('PayEngineCw');
	if (substr($_POST['payment_method'], 0, $length) != 'PayEngineCw') {
		echo json_encode($result);
		die();
	}
	try {
		if ( !defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}
		$paymentMethod = PayEngineCw_Util::getPaymentMehtodInstance($_POST['payment_method']);
		$paymentMethod->validate(PayEngineCw_ContextRequest::getInstance()->getParameters());
		$result = array(
			'result' => 'success');
		echo json_encode($result);
		die();
	}
	catch (Exception $e) {
		$result = array(
			'result' => 'failure',
			'message' => '<ul class="woocommerce-error"><li>' . $e->getMessage() .
			'</li></ul>'
		);
		echo json_encode($result);
		die();
	}
}
add_action('wp_ajax_woocommerce_payenginecw_validate_payment_form', 'woocommerce_payenginecw_validate_payment_form');
add_action('wp_ajax_nopriv_woocommerce_payenginecw_validate_payment_form', 'woocommerce_payenginecw_validate_payment_form');

//Fix to avoid multiple cart calculations
function woocommerce_payenginecw_before_calculate_totals($cart){
	$cart->disableValidationCw = true;
	return;
}
add_action('woocommerce_before_calculate_totals', 'woocommerce_payenginecw_before_calculate_totals');

//Fix to avoid multiple cart calculations
function woocommerce_payenginecw_after_calculate_totals($cart){
	$cart->totalCalculatedCw = true;
	$cart->disableValidationCw = false;
	return;
}
add_action('woocommerce_after_calculate_totals', 'woocommerce_payenginecw_after_calculate_totals');




/**
 * Returns the html required to display all external checkout widgets.
 */
function woocommerce_payenginecw_checkout_widgets_html(){
	if (!isset($GLOBALS['cwWooCommerceExternalCheckoutWidgetsHtml'])) {
		$output = '';
		if (!empty($GLOBALS['cwWooCommerceExternalCheckouts'])) {
			$output = '<div class="cw-external-checkouts">';
			foreach ($GLOBALS['cwWooCommerceExternalCheckouts'] as $checkout) {
				$output .= $checkout['widget'];
			}
			$output .= '</div>';
		}
		$GLOBALS['cwWooCommerceExternalCheckoutWidgetsHtml'] = $output;
	}
	return $GLOBALS['cwWooCommerceExternalCheckoutWidgetsHtml'];
}

/**
 * Adds the external checkouts to the cart page
 * WP Action: woocommerce_proceed_to_checkout
 */
function woocommerce_payenginecw_proceed_to_checkout(){
	if (PayEngineCw_ConfigurationAdapter::getExternalCheckoutPlacement() == 'both' ||
			 PayEngineCw_ConfigurationAdapter::getExternalCheckoutPlacement() == 'cart') {
		if (!isset($GLOBALS['cwWooCommerceExternalCheckoutDisplay'])) {
			$GLOBALS['cwWooCommerceExternalCheckoutDisplay'] = true;
			echo woocommerce_payenginecw_checkout_widgets_html();
		}
	}
}
add_action('woocommerce_proceed_to_checkout', 'woocommerce_payenginecw_proceed_to_checkout', 10);

/**
 * Adds the external checkouts to the checkout page
 * WP Action: woocommerce_before_checkout_form
 */
function woocommerce_payenginecw_before_checkout_form(){
	if (PayEngineCw_ConfigurationAdapter::getExternalCheckoutPlacement() == 'both' ||
			 PayEngineCw_ConfigurationAdapter::getExternalCheckoutPlacement() == 'checkout') {
		if (!isset($GLOBALS['cwWooCommerceExternalCheckoutDisplay'])) {
			$GLOBALS['cwWooCommerceExternalCheckoutDisplay'] = true;
			echo woocommerce_payenginecw_checkout_widgets_html();
		}
	}
}
add_action('woocommerce_before_checkout_form', 'woocommerce_payenginecw_before_checkout_form', 20);

/**
 * Adds the external checkouts to the checkout page
 * WP Action: woocommerce_payenginecw_checkout_widgets
 */
function woocommerce_payenginecw_checkout_widgets_custom(){
	if (PayEngineCw_ConfigurationAdapter::getExternalCheckoutPlacement() == 'custom') {
		if (!isset($GLOBALS['cwWooCommerceExternalCheckoutDisplay'])) {
			$GLOBALS['cwWooCommerceExternalCheckoutDisplay'] = true;
			echo woocommerce_payenginecw_checkout_widgets_html();
		}
	}
}
add_action('woocommerce_customweb_checkout_widgets', 'woocommerce_payenginecw_checkout_widgets_custom', 20);

/**
 * Clears the external checkout widget flag, so the action can be executed multiple times
 */
function woocommerce_payenginecw_checkout_widgets_clear(){
	unset($GLOBALS['cwWooCommerceExternalCheckoutDisplay']);
}
add_action('woocommerce_customweb_checkout_widgets', 'woocommerce_payenginecw_checkout_widgets_clear', 1000);
add_action('woocommerce_proceed_to_checkout', 'woocommerce_payenginecw_checkout_widgets_clear', 1000);
add_action('woocommerce_before_checkout_form', 'woocommerce_payenginecw_checkout_widgets_clear', 1000);

/**
 * collects all external checkout form different modules with filter cw_woocommerce_external_checkout_collection
 * WP action: get_header
 */
function woocommerce_payenginecw_create_checkouts(){
	if (is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART')) {
		if (!isset($GLOBALS['cwWooCommerceExternalCheckouts'])) {
			if (class_exists('WC_Subscriptions_Cart') && WC_Subscriptions_Cart::cart_contains_subscription()) {
				$GLOBALS['cwWooCommerceExternalCheckouts'] = array();
				return;
			}
			$checkouts = apply_filters('cw_woocommerce_external_checkout_collection', array());
			usort($checkouts, "woocommerce_payenginecw_sort_checkouts");
			$GLOBALS['cwWooCommerceExternalCheckouts'] = $checkouts;
		}
	}
}
add_action('get_header', 'woocommerce_payenginecw_create_checkouts');

/**
 * generates the external checkouts html code for the checkout of this modules
 * WP filter: cw_woocommerce_external_checkout_collection
 *
 * @param array $checkoutList
 * @throws Customweb_Core_Exception_CastException
 * @return array
 */
function woocommerce_payenginecw_checkout_collection($checkoutList = array()){
	$context = PayEngineCw_Entity_ExternalCheckoutContext::getReusableContextFromCookie();
	if ($context === null) {
		$context = new PayEngineCw_Entity_ExternalCheckoutContext();
		$context->setCookieKey(Customweb_Util_Rand::getUuid());
		PayEngineCw_Util::getEntityManager()->persist($context);
		setcookie('payenginecw-woocommerce-context-id', $context->getContextId());
		setcookie('payenginecw-woocommerce-context-key', $context->getCookieKey());
	}
	
	$context->updateFromCart(WC()->cart);
	$context->setState(Customweb_Payment_ExternalCheckout_IContext::STATE_PENDING);
	
	//Logged In
	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		$context->setCustomerId($user->ID);
	}
	
	$providerService = PayEngineCw_Util::createContainer()->getBean('Customweb_Payment_ExternalCheckout_IProviderService');
	if (!($providerService instanceof Customweb_Payment_ExternalCheckout_IProviderService)) {
		throw new Customweb_Core_Exception_CastException('Customweb_Payment_ExternalCheckout_IProviderService');
	}
	
	$checkouts = $providerService->getCheckouts($context);
	
	foreach ($checkouts as $checkout) {
		ob_start();
		PayEngineCw_Util::includeTemplateFile('cart_checkout', 
				array(
					'widgetCode' => $providerService->getWidgetHtml($checkout, $context),
					'checkoutName' => $checkout->getMachineName() 
				));
		$widgetHtml = ob_get_clean();
		$checkoutList['payenginecw_' . $checkout->getMachineName()] = array(
			'sortOrder' => $checkout->getSortOrder(),
			'widget' => $widgetHtml 
		);
	}
	PayEngineCw_Util::getEntityManager()->persist($context);
	return $checkoutList;
}
add_filter('cw_woocommerce_external_checkout_collection', 'woocommerce_payenginecw_checkout_collection');

/**
 * Sorts the available external checkouts
 */
function woocommerce_payenginecw_sort_checkouts($a, $b){
	if (isset($a['sortOrder']) && isset($b['sortOrder'])) {
		if ($a['sortOrder'] < $b['sortOrder']) {
			return -1;
		}
		else if ($a['sortOrder'] > $b['sortOrder']) {
			return 1;
		}
		else {
			return 0;
		}
	}
	else {
		return 0;
	}
}

/**
 * Returns the formatted cost from the give shipping method,
 * Uses shop settings to match the format
 *
 * @param unknown $method
 * @return string
 */
function woocommerce_payenginecw_format_shipping_amount_like_shop($method){
	$amount;
	if ($method->cost > 0) {
		if (WC()->cart->tax_display_cart == 'excl') {
			$amount = wc_price($method->cost);
			if ($method->get_shipping_tax() > 0 && WC()->cart->prices_include_tax) {
				$amount .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
		else {
			$amount = wc_price($method->cost + $method->get_shipping_tax());
			if ($method->get_shipping_tax() > 0 && !WC()->cart->prices_include_tax) {
				$amount .= ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		}
	}
	elseif ($method->id == 'free_shipping') {
		$amount = '(' . __('Free', 'woocommerce_payenginecw') . ')';
	}
	else {
		$amount = wc_price($method->cost);
	}
	return $amount;
}




//Fix to not send cancel subscription mail (if initial paymet fails)
function woocommerce_payenginecw_unhook_subscription_cancel($email){
	remove_action('cancelled_subscription_notification', array(
		$email->emails['WCS_Email_Cancelled_Subscription'],
		'trigger' 
	));
}




/**
 * Avoid redirects if our page is called, fixes some problem introduced by other plugins
 * WP filter: redirect_canonical
 *
 * @param string $redirectUrl
 * @param string $requestUrl
 * @return false|string
 */
function woocommerce_payenginecw_redirect_canonical($redirectUrl, $requestUrl){
	if (woocommerce_payenginecw_is_plugin_page()) {
		return false;
	}
	return $redirectUrl;
}
add_filter('redirect_canonical', 'woocommerce_payenginecw_redirect_canonical', 10, 2);

/**
 * Removes our page/post, from appearing in breadcrumbs or navigation
 * WP filter: get_pages
 *
 * @param array $pages
 * @return array
 */
function woocommerce_payenginecw_get_pages($pages){
	$pageFound = -1;
	$pageId = get_option('woocommerce_payenginecw_page');
	
	foreach ($pages as $key => $post) {
		$postId = $post->ID;
		if ($postId == $pageId) {
			$pageFound = $key;
			break;
		}
	}
	if ($pageFound != -1) {
		unset($pages[$pageFound]);
	}
	return $pages;
}
add_filter('get_pages', 'woocommerce_payenginecw_get_pages', 10, 2);

/**
 * Replaces our shortcode string with the actual content
 * WP shortcode: woocommerce_payenginecw
 */
function woocommerce_payenginecw_shortcode_handling(){
	if (isset($GLOBALS['woo_payenginecwContent'])) {
		return $GLOBALS['woo_payenginecwContent'];
	}
}
add_shortcode('woocommerce_payenginecw', 'woocommerce_payenginecw_shortcode_handling');

/**
 * Initialies our context request, before wordpress messes up the parameters with it's magic quotes functions
 * WP action: plugins_loaded
 */
function woocommerce_payenginecw_loaded(){
	PayEngineCw_ContextRequest::getInstance();
}
add_action('plugins_loaded', 'woocommerce_payenginecw_loaded', 10);

/**
 * Filter for the get_locale function, this is activated before authorizing a transaction.
 * 
 * 
 * WP Filter : locale
 */
function woocommerce_payenginecw_locale($locale){
	if(isset($GLOBALS['woo_payenginecwAuthorizeLanguage'])){
		return $GLOBALS['woo_payenginecwAuthorizeLanguage'];
	}
	return $locale;
}

/**
 * Generates our content, handles request to our enpoint,
 * writes possible content to $GLOBALS['woo_payenginecwContent']
 * sets default title for our pages in $GLOBALS['woo_payenginecwTitle']
 * WP action: wp_loaded -> most of wordpress is loaded and headers are not yet sent
 */
function woocommerce_payenginecw_init(){
	if (woocommerce_payenginecw_is_plugin_page()) {
		
		//If we have WPML language parameter, force Wordpress language
		global $sitepress;
		if (isset($sitepress) && isset($_REQUEST['wpml-lang'])) {
			$sitepress->switch_lang($_REQUEST['wpml-lang'], false);
		}
		$dispatcher = new PayEngineCw_Dispatcher();
		$GLOBALS['woo_payenginecwTitle'] = __('Payment', 'woocommerce_payenginecw');
		try {
			$result = $dispatcher->dispatch();
		}
		catch (Exception $e) {
			$result = '<strong>' . $e->getMessage() . '</strong> <br />';
		}
		$GLOBALS['woo_payenginecwContent'] = $result;
	}
}
add_action('wp_loaded', 'woocommerce_payenginecw_init', 50);

/**
 * Echos additional JS and CSS file urls during the html head generation
 * WP action: wp_head -> is triggered while wordpress is echoing the html head
 */
function woocommerce_payenginecw_additional_files_header(){
	if (isset($GLOBALS['woo_payenginecwCSS'])) {
		echo $GLOBALS['woo_payenginecwCSS'];
	}
	if (isset($GLOBALS['woo_payenginecwJS'])) {
		echo $GLOBALS['woo_payenginecwJS'];
	}
}
add_action('wp_head', 'woocommerce_payenginecw_additional_files_header');

/**
 * Replaces the title of our page, if it is set in $GLOBALS['woo_payenginecwTitle']
 * WP filter: the_title
 *
 * @param string $title
 * @param int $id
 * @return string
 */
function woocommerce_payenginecw_get_page_title($title, $id = null){
	if(woocommerce_payenginecw_check_pageid($id)){
		if (isset($GLOBALS['woo_payenginecwTitle'])) {
			return $GLOBALS['woo_payenginecwTitle'];
		}
	}
	return $title;
}
add_filter('the_title', 'woocommerce_payenginecw_get_page_title', 10, 2);

/**
 * Never do unforce SSL redirect on our page
 * WP Filter : woocommerce_unforce_ssl_checkout
 */
function woocommerce_payenginecw_unforce_ssl_checkout($unforce){
	if (woocommerce_payenginecw_is_plugin_page()) {
		return false;
	}
	return $unforce;
}
add_filter('woocommerce_unforce_ssl_checkout', 'woocommerce_payenginecw_unforce_ssl_checkout', 10, 2);

/**
 * Remove get variables to avoid wordpress redirecting to 404,
 * if our page is called and
 * WP Filter : request
 */
function woocommerce_payenginecw_alter_the_query($request){
	if (woocommerce_payenginecw_is_plugin_page()) {
		unset($request['year']);
		unset($request['day']);
		unset($request['w']);
		unset($request['m']);
		unset($request['name']);
		unset($request['hour']);
		unset($request['minute']);
		unset($request['second']);
		unset($request['order']);
		unset($request['term']);
		unset($request['error']);
	}
	return $request;
}
add_filter('request', 'woocommerce_payenginecw_alter_the_query');


/**
 * We define our sites as checkout, so we are not unforced from SSL
 *
 * @param boolean $isCheckout
 * @return boolean
 */
function woocommerce_payenginecw_is_checkout($isCheckout){
	
	// We need to be in checkout, to calculate the complete order total
	if (isset($GLOBALS['cwExternalCheckoutOrderTotal']) && $GLOBALS['cwExternalCheckoutOrderTotal']) {
		return true;
	}
	
	if (woocommerce_payenginecw_is_plugin_page()) {
		return true;
	}
	return $isCheckout;
}
add_filter('woocommerce_is_checkout', 'woocommerce_payenginecw_is_checkout', 10, 2);

/**
 * This function returns true if the page id ($pid) belongs to the plugin.
 *
 * @param integer|null $pid
 * @return boolean
 */
function woocommerce_payenginecw_check_pageid($pid){
	if ($pid == get_option('woocommerce_payenginecw_page')) {
		return true;
	}
	if (defined('ICL_SITEPRESS_VERSION')) {
		$meta = get_post_meta($pid, '_icl_lang_duplicate_of', true);
		if ($meta != '' && $meta == get_option('woocommerce_payenginecw_page')) {
			return true;
		}
	}
	return false;
}

/**
 * This function returns true if the page id ($pid) belongs to the plugin page endpoint.
 * If no page id is provided, the function determines it with the
 * woocommerce_payenginecw_get_page_id function
 *
 * @param integer|null $pid
 * @return boolean
 */
function woocommerce_payenginecw_is_plugin_page(){
	if (is_admin()) {
		return false;
	}
	if ( function_exists( 'ux_builder_is_active' ) && ux_builder_is_active() ) {
		//UX Builder compatibility
		return false;
	}
	$pid = woocommerce_payenginecw_get_page_id();
	if ($pid == get_option('woocommerce_payenginecw_page')) {
		return true;
	}
	if (defined('ICL_SITEPRESS_VERSION')) {
		$meta = get_post_meta($pid, '_icl_lang_duplicate_of', true);
		if ($meta != '' && $meta == get_option('woocommerce_payenginecw_page')) {
			return true;
		}
	}
	return false;
}

/**
 * Returns the current page id,
 * Uses the wordpress function url_to_postid
 *
 * @return number
 */
function woocommerce_payenginecw_get_page_id(){
	/*
	 * WPML (Version < 3.3) has problems with calling ur_to_postid during this stage.
	 * It looks like Version 3.5 introduced the issue again.
	 * We remove their filter for our call and re add it afterwards.
	 * We need to backup and restore the registred filters. (WPML Versions 3.2)
	 *
	 * WPML adds an filter to 'option_rewrite_rules', when calling wp_rewrite_rules inside of
	 * url_to_postid. With this filter the following calls to wp_rewrite_rules return a wrong result. This leads to Page not found errors, when
	 * permalink setting for product base is a custom value.
	 * Therefore we restore the filters after we call url_to_post_id at this point. (older WPML Versions)
	 */
	$pid = 0;
	if (defined('ICL_SITEPRESS_VERSION')) {
		if (version_compare(ICL_SITEPRESS_VERSION, '3.2') < 0) {
			$backup = $GLOBALS['wp_filter'];
			$pid = url_to_postid($_SERVER['REQUEST_URI']);
			$GLOBALS['wp_filter'] = $backup;
		}
		elseif (version_compare(ICL_SITEPRESS_VERSION, '3.3') < 0 || version_compare(ICL_SITEPRESS_VERSION, '3.5') > 0) {
			global $sitepress;
			$removedFilter = false;
			if (isset($sitepress) && has_filter('url_to_postid', array(
				$sitepress,
				'url_to_postid' 
			))) {
				remove_filter('url_to_postid', array(
					$sitepress,
					'url_to_postid' 
				));
				$removedFilter = true;
			}
			$pid = url_to_postid($_SERVER['REQUEST_URI']);
			if ($removedFilter) {
				add_filter('url_to_postid', array(
					$sitepress,
					'url_to_postid' 
				));
			}
		}
		else {
			$pid = url_to_postid($_SERVER['REQUEST_URI']);
		}
	}
	else {
		$pid = url_to_postid($_SERVER['REQUEST_URI']);
	}
	return $pid;
}

/**
 * Disable product state check for subscription during the processing of the transaction
 * 
 * @param boolean $state
 * @param Object $product
 * @return boolean
 */
function woocommerce_payenginecw_is_subscription_purchasable($state, $product){
	if(woocommerce_payenginecw_is_plugin_page()){
		return true;
	}
	return $state;	
}

add_filter('woocommerce_subscription_is_purchasable', 'woocommerce_payenginecw_is_subscription_purchasable', 10, 2);