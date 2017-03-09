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

require_once 'Customweb/PayEngine/Authorization/AbstractAuthorizationParameterBuilder.php';
require_once 'Customweb/Payment/Authorization/Moto/IAdapter.php';
require_once 'Customweb/Core/Url.php';

class Customweb_PayEngine_Authorization_Ajax_DirectParameterBuilder extends Customweb_PayEngine_Authorization_AbstractAuthorizationParameterBuilder {
	

	protected function getAliasManagerParameters(){
		$parameters = array();
		
		$parameters['ALIAS'] = $this->formData['ALIAS_ALIASID'];
		$parameters['ALIASPERSISTEDAFTERUSE'] = $this->formData['ALIAS_STOREPERMANENTLY'];
		
		return $parameters;
	}

	protected function getReactionUrlParameters(){
		$url = $this->getEndpointAdapter()->getUrl('process', 'return', 
				array(
					'cwTransId' => $this->getTransaction()->getExternalTransactionId(),
				));
		$failUrl = Customweb_Core_Url::_($url)->appendQueryParameter('state', 'fail')->toString();
		return array(
			'ACCEPTURL' => Customweb_Core_Url::_($url)->appendQueryParameter('state', 'success')->toString(),
			'EXCEPTIONURL' => $failUrl, 
			'DECLINEURL' => $failUrl,
			'CANCELURL' => $failUrl,
			'BACKURL' => $failUrl
			
		);
	}
	

	protected function getCustomerParameters(){
		$parameters = parent::getCustomerParameters();
	
		//For alias gateway use, the cardholder entred in the form
		if(isset($this->formData['CARD_CARDHOLDERNAME'])){
			$cn = $this->formData['CARD_CARDHOLDERNAME'];
			if(!empty($cn)){
				$parameters['CN'] = $cn;
			}
		}
	
		return $parameters;
	}
	
	protected function get3DSecureParameters(){
		if ($this->getTransaction()->getAuthorizationMethod() != Customweb_Payment_Authorization_Moto_IAdapter::AUTHORIZATION_METHOD_NAME) {
			return array(
				'FLAG3D' => 'Y',
				'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'],
				'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
				'WIN3DS' => 'MAINW'
			);
		}
		else {
			return array();
		}
	}
}
	