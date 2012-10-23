<?php if(!defined('SYSDIR')) exit();

/**

app
	- models
	- controllers
	- views
	- library
	- assets
		- css
		- js
		- images
		- uploads
		- db

**/

/* Global Config ************************************** */

	# Your website's url
	$site_url = 'http://localhost/crowdcode';	
	
	# Directory Config
	$dirs = array(
		
		'library' => 'library',
		
		'assets' => 'assets',
		
		'controllers' => 'controllers',
		
		'models' => 'models',
		
		'views' => 'views',
		
		'tmp' => 'tmp',
		
		'cache' => 'cache',
	);
	
	# Default Controller
	$default_controller = 'index';	
	
	# Default Method
	$default_method = 'index';
	
/* ******************************************************** */
	
/* Template Config ************************************ */
	
	# Template Engine
	$tplEngine = 'template';
	
	# Template Extension
	$tplExtension = '.php';

	# Remove undefined template variables
	$removeUndefined = true;
	
/* ******************************************************** */	

/* Cache Config ************************************ */
	
	# Cache Engine
	$cacheEngine = 'cache';
	
	# Cache Extension
	$cacheExtension = '.cache';	
	
	# Cache Hash
	$cacheHash = 'md5';
	
/* ******************************************************** */

/* Database Config ************************************ */

	# Database Server
	$dbServer = 'localhost';
	
	# Database Username
	$dbUser = 'root';
	
	# Database Password
	$dbPass = 'asdf';
	
	# Database Name
	$dbName = 'voting';
	
	# Database Driver
	$dbDriver = 'mysql';
	
/* ******************************************************** */

/* URL Handler ************************************ */

	$urlHandler = 'url';

/* ******************************************************** */
?>