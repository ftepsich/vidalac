<?php
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library') , get_include_path())));

define('RAD_GRIDDATAGATEWAY_MODEL_INI_PATH','/modules/default/models/DbTable/');
define("quote",'"');

// Muy importante ver que las funciones de fecha esten retornando el valor correcto
date_default_timezone_set('America/Argentina/Buenos_Aires');

require_once 'Rad/ErrorHandler.php';
//inicializo el manejador de errores
Rad_ErrorHandler::init();