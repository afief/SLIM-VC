<?php

$app->get("/", function() use ($app) {

	$app->render("home.php", array("data" => "Afief YR"));

});

?>