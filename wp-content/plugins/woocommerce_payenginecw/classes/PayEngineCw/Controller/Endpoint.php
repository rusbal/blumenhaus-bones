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
require_once 'PayEngineCw/EndpointAdapter.php';
require_once 'PayEngineCw/Util.php';
require_once 'Customweb/Core/Http/Response.php';
require_once 'Customweb/Payment/Endpoint/Dispatcher.php';
require_once 'PayEngineCw/Controller/Abstract.php';


/**
 *
 * @author Nico Eigenmann
 *
 */
class PayEngineCw_Controller_Endpoint extends PayEngineCw_Controller_Abstract {

	public function indexAction() {
		$container = PayEngineCw_Util::createContainer();
		$packages = array(
			0 => 'Customweb_PayEngine',
 			1 => 'Customweb_Payment_Authorization',
 		);
		$adapter = new PayEngineCw_EndpointAdapter();
		
		$dispatcher = new Customweb_Payment_Endpoint_Dispatcher($adapter, $container, $packages);
		$response = $dispatcher->dispatch(PayEngineCw_ContextRequest::getInstance());
		$wrapper = new Customweb_Core_Http_Response($response);
		$wrapper->send();
		die();
	}
	
}