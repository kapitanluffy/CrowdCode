<?php 

# System Directory
$system_directory = 'sys';

# Application Directory
$application_directory = 'app';

define('DS', DIRECTORY_SEPARATOR);

define('BASEDIR', pathinfo(__FILE__, PATHINFO_DIRNAME). DS);

define('SYSDIR', BASEDIR . rtrim( str_replace('\\', DS, $system_directory), DS ) . DS);

define('APPDIR', BASEDIR . rtrim( str_replace('\\', DS, $application_directory), DS ) . DS);

# Load Config FIle
include SYSDIR . 'config.php';

# Set Directories

# CC Library
define('SYSLIB' , SYSDIR . 'lib' . DS);

# Temp Folder
define('TMP' , APPDIR . $dirs['tmp'] . DS);

# Cache Folder
define('CACHE' , TMP . $dirs['cache'] . DS);

# App Library
define('APPLIB' , APPDIR . $dirs['library'] . DS);

# Assets Folder
define('ASSETS' , APPDIR . $dirs['assets'] . DS);

# Models Folder
define('MODELS' , APPDIR . $dirs['models'] . DS);

# Controllers Folder
define('CONTROLLERS' , APPDIR . $dirs['controllers'] . DS);

# Views Folder
define('VIEWS' , APPDIR . $dirs['views'] . DS);

include SYSDIR . 'core/utils.php';

include SYSDIR . 'core/interfaces.php';

include SYSDIR . 'core/core.php';

include SYSDIR . 'core/cloud.php';

define('BASEURL', rtrim($site_url, '/'));

include SYSDIR . 'core/router.php';

include SYSDIR . 'startup.php';

$controller = new $controller;

# Call the page
$object = array($controller, $method);

call_user_func_array($object, $queryUrl);

?>