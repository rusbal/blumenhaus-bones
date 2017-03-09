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

require_once 'Customweb/Core/Http/Response.php';
require_once 'Customweb/Payment/Authorization/ITransaction.php';
require_once 'Customweb/PayEngine/Authorization/Transaction.php';
require_once 'Customweb/Core/Url.php';
require_once 'Customweb/I18n/Translation.php';
require_once 'Customweb/PayEngine/Authorization/Ajax/InitParameterBuilder.php';
require_once 'Customweb/Payment/Authorization/Ajax/IAdapter.php';
require_once 'Customweb/PayEngine/Authorization/AbstractAdapter.php';
require_once 'Customweb/PayEngine/Util.php';
require_once 'Customweb/Payment/Authorization/ErrorMessage.php';
require_once 'Customweb/Util/JavaScript.php';
require_once 'Customweb/PayEngine/Authorization/Ajax/DirectParameterBuilder.php';



/**
 *
 * @author Thomas Hunziker
 * @Bean
 *
 */
class Customweb_PayEngine_Authorization_Ajax_Adapter extends Customweb_PayEngine_Authorization_AbstractAdapter implements 
		Customweb_Payment_Authorization_Ajax_IAdapter {

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}

	public function getAdapterPriority(){
		return 150;
	}

	public function getAjaxFileUrl(Customweb_Payment_Authorization_ITransaction $transaction){
		$assetResolver = $this->getContainer()->getBean('Customweb_Asset_IResolver');
		return (string) $assetResolver->resolveAssetUrl('hosted.js');
	}

	public function getJavaScriptCallbackFunction(Customweb_Payment_Authorization_ITransaction $transaction){
		$builder = new Customweb_PayEngine_Authorization_Ajax_InitParameterBuilder($transaction, $this->getContainer());
		$parameters = $builder->buildParameters();
		$iframeUrl = Customweb_Core_Url::_($this->getConfiguration()->getFlexCheckoutUrl())->appendQueryParameters($parameters)->toString();
		
		$cssUrl = $this->getContainer()->getBean('Customweb_Asset_IResolver')->resolveAssetUrl('hosted.css');
		
		$execute = '
					if(typeof window.jQuery == "undefined") {
						window.jQuery = cwjQuery;
					}
					payengineFlexCheckout.includeCss("' . $cssUrl . '");
					payengineFlexCheckout.createIframe("' . $iframeUrl . '", window.jQuery);';
		
		$complete = "function(formFieldValues) {" . Customweb_Util_JavaScript::getLoadJQueryCode(null, 'cwjQuery', 'function(){' . $execute . '}') .
				 '}';
		return $complete;
	}

	public function createTransaction(Customweb_Payment_Authorization_Ajax_ITransactionContext $transactionContext, $failedTransaction){
		$transaction = new Customweb_PayEngine_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod(self::AUTHORIZATION_METHOD_NAME);
		
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		
		return $transaction;
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $customerPaymentContext){
		return array();
		/*
		 * TODO: Check Forms
		 * $paymentMethod = $this->getPaymentMethodFactory()->getPaymentMethod($orderContext->getPaymentMethod(), self::AUTHORIZATION_METHOD_NAME);
		 * return $paymentMethod->getFormFields($orderContext, $aliasTransaction, $failedTransaction, self::AUTHORIZATION_METHOD_NAME, false,
		 * $customerPaymentContext);
		 */
	}
	
	/**
	 * This function handles the notification
	 * 
	 * @param Customweb_PayEngine_Authorization_Transaction $transaction
	 * @param array $parameters
	 * @return Customweb_Core_Http_Response
	 */
	public function processAuthorization(Customweb_PayEngine_Authorization_Transaction $transaction, array $parameters) {
	
		// In case the authorization failed, we stop processing here
		if ($transaction->isAuthorizationFailed()) {
			return $this->finalizeAuthorizationRequest($transaction);
		}
	
		// In case the transaction is authorized, we do not have to do anything here.         		  	 			   		
		if ($transaction->isAuthorized()) {
			return $this->finalizeAuthorizationRequest($transaction);
		}
		
		$transaction->appendAuthorizationParameters($parameters);
		$parameters = array_change_key_case($parameters, CASE_UPPER);
		if (!$this->validateResponse($parameters)) {
			$transaction->setAuthorizationFailed(
					Customweb_I18n_Translation::__(
							'The notification failed because the SHA signature seems not to be valid.'
							)
					);
		}
		else {
			$this->setTransactionAuthorizationState($transaction, $parameters);
		
		}
		return $this->finalizeAuthorizationRequest($transaction);
	}
	

	public function processTokenCreation(Customweb_PayEngine_Authorization_Transaction $transaction, array $parameters){
		$computed = Customweb_PayEngine_Util::calculateHash($parameters, "out", $this->getConfiguration());
		$parameters = array_change_key_case($parameters, CASE_UPPER);
		if (!isset($parameters['SHASIGN']) || $parameters['SHASIGN'] != $computed) {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__("The SHA signatures do not match."));
			return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		$method = $this->getPaymentMethodByTransaction($transaction);
		$transaction->setAliasCreationResponse($parameters);
		
		if(!isset($parameters['ALIAS_STATUS']) || !isset($parameters['ALIAS_NCERROR']) || !isset($parameters['ALIAS_ALIASID']) || !isset($parameters['ALIAS_STOREPERMANENTLY'])) {
			$errorMessage = new Customweb_Payment_Authorization_ErrorMessage(Customweb_I18n_Translation::__('The payment failed due to technical difficulties.'), 
					Customweb_I18n_Translation::__('Missing return parameters for the tokenization, please check the dynamic parameter configuration in the ConCardis backend.'));
			$transaction->setAuthorizationFailed($errorMessage);
			return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		
		if ($parameters['ALIAS_NCERROR'] != '0') {
			$errorMessage = $method->getAliasCreationErrorMessage($parameters);
			$transaction->setAuthorizationFailed($errorMessage);
			return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		if ($parameters['ALIAS_STATUS'] == '1') {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment failed due to technical difficulties.'));
			return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		elseif ($parameters['ALIAS_STATUS'] == '3') {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment was successfully cancelled.'));
			return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		elseif ($parameters['ALIAS_STATUS'] == '0' || $parameters['ALIAS_STATUS'] == '2') {
			try {
				$builder = new Customweb_PayEngine_Authorization_Ajax_DirectParameterBuilder($transaction, $this->getContainer(), $parameters);
				$response = Customweb_PayEngine_Util::sendDirectRequest($this->getDirectOrderUrl(), $builder->buildParameters());
					
				unset($parameters['CVC']);
				unset($parameters['CARD_CVC']);

				$transaction->appendAuthorizationParameters($response);
				$transaction->appendAuthorizationParameters($parameters);
				
				$converted = array();
				if(isset($parameters['CARD_CARDNUMBER'])){
					$converted['CARDNO'] = $parameters['CARD_CARDNUMBER'];
				}
				if(isset($parameters['CARD_EXPIRYDATE'])){
					$converted['ED'] = $parameters['CARD_EXPIRYDATE'];
				}
				if(isset($parameters['CARD_BRAND'])){
					$converted['BRAND'] = $parameters['CARD_BRAND'];
				}
				$transaction->appendAuthorizationParameters($converted);
					
				// Check whether a 3D secure redirection is required or not.
				if (!$transaction->is3dRedirectionRequired()) {
					$this->setTransactionAuthorizationState($transaction, $response);
				}
				if($parameters['ALIAS_STATUS'] == '2'){
					try{
						if($transaction->getTransactionContext()->getAlias() instanceof Customweb_Payment_Authorization_ITransaction) {
							/* @var $transactionHandler Customweb_Payment_ITransactionHandler */
							$transactionHandler = $this->getContainer()->getBean('Customweb_Payment_ITransactionHandler');
							$aliasTransaction = $transactionHandler->findTransactionByTransactionId($transaction->getTransactionContext()->getAlias()->getTransactionId());
							$aliasTransaction->setAliasForDisplay(null);
							$transactionHandler->persistTransactionObject($aliasTransaction);
						}
					}catch(Exception $e){
						//ignore if we can not remove old alias display
					}
					
				}
			}
			catch (Exception $e) {
				$transaction->setAuthorizationFailed($e->getMessage());
			}
			
			if ($transaction->isAuthorizationFailed()) {
				return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
			}
			
			if ($transaction->isAuthorized()) {
				return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getSuccessUrl());
			}
			
			// Handle 3D secure case
			if (!$transaction->isAuthorized()) {
				if ($transaction->is3dRedirectionRequired()) {
					$url = $this->getEndpointAdapter()->getUrl('process', 'redirect3d', array(
						'cwTransId' => $transaction->getExternalTransactionId(),
						'cwHash' => $transaction->getSecuritySignature('process/redirect3d')
					));
					
					return Customweb_PayEngine_Util::createBreakoutResponse($url);
				}
			}
			return Customweb_Core_Http_Response::_("The transaction is in a bad state.");
		}
		else {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment failed due to technical difficulties.'));
			return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
	}
	
	public function finalizeAuthorizationRequest(Customweb_Payment_Authorization_ITransaction $transaction) {
	
		return new Customweb_Core_Http_Response();
	}

}