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
require_once 'Customweb/Database/Driver/AbstractStatement.php';

final class PayEngineCw_Database_Statement extends Customweb_Database_Driver_AbstractStatement {
	
	/**
	 * @var result resource
	 */
	private $result;
	
	private $rowCount = 0;
	
	/**
	 * @return PayEngineCw_Database_Driver
	 */
	public function getDriver(){
		return parent::getDriver();
	}
	
	/**
	 * @return wpdb
	 */
	private function getLink() {
		return $this->getDriver()->getLink();
	}
	
	public function getInsertId() {
		$this->executeQuery();
		return $this->getLink()->insert_id;
	}
	
	public function getRowCount() {
		$this->executeQuery();
		return $this->rowCount;
	}
	
	public function fetch() {
		$this->executeQuery();
		if ($this->result === false) {
			return false;
		}
		else {
			$rs = current($this->result);
			if ($rs === false) {
				return false;
			}
			else {
				next($this->result);
				return $rs;
			}
		}
	}
	
	final protected function executeQuery() {
		
		
		if (!$this->isQueryExecuted()) {
			$this->getLink()->rows_affected = false;
			$this->getLink()->last_error = '';
			$this->getLink()->insert_id = false;
			$result = $this->getLink()->get_results($this->prepareQuery(), ARRAY_A);
			
			$this->rowCount = $this->getLink()->rows_affected;
			if($this->rowCount === 0) {
				$this->rowCount = $this->getLink()->num_rows;
			}
			
			$this->result = $result;
			reset($this->result);
			
	
			if ($this->getLink()->last_error !== '') {
				throw new Exception($this->getLink()->last_error);
			}
			$this->setQueryExecuted();
		}
	}
	
}