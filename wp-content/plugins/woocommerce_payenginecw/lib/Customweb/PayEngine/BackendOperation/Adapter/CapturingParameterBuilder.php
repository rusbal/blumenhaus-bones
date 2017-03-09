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

require_once 'Customweb/PayEngine/IAdapter.php';
require_once 'Customweb/Payment/Util.php';
require_once 'Customweb/PayEngine/MaintenanceParameterBuilder.php';


class Customweb_PayEngine_BackendOperation_Adapter_CapturingParameterBuilder extends Customweb_PayEngine_MaintenanceParameterBuilder {

	private $captureAmount = null;
	private $close = false;
	
	public function __construct(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_DependencyInjection_IContainer $container, $amount, $close) {
		parent::__construct($transaction, $container);
		
		$this->captureAmount = $amount;
		$this->close = $close;
	}

	protected function getOperationParameter() {
		if (Customweb_Payment_Util::amountEqual($this->getTransaction()->getAuthorizationAmount(), $this->captureAmount) || $this->close) {
			return array('OPERATION' => Customweb_PayEngine_IAdapter::OPERATION_CAPTURE_FULL);
		}
		else {
			return array('OPERATION' => Customweb_PayEngine_IAdapter::OPERATION_CAPTURE_PARTIAL);
		}
	}
	
	protected function getMaintenanceAmount() {
		return $this->captureAmount;
	}
}