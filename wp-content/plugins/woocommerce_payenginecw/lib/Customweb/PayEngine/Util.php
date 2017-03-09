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

require_once 'Customweb/Core/Http/Request.php';
require_once 'Customweb/Core/String.php';
require_once 'Customweb/Core/Http/Client/Factory.php';
require_once 'Customweb/Core/Http/Response.php';
require_once 'Customweb/PayEngine/Util.php';
require_once 'Customweb/I18n/Translation.php';



/**
 * This util class some basic functions for PayEngine.
 *
 * @author Thomas Hunziker
 *
 */
final class Customweb_PayEngine_Util {

	private function __construct(){
		// prevent any instantiation of this class	
	}

	public static function applyOrderSchema(Customweb_PayEngine_Configuration $config, $transactionId){
		$orderSchema = $config->getOrderIdSchema();
		$id = (string) $transactionId;
		
		if (!empty($orderSchema)) {
			$totalLength = strlen($id) + strlen($orderSchema);
			if ($totalLength > 30) {
				$lengthToReduce = ($totalLength - 30);
				$orderSchema = Customweb_PayEngine_Util::substrUtf8($orderSchema, min($lengthToReduce, strlen($orderSchema)), 
						strlen($orderSchema));
			}
			
			if (strstr($orderSchema, '{id}')) {
				$id = str_replace('{id}', $id, $orderSchema);
			}
			else {
				$id = $orderSchema . $id;
			}
		}
		return Customweb_PayEngine_Util::substrUtf8($id, 0, 30);
	}

	public static function applyOrderDescriptionSchema(Customweb_PayEngine_Configuration $config, $transactionId){
		$orderSchema = $config->getOrderDescriptionSchema();
		$id = (string) $transactionId;
		
		if (!empty($orderSchema)) {
			$totalLength = strlen($id) + strlen($orderSchema);
			if ($totalLength > 30) {
				$lengthToReduce = ($totalLength - 30);
				$orderSchema = Customweb_PayEngine_Util::substrUtf8($orderSchema, min($lengthToReduce, strlen($orderSchema)), 
						strlen($orderSchema));
			}
			
			if (strstr($orderSchema, '{id}')) {
				$id = str_replace('{id}', $id, $orderSchema);
			}
		}
		return Customweb_PayEngine_Util::substrUtf8($id, 0, 30);
	}

	/**
	 * This method does a HTTP POST request to the given URL with
	 * the given parameters.
	 *         		  	 			   		
	 * 
	 * @param string $url The URL to which the request is send to.
	 * @param array $parameters An array of key / value pairs
	 * @return string The body of the response
	 */
	private static function sendRequest($url, $parameters){
	
		$request = new Customweb_Core_Http_Request($url);
		
		$request->setBody($parameters);
		$request->setMethod('POST');
		$request->setContentType('application/x-www-form-urlencoded');
		$client = Customweb_Core_Http_Client_Factory::createClient();
		$response = $client->send($request);
		if($response->getStatusCode() != 200) {
			throw new Exception(
					'ConCardis Server response is: ' . $response->getStatusCode() . ' ' . $response->getStatusMessage());
		}
		$responseBody = trim($response->getBody());
		return $responseBody;
	}

	public static function getXmlAttributes($xml){
		preg_match_all('/([^[:space:]=]+)\="([^"]*)"/i', $xml, $result);
		
		$parameters = array();
		foreach ($result[1] as $key => $value) {
			$parameters[$value] = html_entity_decode($result[2][$key]);
		}
		
		preg_match('/\<HTML_ANSWER\>([^\<]*)/i', $xml, $result);
		
		if (count($result) > 0) {
			$parameters['HTML_ANSWER'] = $result[1];
		}
		
		return $parameters;
	}

	/**
	 * This method retrieves a substring of a UTF-8 string.
	 * The regular substr
	 * method does not support UTF-8.
	 *
	 * @param string $string The original string
	 * @param int $start The start char index.
	 * @param int $end [optional] The end char index. If not set the length of the string is used.
	 * @return string The resulting new string.
	 */
	public static function substrUtf8($string, $start, $end = NULL){
		$stringObject = Customweb_Core_String::_($string);
		if($end === null) {
			return $stringObject->substring($start, $stringObject->getLength())->toString();
		}
		elseif($end < 0) {
			return $stringObject->substring($start, $stringObject->getLength()+$end)->toString();
		}
		else {
			return $stringObject->substring($start, $end)->toString();
		}
		
	}

