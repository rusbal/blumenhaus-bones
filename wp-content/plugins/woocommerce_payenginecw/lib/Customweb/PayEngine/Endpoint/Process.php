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

require_once 'Customweb/PayEngine/Authorization/Ajax/Adapter.php';
require_once 'Customweb/Payment/Endpoint/Controller/Abstract.php';
require_once 'Customweb/Payment/Endpoint/Annotation/ExtractionMethod.php';
require_once 'Customweb/Core/Http/Response.php';
require_once 'Customweb/PayEngine/Util.php';
require_once 'Customweb/I18n/LocalizableException.php';
require_once 'Customweb/I18n/Translation.php';



/**
 *
 * @author Thomas Hunziker
 * @Controller("process")
 *
 */
class Customweb_PayEngine_Endpoint_Process extends Customweb_Payment_Endpoint_Controller_Abstract {

	/**
	 *
	 * @param Customweb_Core_Http_IRequest $request
	 * @return Customweb_PayEngine_Authorization_Transaction
	 * @throws Exception
	 */
	private function loadTransaction(Customweb_Core_Http_IRequest $request){
		$transactionHandler = $this->getTransactionHandler();
		
		$idMap = $this->getTransactionIdentifier($request);
		if ($idMap['key'] == Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY) {
			$transaction = $transactionHandler->findTransactionByTransactionExternalId($idMap['id'], false);
		}
		elseif ($idMap['key'] == Customweb_Payment_Endpoint_Annotation_ExtractionMethod::PAYMENT_ID_KEY) {
			$transaction = $transactionHandler->findTransactionByPaymentId($idMap['id'], false);
		}
		elseif ($idMap['key'] == Customweb_Payment_Endpoint_Annotation_ExtractionMethod::TRANSACTION_ID_KEY) {
			$transaction = $transactionHandler->findTransactionByTransactionId($idMap['id'], false);
		}
		if ($transaction === null) {
			throw new Exception('No transaction found');
		}
		return $transaction;
	}

	/**
	 *
	 * @param Customweb_Core_Http_IRequest $request 
	 * 
	 * @ExtractionMethod
	 */
	public function getTransactionIdentifier(Customweb_Core_Http_IRequest $request){
		$parameters = $request->getParameters();
		
		if (isset($parameters['cwTransId'])) {
			return array(
				'id' => $parameters['cwTransId'],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY 
			);
		}
		if (isset($parameters['cw_transaction_id'])) {
			return array(
				'id' => $parameters['cw_transaction_id'],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::EXTERNAL_TRANSACTION_ID_KEY 
			);
		}
		if (isset($parameters['PAYID'])) {
			return array(
				'id' => $parameters['PAYID'],
				'key' => Customweb_Payment_Endpoint_Annotation_ExtractionMethod::PAYMENT_ID_KEY 
			);
		}
		
		throw new Exception("No transaction identifier present in the request.");
	}

	/**
	 * @Action("update")
	 */
	public function update(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request){
		if (!$transaction->isAuthorized() && !$transaction->isAuthorizationFailed()) {
			return $this->process($transaction, $request);
		}
		if ($transaction->getStatusAfterReceivingUpdate() != 'pending') {
			//We already handled an update successfully, or it's a update we do not expect/handle
			return new Customweb_Core_Http_Response();
		}
		$responseParameters = $request->getParameters();
		$parameters = $transaction->getAuthorizationParameters();
		$config = $this->getContainer()->getBean('Customweb_PayEngine_Configuration');
		
		$hash = Customweb_PayEngine_Util::calculateHash($responseParameters, 'OUT', $config);
		
		if (isset($responseParameters['SHASIGN']) && $responseParameters['SHASIGN'] == $hash) {
			$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
			$adapter->processNewStatus($transaction, $responseParameters, $parameters['INITIALSTATUS']);
		}
		return new Customweb_Core_Http_Response();
	}

	/**
	 * @Action("index")
	 */
	public function process(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request){
		$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
		/* @var $adapter Customweb_PayEngine_AbstractAdapter */
		$pm = $adapter->getPaymentMethodByTransaction($transaction);
		$parameters = $request->getParameters();
		return $pm->processAuthorization($adapter, $transaction, $parameters);
	}

