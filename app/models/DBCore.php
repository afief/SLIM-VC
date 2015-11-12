<?php

namespace models;

use medoo;

class DBCore {

	public $db;
	private static $instance;

	function __construct() {
		$this->db = new medoo(array(
			'database_type' => 'mysql',
			'database_name' => DATABASE_NAME,
			'server' => DATABASE_SERVER,
			'username' => DATABASE_USERNAME,
			'password' => DATABASE_PASSWORD,
			'charset' => 'utf8'
			));
	}

	public static function getInstance() {
		if (!isset(self::$instance))
		{
			$object = __CLASS__;
			self::$instance = new $object;
		}
		return self::$instance;
	}

}

?>
