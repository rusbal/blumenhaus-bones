<?php

/**
 * You are allowed to use this API in your web application.
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

require_once dirname(dirname(__FILE__)) . '/classes/PayEngineCw/PaymentMethod.php'; 

class PayEngineCw_CreditCard extends PayEngineCw_PaymentMethod
{
	public $machineName = 'creditcard';
	public $admin_title = 'Credit Card';
	public $title = 'Credit Card';
	
	protected function getMethodSettings(){
		return array(
			'capturing' => array(
				'title' => __("Capturing", 'woocommerce_payenginecw'),
 				'default' => 'direct',
 				'description' => __("Should the amount be captured automatically after the order (direct) or should the amount only be reserved (deferred)?", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'direct' => __("Directly after order", 'woocommerce_payenginecw'),
 					'deferred' => __("Deferred", 'woocommerce_payenginecw'),
 				),
 			),
 			'payment_method_listing' => array(
				'title' => __("Payment Method Listing", 'woocommerce_payenginecw'),
 				'default' => '2',
 				'description' => __("In case the payment page is used with this payment method the different credit card brands are listed on the payment page of ConCardis. This setting controlls the display of them.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'0' => __("Horizontally grouped logos with the name on the left side", 'woocommerce_payenginecw'),
 					'1' => __("Horizontally grouped logos with no group names", 'woocommerce_payenginecw'),
 					'2' => __("Vertical list of logos with the name on the left side", 'woocommerce_payenginecw'),
 				),
 			),
 			'credit_card_brands' => array(
				'title' => __("Credit Card Brands", 'woocommerce_payenginecw'),
 				'default' => array(
					0 => 'visa',
 					1 => 'mastercard',
 					2 => 'americanexpress',
 					3 => 'diners',
 					4 => 'jcb',
 					5 => 'maestro',
 					6 => 'cartebleu',
 					7 => 'solo',
 				),
 				'description' => __("In case hidden authorization is used, the brand of the credit card is detected by the card number and the card number is validated accordingly. This setting enables the restriction of the allowed brands.", 'woocommerce_payenginecw'),
 				'cwType' => 'multiselect',
 				'type' => 'multiselect',
 				'options' => array(
					'visa' => __("VISA", 'woocommerce_payenginecw'),
 					'mastercard' => __("MasterCard", 'woocommerce_payenginecw'),
 					'americanexpress' => __("American Express", 'woocommerce_payenginecw'),
 					'diners' => __("Diners Club", 'woocommerce_payenginecw'),
 					'jcb' => __("JCB", 'woocommerce_payenginecw'),
 					'maestro' => __("Maestro", 'woocommerce_payenginecw'),
 					'cartebleu' => __("Carte Bleue", 'woocommerce_payenginecw'),
 					'solo' => __("Solo Card", 'woocommerce_payenginecw'),
 				),
 			),
 			'status_authorized' => array(
				'title' => __("Authorized Status", 'woocommerce_payenginecw'),
 				'default' => (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.0') >= 0) ? 'wc-processing' : 'processing',
 				'description' => __("This status is set, when the payment was successfull and it is authorized.", 'woocommerce_payenginecw'),
 				'cwType' => 'orderstatusselect',
 				'type' => 'select',
 				'options' => array(
					'use-default' => __("Use WooCommerce rules", 'woocommerce_payenginecw'),
 				),
 				'is_order_status' => true,
 			),
 			'status_uncertain' => array(
				'title' => __("Uncertain Status", 'woocommerce_payenginecw'),
 				'default' => (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.0') >= 0) ? 'wc-on-hold' : 'on-hold',
 				'description' => __("You can specify the order status for new orders that have an uncertain authorisation status.", 'woocommerce_payenginecw'),
 				'cwType' => 'orderstatusselect',
 				'type' => 'select',
 				'options' => array(
				),
 				'is_order_status' => true,
 			),
 			'status_cancelled' => array(
				'title' => __("Cancelled Status", 'woocommerce_payenginecw'),
 				'default' => (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.0') >= 0) ? 'wc-cancelled' : 'cancelled',
 				'description' => __("You can specify the order status when an order is cancelled.", 'woocommerce_payenginecw'),
 				'cwType' => 'orderstatusselect',
 				'type' => 'select',
 				'options' => array(
					'no_status_change' => __("Don't change order status", 'woocommerce_payenginecw'),
 				),
 				'is_order_status' => true,
 			),
 			'status_captured' => array(
				'title' => __("Captured Status", 'woocommerce_payenginecw'),
 				'default' => 'no_status_change',
 				'description' => __("You can specify the order status for orders that are captured either directly after the order or manually in the backend.", 'woocommerce_payenginecw'),
 				'cwType' => 'orderstatusselect',
 				'type' => 'select',
 				'options' => array(
					'no_status_change' => __("Don't change order status", 'woocommerce_payenginecw'),
 				),
 				'is_order_status' => true,
 			),
 			'status_success_after_uncertain' => array(
				'title' => __("HTTP Status for Successful Payments", 'woocommerce_payenginecw'),
 				'default' => 'no_status_change',
 				'description' => __("You can specify the order status for orders that are successful after being in a uncertain state. In order to use this setting, you will need to activate the http-request for status changes as outlined in the manual.", 'woocommerce_payenginecw'),
 				'cwType' => 'orderstatusselect',
 				'type' => 'select',
 				'options' => array(
					'no_status_change' => __("Don't change order status", 'woocommerce_payenginecw'),
 				),
 				'is_order_status' => true,
 			),
 			'status_refused_after_uncertain' => array(
				'title' => __("HTTP Status for Refused Payments", 'woocommerce_payenginecw'),
 				'default' => 'no_status_change',
 				'description' => __("You can specify the order status for orders that are refused after being in a uncertain state. In order to use this feature you will have to set up the http request for status changes as outlined in the manual.", 'woocommerce_payenginecw'),
 				'cwType' => 'orderstatusselect',
 				'type' => 'select',
 				'options' => array(
					'no_status_change' => __("Don't change order status", 'woocommerce_payenginecw'),
 				),
 				'is_order_status' => true,
 			),
 			'refusing_threshold' => array(
				'title' => __("Refused Transaction Threshold", 'woocommerce_payenginecw'),
 				'default' => '3',
 				'description' => __("A typical pattern of a fraud transaction is a series of refused transaction before one of them is accepted. This setting defines the threshold after any following transaction is marked as uncertain. E.g. a threshold of three will mark any successful transaction after three refused transaction as uncertain.", 'woocommerce_payenginecw'),
 				'cwType' => 'textfield',
 				'type' => 'text',
 			),
 			'three_d_secure_behavior' => array(
				'title' => __("Non 3D Secure Behavior", 'woocommerce_payenginecw'),
 				'default' => 'never',
 				'description' => __("Some cards are not enrolled for the 3D secure process and you may exclude some cards from performing a 3D secure authorization. This setting controls, what should happend with these transactions. This setting requires that the parameter 'CCCTY' is returned.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'always' => __("Mark all transactions as uncertain, except those from countries listed below.", 'woocommerce_payenginecw'),
 					'never' => __("Do never mark the transaction as uncertain, except those from countries listed below.", 'woocommerce_payenginecw'),
 				),
 			),
 			'three_d_secure_country_list' => array(
				'title' => __("Non 3D Secure Behavior Country List", 'woocommerce_payenginecw'),
 				'default' => '',
 				'description' => __("The countries listed in this field are used to mark transaction as uncertain. The behavior depends on the setting above 'Non 3D Secure Behavior'. The country code is taken from the card. You need to make sure you return the parameter 'CCCTY'. As indicated by ConCardis this value is only for 94% of the transaction correct. The list below must be a comma separated list of country codes. The codes must be in ISO 3166-2 format (e.g. DE,IT,FR).", 'woocommerce_payenginecw'),
 				'cwType' => 'textfield',
 				'type' => 'text',
 			),
 			'country_check' => array(
				'title' => __("Country Check", 'woocommerce_payenginecw'),
 				'default' => 'inactive',
 				'description' => __("The module can perform a check of the country code provided by the issuer of the card, the IP address country and the billing address country. In case they do not match, the transaction is marked as uncertain. This setting does not override any other rule for marking transaction as uncertain.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'inactive' => __("Inactive", 'woocommerce_payenginecw'),
 					'all' => __("All country codes must match.", 'woocommerce_payenginecw'),
 					'ip_country_code_issuer_code' => __("IP country code and issuer country code must match.", 'woocommerce_payenginecw'),
 					'ip_country_code_billing_code' => __("IP country and billing country code must match.", 'woocommerce_payenginecw'),
 					'issuer_code_billing_code' => __("Issuer country code and billing country code.", 'woocommerce_payenginecw'),
 				),
 			),
 			'authorizationMethod' => array(
				'title' => __("Authorization Method", 'woocommerce_payenginecw'),
 				'default' => 'PaymentPage',
 				'description' => __("Select the authorization method to use for processing this payment method.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'PaymentPage' => __("Payment Page", 'woocommerce_payenginecw'),
 					'HiddenAuthorization' => __("Hidden Authorization (Alias Gateway)", 'woocommerce_payenginecw'),
 					'AjaxAuthorization' => __("Ajax Authorization (Flex Checkout)", 'woocommerce_payenginecw'),
 				),
 			),
 			'alias_manager' => array(
				'title' => __("Alias Manager", 'woocommerce_payenginecw'),
 				'default' => 'inactive',
 				'description' => __("The alias manager allows the customer to select from a credit card previously stored. The sensitive data is stored by ConCardis.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'active' => __("Active", 'woocommerce_payenginecw'),
 					'inactive' => __("Inactive", 'woocommerce_payenginecw'),
 				),
 			),
 		); 
	}
	
	public function __construct() {
		$this->icon = apply_filters(
			'woocommerce_payenginecw_creditcard_icon', 
			PayEngineCw_Util::getResourcesUrl('icons/creditcard.png')
		);
		parent::__construct();
	}
	
	public function createMethodFormFields() {
		$formFields = parent::createMethodFormFields();
		
		return array_merge(
			$formFields,
			$this->getMethodSettings()
		);
	}

}