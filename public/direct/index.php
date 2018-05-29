<?php
/**
 * Punto de entrada para acceder a los modelos usando Ext.Direct
 * @author Martin A. Santangelo
 * @copyright SmartSoftware 2010
 */
require '../Common.php';

/**
 *  Analizamos la peticion para obtener el modelo al que esta intentando acceder
 */
$baseUri = str_replace('?javascript','',$_SERVER['REQUEST_URI']);

//$uri = str_replace ('_', '', substr($baseUri,1));
$uri = substr($baseUri,1);
@list( $controller, $module, $model ) = explode( '/', $uri, 3 );

if (!$module && !$model) {
    throw new Exception('Peticion Erronea');
}
if (!$model) {
    $class = 'Model_'.$module.'Mapper';
} else {
    $class = $module.'_Model_'.$model.'Mapper';
}

/** Zend_Application **/
require_once 'Zend/Application.php';
require 'ExtDirect.php';
require 'Zend/Loader.php';
require_once 'Rad/ConfirmationException.php';


// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->getBootstrap()
            ->bootstrap('Registry')
            ->bootstrap('AutoLoad')
            ->bootstrap('PubSub')
            ->bootstrap('FastCache')
            ->bootstrap('SlowCache')
            ->bootstrap('Modules')
            ->bootstrap('DbProfiler');

//$loader = Zend_Loader_Autoloader::getInstance();


/**
 * Verifica si el usuario tiene permiso de acceso a este modelo
 *
 * @param ExtDirectAction $action
 * @return boolean
 */
function checkACl($action)
{
    if (! Zend_Auth::getInstance()->hasIdentity()) {
        throw new Exception('relogin');
    }
    $identity = Zend_Auth::getInstance()->getIdentity();
    // role is a column in the user table (database)

    if ($identity->GrupoDeUsuario != 1) {
        try {

            if (method_exists($action->instance, 'authorize')) {

                $isAllowed = $action->instance->authorize($identity);
            } else {
                $dbAdapter = Zend_Registry::get('db');

                $acl = Rad_Acl::getInstance($dbAdapter);

                $isAllowed = $acl->isAllowed(
                    $identity->GrupoDeUsuario,
                    $action->action,
                    $action->method
                );
            }

        } catch (Exception $e) {
            $isAllowed = false;
        }
    } else { // Si es Admin siempre tiene permiso
        $isAllowed = true;
    }
    return $isAllowed;
}


ExtDirect::$debug = (APPLICATION_ENV == 'development');
if (APPLICATION_ENV != 'development') ExtDirect::$cache = Zend_Registry::get('fastCache');
//ExtDirect::$count_only_required_params = true;
ExtDirect::$include_static_methods     = true;
ExtDirect::$include_inherited_methods  = true;
ExtDirect::$authorization_function     = 'checkACl';
ExtDirect::$url = $baseUri;

ExtDirect::provide( $class );
