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

require_once 'PayEngineCw/Util.php';
require_once 'PayEngineCw/ConfigurationAdapter.php';
require_once 'Customweb/Payment/Authorization/DefaultInvoiceItem.php';
require_once 'Customweb/Core/Language.php';
require_once 'Customweb/Payment/Authorization/OrderContext/AbstractDeprecated.php';


abstract class PayEngineCw_AbstractOrderContext extends  Customweb_Payment_Authorization_OrderContext_AbstractDeprecated
{
	protected $order;
	protected $orderAmount;
	protected $currencyCode;
	protected $paymentMethod;
	protected $language;
	protected $userId;
	
	protected $checkoutId;
	
	public function __construct($order, Customweb_Payment_Authorization_IPaymentMethod $paymentMethod) {
		
		if ($order == null) {
			throw new Exception("The order parameter cannot be null.");
		}
		
	
		if(method_exists($order, 'get_order_currency')){
			$this->currencyCode = $order->get_order_currency();
		}
		elseif (function_exists('get_woocommerce_currency')) {
			$this->currencyCode = get_woocommerce_currency();
		}
		else {
			$this->currencyCode = PayEngineCw_Util::getShopOption('woocommerce_currency');
		}
		$this->order = $order;
		$this->paymentMethod = $paymentMethod;
		if(method_exists($order, 'get_total')){
			$this->orderAmount = $order->get_total();
		}
		else if (method_exists($order, 'get_order_total')) {
			$this->orderAmount = $order->get_order_total();
		}
		else {
			$this->orderAmount = $order->order_total;
		}
		
		$this->language = get_bloginfo('language');
		
		if (isset($order->customer_user)) {
			$userId = $order->customer_user;
		}
		else {
			$userId = $order->user_id;
		}

		if ($userId === null) {
			$this->userId = get_current_user_id();
		}
		else {
			$this->userId = $userId;
		}
		
		if ($this->userId === null) {
			$this->userId = 0;
		}
	}
	
	public function getCustomerId() {
		return $this->userId;
	}
	
	public function isNewCustomer() {
		return 'unkown';
	}
	
	public function getCustomerRegistrationDate() {
		return null;
	}
	
	public function getOrderObject() {
		return $this->order;
	}
	
	public function getOrderAmountInDecimals() {
		return $this->orderAmount;
	}
	
