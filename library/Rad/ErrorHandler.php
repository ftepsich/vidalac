<?php

/**
 * Rad_ErrorHandler
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage App
 * @author Martin Alejandro Santangelo
 */

/**
 * Clase Rad_ErrorHandler
 *
 * Manejo de errores genericos tanto para el MVC (llamado desde el Error Controller) como Direct,
 *
 * Captura los errores genericos de PHP y los transforma en excepciones para que sean capturados por el ErrorController
 *
 * Se encarga de enviar alertas segun el tipo de error y modo (produccion, desarrollo, etc)
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage App
 * @author Martin Alejandro Santangelo
 */
class Rad_ErrorHandler
{
    public static $ERROR_ALERT_ENABLED = true;

    /**
     * Manejador de errores fatales de PHP
     * @var array
     */
    public static $ERROR_HANDLER = array(__CLASS__, 'fatalErrorHandler');

    /**
     * Convierto los errores de php en Excepciones (excepto los errores fatales)
     */
    public static function exception_error_handler($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Manejo los errores fatales de PHP llamando a esta funcion durante el shutodown
     */
    public static function error_alert()
    {
        if(self::$ERROR_ALERT_ENABLED && is_null($e = error_get_last()) === false)
        {
            if ($e['type'] === E_ERROR || $e['type'] === E_PARSE) {
                call_user_func_array(self::$ERROR_HANDLER, array($e));
            }
        }

        $db = Zend_Registry::get('db');
        if ($db->getTransactionCount() > 0) throw new Exception('La transaccion no se comiteo!');
    }

    public static function fatalErrorHandler($e)
    {
        ob_clean();

        $msg1 = $e['message']."\n  Tipo: ".$e['type']."\n  Archivo: ".$e['file']."\n  Linea: ".$e['line'];

        // Logueo el Error critico
        Rad_Log::getLog()->crit($msg1);
        //print_r($e);
        if (APPLICATION_ENV == 'development') {
            Rad_Debug::debugPhpError($e);
        } else {
            self::sendError('Error en la aplicacíon');
        }
    }

    protected static function _sendConfirmationError($exception)
    {
        $msg = new stdClass();
        $msg->msg = addslashes($exception->getMessage());
        $msg->uid = $exception->getUid();
        $msg->options = $exception->getOptions();
        self::_sendJsonResponse($msg, 506);
    }

    public static function sendDenied($e = null)
    {
        if ($e) {
            self::_sendAndExit($e->getMessage(), 500);
        } else {
            self::_sendAndExit('Uds no tiene permiso', 500);
        }
    }

    /**
     * Envia la respuesta json sin romper los logs de firephp
     */
    protected static function _sendJsonResponse($data, $code = null)
    {
        $data = Zend_Json::encode($data, null, array('enableJsonExprFinder' => true));
        self::_sendAndExit($data, $code);
    }

    protected static function _sendAndExit($data, $code = null)
    {

        if ($code && !headers_sent()) header('HTTP/1.1 '.$code);

        // no puedo enviar la cabecera  json/javascript porque el formulario al tener fielUpload: true no funciona
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug

        echo $data;
        exit();
    }


    public static function handleException($e, $req = null)
    {
        // Confirmacion
        if ($e instanceof Rad_ConfirmationException) {
            self::_sendConfirmationError($e);
            return;
        }

        // Faltan permisos de acceso a un controlador?
        if ($e instanceof Rad_Exception_NotAllowed) {
            self::sendDenied($e);
            return;
        }

        // si es un error de Consutlta a la DB lo logueamos
        if ($e instanceof Zend_Db_Statement_Exception) {
            error_log(
                date('Y-m-d H:i:s').': '.$e->getMessage()."\nSQL: ".$e->sql."\n-------------------------------\n",
                3,
                APPLICATION_PATH.'/../logs/db-errors'
            );
        }

        if (APPLICATION_ENV == 'development') {
            Rad_Debug::debugException($e);
        } else {
            self::sendError($e->getMessage(), $req);
        }
    }

    /**
     * Envia el error al usuario se por ajax o peticion comun
     */
    public static function sendError($errorMsg, $req = null)
    {
        // Si es ejecutada en el entorno cli
        if(php_sapi_name() == "cli") {
            echo $errorMsg.PHP_EOL;
            return;
        }

        if ( !$req ) $req = new Zend_Controller_Request_Http;

        // is ajax request
        if ($req->isXmlHttpRequest() || $req->isPost()) {
            $errorMsg = addslashes($errorMsg);
            $msg = new stdClass();
            $msg->success = false;
            $msg->msg = $errorMsg;
            self::_sendJsonResponse($msg);
        }
        self::_sendAndExit($msg);
    }

    public static function init()
    {
        set_error_handler(array(__CLASS__, 'exception_error_handler'), E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
        register_shutdown_function(array(__CLASS__, 'error_alert'));
    }
}
