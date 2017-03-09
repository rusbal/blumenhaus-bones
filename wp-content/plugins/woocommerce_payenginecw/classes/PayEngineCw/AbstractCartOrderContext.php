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

require_once 'Customweb/Payment/Authorization/DefaultInvoiceItem.php';
require_once 'Customweb/Core/Language.php';
require_once 'Customweb/Payment/Authorization/OrderContext/AbstractDeprecated.php';


/**
 * This class implements a order context based on user data and the current cart. This order context should never be persisted!
 * @author hunziker
 *
 */
abstract class PayEngineCw_AbstractCartOrderContext extends  Customweb_Payment_Authorization_OrderContext_AbstractDeprecated
{
	protected $cart;
	protected $orderAmount;
	protected $currencyCode;
	protected $paymentMethod;
	protected $language;
	protected $userId;
	protected $userData;
	
	protected $checkoutId;
	
	abstract protected function isShipToBilling();
	
	public function getCustomerId() {
		return $this->userId;
	}
	
	public function isNewCustomer() {
		return 'unkown';
	}
	
	public function getCustomerRegistrationDate() {
		return null;
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
		return $this->userData['billing_email'];
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
		return $this->userData['billing_first_name'];
	}
	
	public function getBillingLastName() {
		return $this->userData['billing_last_name'];
	}
	
	public function getBillingStreet() {
		$second = $this->userData['billing_address_2'];
		if(empty($second)){
			return $this->userData['billing_address_1'];
		}
		return $this->userData['billing_address_1'] . " ". $second;
		
	}
	
	public function getBillingCity() {
		return $this->userData['billing_city'];
	}
	
	public function getBillingPostCode() {
		return $this->userData['billing_postcode'];
	}
	
	public function getBillingState() {
		return $this->cleanUpStateField($this->userData['billing_state']);
	}
	
	public function getBillingCountryIsoCode() {
		return $this->userData['billing_country'];
	}
	
	public function getBillingPhoneNumber() {
		return $this->userData['billing_phone'];
	}
	
	public function getBillingMobilePhoneNumber() {
		return null;
	}
	
	public function getBillingDateOfBirth() {
		return null;
	}
	
	public function getBillingCompanyName() {
		return $this->userData['billing_company'];
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
		if ($this->isShipToBilling() || !isset($this->userData['shipping_email'])) {
			return $this->getBillingEMailAddress();
		}
		else {
			$shippingEmail = $this->userData['shipping_email'];
			if (!empty($shippingEmail)) {
				return $shippingEmail;
			}
			else {
				return $this->getBillingEMailAddress();
			}
		}
	}
	
	public function getShippingGender() {
		if ($this->isShipToBilling()) {
			return $this->getBillingGender();
		}
		else {
			$company = trim($this->getShippingCompanyName());
			if (!empty($company)) {
				return 'company';
			}
			else {
				return null;
			}
		}
	}
	
	public function getShippingSalutation() {
		return null;
	}
	
	public function getShippingFirstName() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_first_name'])) {
			return $this->getBillingFirstName();
		}
		else {
			return $this->userData['shipping_first_name'];
		}
	}
	
	public function getShippingLastName() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_last_name'])) {
			return $this->getBillingLastName();
		}
		else {
			return $this->userData['shipping_last_name'];
		}
	}
	
	public function getShippingStreet() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_address_1'])) {
			return $this->getBillingStreet();
		}
		else {
			$second = $this->userData['shipping_address_2'];
			if(empty($second)){
				return $this->userData['shipping_address_1'];
			}
			return$this->userData['shipping_address_1']. " ". $second;
			
		}
	}
	
	public function getShippingCity() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_city'])) {
			return $this->getBillingCity();
		}
		else {
			return $this->userData['shipping_city'];
		}
		
	}
	
	public function getShippingPostCode() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_postcode'])) {
			return $this->getBillingPostCode();
		}
		else {
			return $this->userData['shipping_postcode'];
		}
		
	}
	
	public function getShippingState() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_state'])) {
			return $this->getBillingState();
		}
		else {
			return $this->cleanUpStateField($this->userData['shipping_state']);
		}
	}
	
	public function getShippingCountryIsoCode() {
		if ($this->isShipToBilling() || !isset($this->userData['shipping_country'])) {
			return $this->getBillingCountryIsoCode();
		}
		else {
			return $this->userData['shipping_country'];
		}
		
	}
	
	public function getShippingPhoneNumber() {
		if ($this->isShipToBilling()) {
			return $this->getBillingPhoneNumber();
		}
		else {
			$shippingPhone = $this->userData['shipping_phone'];
			if (!empty($shippingPhone)) {
				return $shippingPhone;
			}
			else {
				return $this->getBillingPhoneNumber();
			}
		}
		
	}
	
	public function getShippingMobilePhoneNumber() {
		return null;
	}
	
	public function getShippingDateOfBirth() {
		return null;
	}
	
	public function getShippingCompanyName() {
		if ($this->isShipToBilling()) {
			return $this->getBillingCompanyName();
		}
		else {
			if(isset($this->userData['shipping_company'])){
				return $this->userData['shipping_company'];
			}
			return null;
		}
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
	

	
	protected function cleanUpStateField($state) {
		if (!empty($state) && strlen($state) == 2) {
			return $state;
		}
		else {
			return null;
		}
	}
	
	
	public function getCheckoutId() {
		return $this->checkoutId;
	}

	
}