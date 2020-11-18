<?php


	if(!defined("BASE_PATH")) {
		define("BASE_PATH",__DIR__);
	}


	$autoloader = BASE_PATH . '/vendor/autoload.php';
	
	if(file_exists($autoloader)) {
		require($autoloader);
	}