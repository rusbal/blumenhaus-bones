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
require_once 'Customweb/Core/String.php';
require_once 'Customweb/Payment/Authorization/DefaultInvoiceItem.php';
require_once 'Customweb/Payment/Authorization/IInvoiceItem.php';
require_once 'Customweb/Util/Invoice.php';


/**
 *
 * @author eigenmann
 */
class PayEngineCw_CartUtil {

	private function __construct(){}

	public static function getInoviceItemsFromCart(WC_Cart $cart){
		$wooCommerceItems = $cart->get_cart();
		if (empty($wooCommerceItems)) {
			return array();
		}
		$items = array();
		
		foreach ($wooCommerceItems as $wooItem) {
			/*
			 * @var $product WC_Product
			 */
			
			$product = $wooItem['data'];
			
			$sku = $product->get_sku();
			$name = $product->get_title();
			if (empty($sku)) {
				$sku = Customweb_Core_String::_($name)->replace(" ", "")->replace("\t", "")->convertTo('ASCII')->toLowerCase()->toString(); 
			}
			
			if (isset($wooItem['line_subtotal']) && (isset($wooItem['quantity']) || isset($wooItem['qty'])) && isset($wooItem['line_subtotal_tax'])) {
				$amountExclTax = $wooItem['line_subtotal'];
				$amountIncludingTax = $wooItem['line_subtotal'] + $wooItem['line_subtotal_tax'];
				$taxRate = 0;
				if ($amountExclTax != 0) {
					$taxRate = ($amountIncludingTax - $amountExclTax) / $amountExclTax * 100;
				}
				if (!isset($wooItem['quantity'])) {
					$quantity = $wooItem['qty'];
				}
				else {
					$quantity = $wooItem['quantity'];
				}
			}
			else {
				$quantity = 1;
				$amountExclTax = $wooItem['line_total'];
				$amountIncludingTax = $wooItem['line_total'] + $wooItem['line_tax'];
				$taxRate = 0;
				if ($amountExclTax != 0) {
					$taxRate = ($amountIncludingTax - $amountExclTax) / $amountExclTax * 100;
				}
			}
			
			$item = new Customweb_Payment_Authorization_DefaultInvoiceItem($sku, $name, $taxRate, $amountIncludingTax, $quantity);
			$items[] = $item;
			$discountAmount = ($item->getAmountIncludingTax() / $quantity - $cart->get_discounted_price($wooItem, $item->getAmountIncludingTax() / $quantity)) * $quantity;
			if($discountAmount > 0){
				$discountItem = new Customweb_Payment_Authorization_DefaultInvoiceItem($sku.'-discount', __("Discount", "woocommerce_payenginecw").' '.$name, $taxRate, $discountAmount, 1,
						Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT);
				$items[] = $discountItem;
			}			
		}
		
		// Add Shipping
		if ($cart->shipping_total > 0) {
			$shippingExclTax = $cart->shipping_total;
			$shippingTax = $cart->shipping_tax_total;
			$taxRate = 0;
			if ($shippingExclTax != 0) {
				$taxRate = $shippingTax / $shippingExclTax * 100;
			}
			$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem('shipping', __("Shipping", "woocommerce_payenginecw"), $taxRate, $shippingExclTax + $shippingTax, 1, 
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_SHIPPING);
		}
		
		//Add Fees
		if(count($cart->get_fees()) > 0) {
			foreach($cart->get_fees() as $fee) {
				if($fee->amount == 0) {
					continue;
				}
				$name = $fee->name;
				$sku = Customweb_Core_String::_($name)->replace(" ", "")->replace("\t", "")->convertTo('ASCII')->toString();
		
				$amountExcludingTax = $fee->amount;
				$taxAmount = $fee->tax;
				$taxRate = ((($amountExcludingTax+$taxAmount)/$amountExcludingTax)-1)*100;
			
				$items[] = new Customweb_Payment_Authorization_DefaultInvoiceItem($sku, $name, $taxRate, $amountExcludingTax+$taxAmount, 1,
					Customweb_Payment_Authorization_DefaultInvoiceItem::TYPE_FEE);
			}
		}
		
		return Customweb_Util_Invoice::cleanupLineItems($items, $cart->total, get_woocommerce_currency());
	}

	public static function applyCartDiscounts($discount, $items){
		// Add cart discounts: We need to apply the discount direclty on the line items, because we can not
		// show a discount with a tax. The tax may be a mixure of multiple taxes, which leads to a strange tax
		// rate.
		if ($discount > 0) {
			$newItems = array();
			
			$total = Customweb_Util_Invoice::getTotalAmountIncludingTax($items);
			if($total == 0) {
				//This can only happens if cart contains item with negativ value
				$reductionRate = 1;
			}
			else {
				$reductionRate = $discount / $total;
			}
			
			
			foreach ($items as $item) {
				$newTotalAmount = $item->getAmountExcludingTax() * (1 - $reductionRate) * (1 + $item->getTaxRate() / 100);
				
				$newItem = new Customweb_Payment_Authorization_DefaultInvoiceItem($item->getSku(), $item->getName(), $item->getTaxRate(), 
						$newTotalAmount, $item->getQuantity());
				$newItems[] = $newItem;
			}
			
			return $newItems;
		}
		else {
			return $items;
		}
	}

}