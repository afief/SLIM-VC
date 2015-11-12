<?php

namespace models;

use Core;

class User {

	private $db;
	private $app;

	function __construct() {
		$this->app = Core::coreInstance();
		$this->db = DBCore::getInstance()->db;		
	}

}

?>
