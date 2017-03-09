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
require_once 'PayEngineCw/AbstractConfigurationAdapter.php';


/**
 * @Bean
 */
class PayEngineCw_ConfigurationAdapter extends PayEngineCw_AbstractConfigurationAdapter {

	public static function isReviewFormInputActive(){
		$value = get_option('woocommerce_payenginecw_review_input_form', 'active');
		if ($value == 'active') {
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function getExternalCheckoutPlacement(){
		return get_option('woocommerce_payenginecw_external_checkout_placement', 'both');
	}
	
	public static function getExternalCheckoutAccountCreation(){
		return get_option('woocommerce_payenginecw_external_checkout_account_creation', 'skip_selection');
	}
	
	public static function getOrderNumberIdentifier(){
		return get_option('woocommerce_payenginecw_order_identifier', 'ordernumber');
	}

	public function getLanguages($currentLanguages = false){
		return null;
	}

	public function getStoreHierarchy(){
		return null;
	}

	public function useDefaultValue(Customweb_Form_IElement $element, array $formData){
		return false;
	}

	public function getOrderStatus(){
		if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.0') >= 0) {
	return wc_get_order_statuses();
}
else {
	$terms = get_terms('shop_order_status', array(
		'hide_empty' => 0,
		'orderby' => 'id' 
	));
	$statuses = array();
	foreach ($terms as $term) {
		$statuses[$term->slug] = $term->name;
	}
	return $statuses;
	
}
		
	}
}