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

class PayEngineCw_OpenInvoice extends PayEngineCw_PaymentMethod
{
	public $machineName = 'openinvoice';
	public $admin_title = 'Open Invoice';
	public $title = 'Open Invoice';
	
	protected function getMethodSettings(){
		return array(
			'processor' => array(
				'title' => __("Processor", 'woocommerce_payenginecw'),
 				'default' => 'billpay',
 				'description' => __("Select the processor for open invoice.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'afterpay' => __("AfterPay", 'woocommerce_payenginecw'),
 					'billpay' => __("Billpay", 'woocommerce_payenginecw'),
 					'PostFinanceFIS' => __("PostFinance FIS", 'woocommerce_payenginecw'),
 					'klarna' => __("Klarna", 'woocommerce_payenginecw'),
 					'ratepay' => __("RatePay", 'woocommerce_payenginecw'),
 				),
 			),
 			'brand_country' => array(
				'title' => __("Brand Country", 'woocommerce_payenginecw'),
 				'default' => 'de',
 				'description' => __("Select the country code defined in the backend of ConCardis for this payment method.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'at' => __("Austria (AT)", 'woocommerce_payenginecw'),
 					'ch' => __("Switzerland (CH)", 'woocommerce_payenginecw'),
 					'de' => __("Germany (DE)", 'woocommerce_payenginecw'),
 					'dk' => __("Denmark (DK)", 'woocommerce_payenginecw'),
 					'fi' => __("Finland (FI)", 'woocommerce_payenginecw'),
 					'nl' => __("Netherlands (NL)", 'woocommerce_payenginecw'),
 					'no' => __("Norway (NO)", 'woocommerce_payenginecw'),
 					'se' => __("Sweden (SE)", 'woocommerce_payenginecw'),
 				),
 			),
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
 			'authorizationMethod' => array(
				'title' => __("Authorization Method", 'woocommerce_payenginecw'),
 				'default' => 'PaymentPage',
 				'description' => __("Select the authorization method to use for processing this payment method.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'PaymentPage' => __("Payment Page", 'woocommerce_payenginecw'),
 				),
 			),
 		); 
	}
	
	public function __construct() {
		$this->icon = apply_filters(
			'woocommerce_payenginecw_openinvoice_icon', 
			PayEngineCw_Util::getResourcesUrl('icons/openinvoice.png')
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