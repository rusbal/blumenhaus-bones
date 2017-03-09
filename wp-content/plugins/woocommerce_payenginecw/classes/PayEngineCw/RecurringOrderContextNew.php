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
require_once 'PayEngineCw/Util.php';
require_once 'Customweb/Payment/Authorization/DefaultInvoiceItem.php';
require_once 'Customweb/Util/Invoice.php';
require_once 'PayEngineCw/AbstractRecurringOrderContext.php';

class PayEngineCw_RecurringOrderContextNew extends PayEngineCw_AbstractRecurringOrderContext {
	

	public function __construct($order, $paymentMethod, $amountToCharge){
		parent::__construct($order, $paymentMethod, $amountToCharge);
		$subscriptions = wcs_get_subscriptions_for_order($order->id, array(
			'order_type' => array(
				'parent',
				'renewal' 
			) 
		));
		if (1 == count($subscriptions)) {
			$subscription = end($subscriptions);
			$initialTransactionId = get_post_meta($subscription->id, 'cwInitialTransactionRecurring', true);
			$initialTransaction = null;
			if(!empty($initialTransactionId)) {
				$this->setInitialTransactionId($initialTransactionId);
				$initialTransaction = PayEngineCw_Util::getTransactionById($initialTransactionId);
			}
			else{
				$initialOrderId = get_post_meta($subscription->id, 'cwCurrentInitialRecurring', true);
				if (empty($initialOrderId)) {
					if (false !== $subscription->order) {
						$initialOrderId = $subscription->order->id;
					}
				}
				$this->setInitialOrderId($initialOrderId);
				$initialTransaction = PayEngineCw_Util::getAuthorizedTransactionByPostId($this->getInitialOrderId());
				if(empty($initialTransaction)){
					$initialTransaction = PayEngineCw_Util::getAuthorizedTransactionByOrderId($this->getInitialOrderId());
				}
			}
			if ($initialTransaction === NULL) {
				throw new Exception(sprintf("No initial transaction found for order %s.", $this->getInitialOrderId()));
			}
			$this->currencyCode = $initialTransaction->getTransactionObject()->getCurrencyCode();
			$this->userId = $initialTransaction->getCustomerId();
		}
		
	}



	public function getInvoiceItems(){
		$items = $this->getInvoiceItemsInternal();
		
		// Calculate the difference to the amountToCharge. This can happen, when some outstanding payments are added to this one.
		$total = $this->getLineTotalsWithTax($items);
		$difference = $this->orderAmount - $total;
		if ($difference > 0) {
			$taxRate = 0;
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem('outstanding-payments', __('Outstanding Payments'), $taxRate, 
					$difference, 1, Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_PRODUCT);
		}
		else if ($difference < 0) {
			$taxRate = 0;
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem('other-discount', 
					__('Other Discount', 'woocommerce_payenginecw'), $taxRate, abs($difference), 1, 
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_DISCOUNT);
		}
		
		return Customweb_Util_Invoice::cleanupLineItems($items, $this->getOrderAmountInDecimals(), $this->getCurrencyCode());
	}

	public function isNewSubscription(){
		return false;
	}
}