	public static function calculateHash($parameters, $transaction, Customweb_PayEngine_Configuration $configuration){
		$cleanParameters = array();
		
		$signature = '';
		if (strtolower($transaction) == 'in') {
			$signature = $configuration->getShaPassphraseIn();
			
			if (empty($signature)) {
				throw new Exception("The SHA IN passphrase is empty.");
			}
			
			foreach ($parameters as $key => $value) {
				if ($value != '' && strtoupper($key) != 'SHASIGN' && strtoupper($key) != 'SHASIGNATURE.SHASIGN') {
					$cleanParameters[] = strtoupper($key) . '=' . $value . $signature;
				}
			}
		}
		else {
			$signature = $configuration->getShaPassphraseOut();
			$allowedParameters = array(
				'AAVADDRESS',
				'AAVCHECK',
				'AAVMAIL',
				'AAVNAME',
				'AAVPHONE',
				'AAVZIP',
				'ACCEPTANCE',
				'ALIAS',
				'AMOUNT',
				'BIC',
				'BIN',
				'BRAND',
				'CARDNO',
				'CCCTY',
				'CN',
				'COLLECTOR_BIC',
				'COLLECTOR_IBAN',
				'COM',
				'COMPLUS',
				'CREATION_STATUS',
				'CREDITDEBIT',
				'CURRENCY',
				'CVCCHECK',
				'CVC',
				'DCC_COMMPERCENTAGE',
				'DCC_CONVAMOUNT',
				'DCC_CONVCCY',
				'DCC_EXCHRATE',
				'DCC_EXCHRATESOURCE',
				'DCC_EXCHRATETS',
				'DCC_INDICATOR',
				'DCC_MARGINPERCENTAGE',
				'DCC_VALIDHOUS',
				'DEVICEID',
				'DIGESTCARDNO',
				'ECI',
				'ED',
				'EMAIL',
				'ENCCARDNO',
				'FXAMOUNT',
				'FXCURRENCY',
				'IP',
				'IPCTY',
				'MANDATEID',
				'MOBILEMODE',
				'NBREMAILUSAGE',
				'NBRIPUSAGE',
				'NBRIPUSAGE_ALLTX',
				'NBRUSAGE',
				'NCERROR',
				'NCERRORCVC',
				'NCERRORED',
				'NCERRORCN',
				'NCERRORCARDNO',
				'ORDERID',
				'PAYID',
				'PAYIDSUB',
				'PAYLIBIDREQUEST',
				'PAYLIBTRANSID',
				'PAYMENT_REFERENCE',
				'PM',
				'SCO_CATEGORY',
				'SCORING',
				'SEQUENCETYPE',
				'SIGNDATE',
				'STATUS',
				'SUBBRAND',
				'SUBSCRIPTION_ID',
				'TRXDATE',
				'VC',
				'WALLET',
			);
			
			$allowedChanged = array(
				'ALIAS.ALIASID',
				'ALIAS.NCERROR',
				'ALIAS.NCERRORCARDNO',
				'ALIAS.NCERRORCN',
				'ALIAS.NCERRORCVC',
				'ALIAS.NCERRORED',
				'ALIAS.ORDERID',
				'ALIAS.STATUS',
				'ALIAS.STOREPERMANENTLY',
				'CARD.BIC',
				'CARD.BIN',
				'CARD.BRAND',
				'CARD.CARDHOLDERNAME',
				'CARD.CARDNUMBER',
				'CARD.CVC',
				'CARD.EXPIRYDATE',
			);
			
			if (empty($signature)) {
				throw new Exception("The SHA OUT passphrase is empty.");
			}
			
			foreach ($parameters as $key => $value) {
				if ($value != '' && in_array(strtoupper($key), $allowedParameters)) {
					$cleanParameters[] = strtoupper($key) . '=' . $value . $signature;
				}
				if($value != '' && in_array(strtoupper(str_replace('_','.',$key)), $allowedChanged)) {
					$cleanParameters[] = strtoupper(str_replace('_','.',$key)) . '=' . $value . $signature;
				}
			}
		}
		
		uasort($cleanParameters, array('Customweb_PayEngine_Util', 'sortParameters'));
		$string_before_hash = implode('', $cleanParameters);
		$hashMethod = preg_replace('/[^a-z0-9]+/', '', strtolower($configuration->getHashMethod()));
		switch ($hashMethod) {
			case 'sha256':
				return strtoupper(hash('sha256', $string_before_hash));
				break;
			
			case 'sha512':
				return strtoupper(hash('sha512', $string_before_hash));
				break;
			
			default:
				return strtoupper(sha1($string_before_hash));
		}
	}

	/**
	 * Replaces parts of the IBAN string to mask the number.
	 * 
	 * @param string $iban
	 * @return string
	 */
	public static function maskIban($iban){
		$iban = str_replace(' ', '', $iban);
		$start = substr($iban, 0, 4);
		$end = substr($iban, -4, 4);
		return str_pad($start, strlen($iban) - 8, 'X') . $end;
	}
	
	/**
	 * Method which sorts the array items in the correct order with special corrections.
	 * 
	 * @param string $str1
	 * @param string $str2
	 * @return number
	 */
	private static function sortParameters($str1, $str2) {
		// SCO_CATEGORY must be before SCORING
		if (strpos($str1, 'SCO_CATEGORY') === 0 && strpos($str2, 'SCORING') === 0) {
			return -1;
		}
		else if(strpos($str2, 'SCO_CATEGORY') === 0 && strpos($str1, 'SCORING') === 0) {
			return 1;
		}
		
		else {
			return strnatcmp($str1, $str2);
		}
	}
	
	public static function sendDirectRequest($url, $parameters){
		$response = self::sendRequest($url, $parameters);
		$responseParameters = self::getXmlAttributes($response);
	
		// The request can not be validated, because the answer of PayEngine does not contain the
		// sha signature.
		if (!is_array($responseParameters) || count($responseParameters) <= 0) {
			throw new Exception(
					Customweb_I18n_Translation::__('The server response was not valid. Response: @response',
							array(
								'@response' => $response
							)));
		}
	
		return $responseParameters;
	}
	
	
	public static function createBreakoutResponse($url){
		$response =  Customweb_Core_Http_Response::_(
				'<script type="text/javascript">
				top.location.href = "' . $url . '";
			</script>
		
			<noscript>
				<a class="button btn payengine-continue-button submit" href="' . $url . '"
				target="_top">' . Customweb_I18n_Translation::__('Continue') . '</a>
			</noscript>');
		$response->appendHeader('cache-control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0');
		return $response;
	}
	
	
}