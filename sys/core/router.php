<?php 

$fullUrl = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$fullUrl = urldecode($fullUrl);

# remove quotes
$fullUrl = preg_replace('/[\'"]/i','',$fullUrl);

# remove http/s
$baseurl = preg_split("#http(|s)://#", BASEURL);
$baseurl = $baseurl[1];

# remove index.php
$fullUrl = str_replace($baseurl . '/index.php', $baseurl, $fullUrl);

# get queries
$queryUrl = substr($fullUrl, strlen($baseurl));

$queryUrl = explode('/',trim($queryUrl, '/'));

# remove empty values
$queryUrl = array_filter($queryUrl);

# rearrange query array index numerically
$queryUrl = array_values($queryUrl);

/* determine module */

# set the default controller
$controller = $default_controller;

# check if requested controller is valid
if(isset($queryUrl[0])){

	# set first query as controller, if controller exists
	if(file_exists(CONTROLLERS . $queryUrl[0] . '.php')){

		$controller = $queryUrl[0];

	}

	# remove the first query if equal to controller
	if($queryUrl[0] == $controller){

		unset($queryUrl[0]);

		$queryUrl = array_values($queryUrl);

	}

}

# include the controller
include CONTROLLERS . $controller . '.php';

/* determine method */

# set the default method
$method = $default_method;

# check if requested method exists
if(isset($queryUrl[0])){

	# set the first query as method, if the method requested exists
	if(method_exists($controller, $queryUrl[0])){

		$method = $queryUrl[0];

	}
	
	# remove the first query if equal to method
	if($queryUrl[0] == $method){

		unset($queryUrl[0]);

		$queryUrl = array_values($queryUrl);

	}
}

?>