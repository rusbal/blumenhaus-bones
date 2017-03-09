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
require_once 'PayEngineCw/ContextRequest.php';
require_once 'PayEngineCw/Util.php';
require_once 'Customweb/Core/Util/System.php';
require_once 'PayEngineCw/Controller/Abstract.php';


/**
 *
 * @author Nico Eigenmann
 *
 */
class PayEngineCw_Controller_Success extends PayEngineCw_Controller_Abstract {

	public function indexAction() {
			
		$parameters = PayEngineCw_ContextRequest::getInstance()->getParameters();
		$dbTransaction = null;
		try {
			$dbTransaction = $this->loadTransaction($parameters);
		}
		catch(Exception $e) {
			return $this->formatErrorMessage($e->getMessage());
		}
	
		$start = time();
		$maxExecutionTime = Customweb_Core_Util_System::getMaxExecutionTime() - 10;
			
		if ($maxExecutionTime > 30) {
			$maxExecutionTime = 30;
		}
	
	
		$order = $dbTransaction->getTransactionObject()->getTransactionContext()->getOrderContext()->getOrderObject();
		$method = PayEngineCw_Util::getPaymentMehtodInstance($dbTransaction->getPaymentClass());
		if (method_exists($method, 'get_return_url')) {
			$successUrl = $method->get_return_url($order);
		}
		else {
			$option = PayEngineCw_Util::getShopOption('woocommerce_thanks_page_id');
			$option = apply_filters( 'woocommerce_get_thanks_page_id', $option);
			$checkout_redirect = apply_filters( 'woocommerce_get_checkout_redirect_page_id', $option );
			$successUrl = add_query_arg('key', $order->order_key, add_query_arg('order', $order->id, get_permalink(PayEngineCw_Util::getPermalinkIdModified($checkout_redirect))));
		}
		// We have to close the session here otherwise the transaction may not be updated by the notification
		// callback.
		session_write_close();
	
		// Wait as long as the notification is done in the background
		while (true) {
	
	
			$dbTransaction = PayEngineCw_Util::getTransactionById($parameters['cwtid'], false);
			$transactionObject = $dbTransaction->getTransactionObject();
	
			$url = null;
			if ($transactionObject->isAuthorizationFailed()) {
	
				$url = PayEngineCw_Util::getPluginUrl('failure', array('cwtid' => $parameters['cwtid'], 'cwtt' => $parameters['cwtt']));
			}
			else if ($transactionObject->isAuthorized()) {
				global $woocommerce;
				$url = $successUrl;
				if (isset($woocommerce)) {
					$woocommerce->cart->empty_cart();
				}
			}
	
			if ($url !== null) {
				header('Location: ' . $url);
				die();
			}
	
			if (time() - $start > $maxExecutionTime) {
				ob_start();
				$GLOBALS['woo_payenginecwTitle'] = __('Time Out' , 'woocommerce_payenginecw');
				PayEngineCw_Util::includeTemplateFile('timeout', array('successUrl' => $successUrl));
				$content = ob_get_clean();
				return $content;
			}
			else {
				// Wait 2 seconds for the next try.
				sleep(2);
			}
		}
	}
	
	
}