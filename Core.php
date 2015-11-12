<?php

class Core extends \Slim\Slim
{
	public static $instance;

	private $arHelpers = array();

	function __construct($args = array()) {
		\Slim\Slim::__construct($args);

		self::$instance = $this;
	}
	public static function coreInstance() {
		if (!isset(self::$instance))
		{
			$object = __CLASS__;
			self::$instance = new $object;
		}
		return self::$instance;
	}

	public function loadLibrary($name) {
		if (file_exists(FOLDER_LIBS . "/" . $name . ".php"))
			require_once FOLDER_LIBS . "/" . $name . ".php";
	}
	public function loadModel($name, $key = false) {
		require_once FOLDER_MODELS . '/' . $name . ".php";
		if ($key) {
			$name = "\\models\\" . $name;
			$this->$key = new $name();
		}
	}
	public function loadHelper($name, $key = false) {
		if (array_search($name, $this->arHelpers) === false) {

			require_once FOLDER_HELPERS . '/' . $name . '.php';
			array_push($this->arHelpers, $name);

			if ($key) {
				$name = "\\helpers\\" . $name;
				$this->$key = new $name();			
			}
		}
	}

	public function getPosts($args = false) {
		if ($args) {
			$post = $this->request->post();
			foreach ($args as $arg) {
				if (!isset($post[$arg])) {
					return false;
				}
			}
		}
		return $this->request->post();
	}

	public function getHeaders($dat) {
		if ($dat != "")
			return $this->request->headers->get($dat);
		return $this->request->headers;
	}

	public function render($template, $data = array(), $status = null) {
		//error_reporting(E_ALL & ~E_NOTICE);

		$data["app"] = $this;

		\Slim\Slim::render($template, $data);
	}

	public function redirect($url, $status = 302) {
		$url = BASE_URL . $url;
		\Slim\Slim::redirect($url, $status);
	}
}

?>
