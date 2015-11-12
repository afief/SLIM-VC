<?php

header("Access-Control-Allow-Origin: *");

require __DIR__ . "/Slim/Slim.php";
require __DIR__ . "/Core.php";
Core::registerAutoloader();

/* prepare configuration */
if (!isset($app_libraries))
	$app_libraries = array();

if (!isset($app_models))
	$app_models = array();

if (!isset($app_helpers))
	$app_helpers = array();

/* Load All Configuration */
require __DIR__ . "/config.php";

/* Init Core */
$app = new Core(array(
	'templates.path' => FOLDER_VIEWS,
	'cookies.encrypt' => true,
	'cookies.lifetime' => "1 years",
	'cookies.path' => '/',
	'cookies.httponly' => true,
	'cookies.secret_key' => SALT
	));

/* Load Libraries */
foreach ($app_libraries as $lib) {
	$app->loadLibrary($lib);
}

/* Load Helpers */
foreach ($app_helpers as $key => $value) {
	$app->loadHelper($value, $key);
}


/* Load Models */
foreach ($app_models as $key => $mod) {
	$app->loadModel($mod, $key);
}

/* Load All Routers */
foreach ($routeFolders as $routeFolder) {
	$routers = glob(FOLDER_ROUTERS . "/" . $routeFolder . "*.php");
	foreach ($routers as $router) {
		require $router;
	}
}

/* Setup Common Const */
defined('CURL_SSLVERSION_DEFAULT') || define('CURL_SSLVERSION_DEFAULT', 0);
defined('CURL_SSLVERSION_TLSv1')   || define('CURL_SSLVERSION_TLSv1', 1);
defined('CURL_SSLVERSION_SSLv2')   || define('CURL_SSLVERSION_SSLv2', 2);
defined('CURL_SSLVERSION_SSLv3')   || define('CURL_SSLVERSION_SSLv3', 3);

$app->run();


?>