	public function getCurrencyCode() {
		return $this->currencyCode;
	}
	
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}
	
	public function getLanguage() {
		return new Customweb_Core_Language($this->language);
	}
	
	public function getCustomerEMailAddress() {
		return $this->getBillingEMailAddress();
	}
	
	public function getBillingEMailAddress() {
		return $this->order->billing_email;
	}
	
	public function getBillingGender() {
		$billingCompany = trim($this->getBillingCompanyName());
		if (!empty($billingCompany)) {
			return 'company';
		}
		else {
			return null;
		}
	}
	
	public function getBillingSalutation() {
		return null;
	}
	
	public function getBillingFirstName() {
		return $this->order->billing_first_name;
	}
	
	public function getBillingLastName() {
		return $this->order->billing_last_name;
	}
	
	public function getBillingStreet() {
		$second =  $this->order->billing_address_2;
		if(empty($second)){
			return $this->order->billing_address_1;
		}		
		return $this->order->billing_address_1 . " " . $second;
	}
	
	public function getBillingCity() {
		return $this->order->billing_city;
	}
	
	public function getBillingPostCode() {
		return $this->order->billing_postcode;
	}
	
	public function getBillingState() {
		if (isset($this->order->billing_state)) {
			$state = $this->order->billing_state;
			if (!empty($state) && strlen($state) == 2) {
				return $state;
			}
		}
		
		return null;
	}
	
	public function getBillingCountryIsoCode() {
		return $this->order->billing_country;
	}
	
	public function getBillingPhoneNumber() {
		if($this->order->billing_phone != null && $this->order->billing_phone != '') {
			return $this->order->billing_phone;
		}
		return null;
	}
	
	public function getBillingMobilePhoneNumber() {
		return null;
	}
	
	public function getBillingDateOfBirth() {
		return null;
	}
	
	public function getBillingCompanyName() {
		return $this->order->billing_company;
	}
	
	public function getBillingCommercialRegisterNumber() {
		return null;
	}
	
	public function getBillingSalesTaxNumber() {
		return null;
	}
	
	public function getBillingSocialSecurityNumber() {
		return null;
	}
	
	public function getShippingEMailAddress() {
		$shippingEmail = $this->order->shipping_email;
		if (!empty($shippingEmail)) {
			return $shippingEmail;
		}
		else {
			return $this->getBillingEMailAddress();
		}
	}
	
	public function getShippingGender() {
		$company = trim($this->getShippingCompanyName());
		if (!empty($company)) {
			return 'company';
		}
		else {
			return null;
		}
	}
	
	public function getShippingSalutation() {
		return null;
	}
	
	public function getShippingFirstName() {
		return $this->order->shipping_first_name;
	}
	
	public function getShippingLastName() {
		return $this->order->shipping_last_name;
	}
	
	public function getShippingStreet() {
		$second = $this->order->shipping_address_2;
		if(empty($second)){
			return $this->order->shipping_address_1;
		}
		return $this->order->shipping_address_1 . " " . $second;
	}
	
	public function getShippingCity() {
		return $this->order->shipping_city;
	}
	
	public function getShippingPostCode() {
		return $this->order->shipping_postcode;
	}
	
	public function getShippingState() {
		if (isset($this->order->shipping_state)) {
			$state = $this->order->shipping_state;
			if (!empty($state) && strlen($state) == 2) {
				return $state;
			}
		}
		
		return null;
	}
	
	public function getShippingCountryIsoCode() {
		return $this->order->shipping_country;
	}
	
	public function getShippingPhoneNumber() {
		if($this->order->shipping_phone != null && $this->order->shipping_phone != '') {
			return $this->order->shipping_phone;
		}
		return null;
	}
	
	public function getShippingMobilePhoneNumber() {
		return null;
	}
	
	public function getShippingDateOfBirth() {
		return null;
	}
	
	public function getShippingCompanyName() {
		if($this->order->shipping_company != null && $this->order->shipping_company = ''){
			return $this->order->shipping_company;
		}
		return null;
	}
	
	public function getShippingCommercialRegisterNumber() {
		return null;
	}
	
	public function getShippingSalesTaxNumber() {
		return null;
	}
	
	public function getShippingSocialSecurityNumber() {
		return null;
	}
	
	public function getOrderParameters() {
		return array();
	}
	
	protected function getLineTotalsWithoutTax(array $lines) {
		$total = 0;
	
		foreach ($lines as $line) {
			if ($line->getType() == Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT) {
				$total -= $line->getAmountExcludingTax();
			}
			else {
				$total += $line->getAmountExcludingTax();
			}
		}
	
		return $total;
	}
	
	protected function getLineTotalsWithTax(array $lines) {
		$total = 0;
	
		foreach ($lines as $line) {
			if ($line->getType() == Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT) {
				$total -= $line->getAmountIncludingTax();
			}
			else {
				$total += $line->getAmountIncludingTax();
			}
		}
	
		return $total;
	}
	
	
	public function getCheckoutId() {
		return $this->checkoutId;
	}
	
	public function getOrderPostId() {
		return $this->order->id;
	}
	
	public function getOrderNumber(){
		$orderNumber = null;
		if(PayEngineCw_ConfigurationAdapter::getOrderNumberIdentifier() == 'ordernumber') {		
			$orderNumber = preg_replace('/[^a-zA-Z\d]/', '', $this->order->get_order_number());
		}
		else{
			$orderNumber = $this->order->id;
		}
		$existing = PayEngineCw_Util::getTransactionsByOrderId($orderNumber);
		if(count($existing) > 0) {
			$number  = count(PayEngineCw_Util::getTransactionsByPostId($this->getOrderPostId()));
			$orderNumber = $orderNumber.'_'.$number;
		}
		return $orderNumber;
	}
}