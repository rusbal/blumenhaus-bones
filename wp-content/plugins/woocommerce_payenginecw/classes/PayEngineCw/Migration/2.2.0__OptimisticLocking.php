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
require_once 'Customweb/Database/Migration/IScript.php';

class PayEngineCw_Migration_2_2_0 implements Customweb_Database_Migration_IScript {

	public function execute(Customweb_Database_IDriver $driver){
		global $wpdb;
		
		$entityManager = PayEngineCw_Util::getEntityManager();
		
		$tableNameTransaction = $entityManager->getTableNameForEntityByClassName('PayEngineCw_Entity_Transaction');
		
		$driver->query("ALTER TABLE `" . $tableNameTransaction . "` ADD COLUMN  `versionNumber` int NOT NULL")->execute();
		$driver->query("ALTER TABLE `" . $tableNameTransaction . "` ADD COLUMN  `liveTransaction` char(1)")->execute();
		
		$tableNameECC = $entityManager->getTableNameForEntityByClassName('PayEngineCw_Entity_ExternalCheckoutContext');
		$driver->query("ALTER TABLE `" . $tableNameECC . "` ADD COLUMN  `versionNumber` int NOT NULL")->execute();
		
		$tableNamePCC = $entityManager->getTableNameForEntityByClassName('PayEngineCw_Entity_PaymentCustomerContext');
		$driver->query("ALTER TABLE `" . $tableNamePCC . "` ADD COLUMN  `versionNumber` int NOT NULL")->execute();
		
		return true;
	}
}