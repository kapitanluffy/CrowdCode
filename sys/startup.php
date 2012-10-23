<?php if(!defined('BASEDIR')) exit('No direct script access allowed');

$autoload = array(

	'session' => 'Session',
	'eh' => 'Error_Handler',
	'db' => $dbDriver,
	'tpl' => $tplEngine,
	'cache' => $cacheEngine,
	'url' => $urlHandler,

);

foreach( $autoload as $alias => $class ) {

	$autoload[ $alias ] = load_class( $class, $alias );

}

if( $autoload['db'] ) {

	load_class('db')->connect( $dbName, $dbPass, $dbUser, $dbServer );

}

# Template Engine
if($tplEngine != ''){
	load_class('tpl')->remove_undefined($removeUndefined);
}

# Cache Engine
if($cacheEngine != ''){

	load_class('cache')->set_hash_type($cacheHash);

	load_class('cache')->set_cache_ext($cacheExtension);

}

/* ******************************************************** */

?>