	/**
	 * This is the regular completion of an Amazon Transaction.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param Customweb_Core_Http_IRequest $request
	 * 
	 * @Action("amco")
	 */
	public function handleAmazoneStepTwo(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request){
		$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
		$parameters = $request->getParsedBody();
		return $adapter->processAuthorization($transaction, $parameters);
	}

	/**
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param Customweb_Core_Http_IRequest $request
	 * 
	 * @Action("hosted")
	 */
	public function initializeHostedToken(Customweb_Core_Http_IRequest $request){
		
		$parameters = $request->getParameters();
		
		for ($i = 0; $i < 5; $i++) {
			try {
				$this->getTransactionHandler()->beginTransaction();
				$transaction = $this->loadTransaction($request);
			
				if($transaction->isAuthorized()){
					$this->getTransactionHandler()->commitTransaction();
					return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getSuccessUrl());
				}
				elseif ($transaction->isAuthorizationFailed()){
					$this->getTransactionHandler()->commitTransaction();
					return Customweb_PayEngine_Util::createBreakoutResponse($transaction->getFailedUrl());
				}
				elseif($transaction->is3dRedirectionRequired()){
					$authParam = $transaction->getAuthorizationParameters();
					if(isset($authParam['HTML_ANSWER'])){
						$url = $this->getEndpointAdapter()->getUrl('process', 'redirect3d', array(
							'cwTransId' => $transaction->getExternalTransactionId(),
							'cwHash' => $transaction->getSecuritySignature('process/redirect3d')
						));
						$this->getTransactionHandler()->commitTransaction();
						return Customweb_PayEngine_Util::createBreakoutResponse($url);
					}
				}
				
				$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
				if (!($adapter instanceof Customweb_PayEngine_Authorization_Ajax_Adapter)) {
					throw new Exception('Invalid Authorization Adapter');
				}
				$response  = $adapter->processTokenCreation($transaction, $parameters);
				$this->getTransactionHandler()->persistTransactionObject($transaction);
				$this->getTransactionHandler()->commitTransaction();
				return $response;
			}
			catch (Customweb_Payment_Exception_OptimisticLockingException $lockingException) {
				$this->getTransactionHandler()->rollbackTransaction();
			}
		}
		return Customweb_Core_Http_Response::_('');
	}

	/**
	 *
	 * @param Customweb_Core_Http_IRequest $request 
	 * 
	 * @Action("return")
	 */
	public function finishHostedToken(Customweb_Core_Http_IRequest $request){
		$transaction = $this->loadTransaction($request);
		$parameters = $request->getParameters();
		
		if ($transaction->isAuthorizationFailed()) {
			return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
		}
		elseif ($transaction->isAuthorized()) {
			return Customweb_Core_Http_Response::redirect($transaction->getSuccessUrl());
		}
		else {
			if (isset($parameters['state']) && $parameters['state'] == 'success') {
				return Customweb_Core_Http_Response::redirect($transaction->getSuccessUrl());
			}
			elseif (isset($parameters['state']) && $parameters['state'] == 'fail') {
				return Customweb_Core_Http_Response::redirect($transaction->getFailedUrl());
			}
		}
		throw new Exception('Invalid Request');
	}

	/**
	 * @param Customweb_Core_Http_IRequest $request
	 * 
	 * @Action("redirect3d")
	 */
	public function redirectThreeD(Customweb_Core_Http_IRequest $request){
		$transaction = $this->loadTransaction($request);
		$parameters = $request->getParameters();
		if (!isset($parameters['cwHash'])) {
			throw new Customweb_I18n_LocalizableException(Customweb_I18n_Translation::__('No signature provided.'));
		}
		$transaction->checkSecuritySignature('process/redirect3d', $parameters['cwHash']);
		$authParameters = $transaction->getAuthorizationParameters();
		$response = Customweb_Core_Http_Response::_(base64_decode($authParameters['HTML_ANSWER']));
		$response->appendHeader('cache-control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0');
		return $response;
	}
}