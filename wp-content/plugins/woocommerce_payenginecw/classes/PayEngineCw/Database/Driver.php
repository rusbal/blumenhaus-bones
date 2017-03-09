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
require_once 'Customweb/Database/Driver/AbstractDriver.php';
require_once 'PayEngineCw/Database/Statement.php';

final class PayEngineCw_Database_Driver extends Customweb_Database_Driver_AbstractDriver {
	
	/**
	 * @var wpdb
	 */
	private $link;
	
	/**
	 * The resource link is the connection link to the database.
	 *
	 * @param resource $resourceLink
	 */
	public function __construct(wpdb $wpdb){
		$this->link = $wpdb;
	}
	
	public function beginTransaction(){
		$this->query("START TRANSACTION;");
		$this->setTransactionRunning(true);
	}
	
	public function commit(){
		$this->query("COMMIT;");
		$this->setTransactionRunning(false);
	}
	
	public function rollBack(){
		$this->query("ROLLBACK;");
		$this->setTransactionRunning(false);
	}
	
	public function query($query){
		$statement = new PayEngineCw_Database_Statement($query, $this);
		return $statement;
	}
	
	public function quote($string){
		if (method_exists($this->link, '_real_escape')) {
			$string = $this->link->_real_escape($string);
		}
		elseif(function_exists('esc_sql')) {
			$string = esc_sql($string);
		}
		else{
			$string = $this->link->escape($string);
		}
		
	
		return '"' . $string . '"';
	}
	
	public function getLink(){
		return $this->link;
	}
	
}