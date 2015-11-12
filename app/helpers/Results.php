<?php

namespace helpers;

use Core;

Class Results {

	public $result = false;
	public $data;
	public $message = "";
	public $props = false;
	public $error = "";
	public $showError = true;

	function __construct() {
		$this->reset();
	}
	public function reset() {
		$this->result = false;
		$this->data = null;
	}
	public function setTrue() {
		$this->result = true;
	}
	public function setFalse() {
		$this->result = false;
	}

	public function setMessage($msg = "") {
		$this->message = $msg;
	}

	public function addData($key, $val = "") {
		if (!is_array($this->data)) {
			$temp = $this->data;
			$this->data = array();
			if ($temp)
				$this->data["default"] = $temp;
		}
		$this->data[$key] = $val;
	}
	public function setData($val) {
		$this->data = $val;
	}
	public function prop($key, $value = "") {
		if ($this->props === false)
			$this->props = array();

		$this->props[$key] = $value;
	}
	public function json() {
		$p = array();
		$p['status'] = $this->result;

		if ($this->data)
			$p['data'] = $this->data;

		if ($this->message !== "")
			$p['message'] = $this->message;

		if ($this->showError && ($this->error != ""))
			$p['error'] = $this->error;

		if ($this->props !== false) {
			foreach ($this->props as $key => $value) {
				$p[$key] = (is_numeric($value)?(float) $value: $value);
			}
		}

		return json_encode($p);
	}

}

?>