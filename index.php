<?php 
	
	session_start();
	require_once "vendor/autoload.php";

	use \Slim\Slim;

	$app = new Slim();
	$app->config('debug', true);

	require_once "routes".DIRECTORY_SEPARATOR."functions.php";
	require_once "routes".DIRECTORY_SEPARATOR."admin.php";
	require_once "routes".DIRECTORY_SEPARATOR."admin-users.php";
	require_once "routes".DIRECTORY_SEPARATOR."admin-products.php";
	require_once "routes".DIRECTORY_SEPARATOR."admin-categories.php";
	require_once "routes".DIRECTORY_SEPARATOR."admin-orders.php";
	require_once "routes".DIRECTORY_SEPARATOR."site.php";
	require_once "routes".DIRECTORY_SEPARATOR."site-cart.php";
	require_once "routes".DIRECTORY_SEPARATOR."site-orders.php";
	require_once "routes".DIRECTORY_SEPARATOR."site-profile.php";

	$app->run();

 ?>