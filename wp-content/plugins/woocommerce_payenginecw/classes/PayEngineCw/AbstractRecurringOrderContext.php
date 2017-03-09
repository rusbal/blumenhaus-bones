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
require_once 'PayEngineCw/OrderContext.php';

class PayEngineCw_AbstractRecurringOrderContext extends PayEngineCw_OrderContext {
	
	private $initialOrderId;
	
	private $initialTransactionId;
	
	public function __construct($order, $paymentMethod, $amountToCharge){
		parent::__construct($order, $paymentMethod);
		$this->orderAmount = $amountToCharge;	
	}
	
	
	protected function setInitialOrderId($initialOrderId){
		$this->initialOrderId = $initialOrderId;
		return $this;
	}
	
	
	public function getInitialOrderId(){
		return $this->initialOrderId;
	}
	
	protected function setInitialTransactionId($initialTransactionId){
		$this->initialTransactionId = $initialTransactionId;
		return $this;
	}
	
	public function getInitialTransactionId(){
		return $this->initialTransactionId;
	}
}