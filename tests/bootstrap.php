<?php
// saco los notice y warnings
//error_reporting(E_ALL ^ (E_NOTICE|E_WARNING));
//error_reporting(E_ALL ^ (E_NOTICE|E_STRICT));
error_reporting(E_ERROR);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));


require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance()
    ->registerNamespace("Rad")
    ->registerNamespace("PHPUnit");

Zend_Session::$_unitTestEnabled = true;