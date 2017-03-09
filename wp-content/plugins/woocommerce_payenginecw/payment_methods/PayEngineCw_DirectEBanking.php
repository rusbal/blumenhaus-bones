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

class PayEngineCw_DirectEBanking extends PayEngineCw_PaymentMethod
{
	public $machineName = 'directebanking';
	public $admin_title = 'Direct E-Banking';
	public $title = 'Direct E-Banking';
	
	protected function getMethodSettings(){
		return array(
			'brand_country' => array(
				'title' => __("Brand Country", 'woocommerce_payenginecw'),
 				'default' => 'de',
 				'description' => __("Select the country code defined in the backend of ConCardis for this payment method.", 'woocommerce_payenginecw'),
 				'cwType' => 'select',
 				'type' => 'select',
 				'options' => array(
					'at' => __("Austria (AT)", 'woocommerce_payenginecw'),
 					'be' => __("Belgium (BE)", 'woocommerce_payenginecw'),
 					'ch' => __("Switzerland (CH)", 'woocommerce_payenginecw'),
 					'no_code' => __("Germany (no code)", 'woocommerce_payenginecw'),
 					'de' => __("Germany (DE)", 'woocommerce_payenginecw'),
 					'fr' => __("France (FR)", 'woocommerce_payenginecw'),
 					'gb' => __("Great Britain (GB)", 'woocommerce_payenginecw'),
 					'it' => __("Italy (IT)", 'woocommerce_payenginecw'),
 					'nl' => __("Netherlands (NL)", 'woocommerce_payenginecw'),
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
			'woocommerce_payenginecw_directebanking_icon', 
			PayEngineCw_Util::getResourcesUrl('icons/directebanking.png')
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