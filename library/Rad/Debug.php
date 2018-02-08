<?php

/**
 * Rad_Debug
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage App
 * @author Martin Alejandro Santangelo
 */

/**
 * Clase Rad_Debug
 * 
 * Funcionalidad para debug y profiling de la app
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage App
 * @author Martin Alejandro Santangelo
 */
class Rad_Debug
{
    public function init()
    {
        register_shutdown_function(array(__CLASS__, 'shutdown'));
    }

    public static function debugException($e)
    {
        $trace = Rad_Debug_Formatter::displayException($e);
        
        // creo un nuevo request pq puede no venir del MVC
        $req = new Zend_Controller_Request_Http;

        self::_sendDebug($req, $trace, $e->getMessage());
    }

    public static function debugPhpError($e)
    {
        $trace = Rad_Debug_Formatter::displayError($e);
        
        // creo un nuevo request pq puede no venir del MVC
        $req = new Zend_Controller_Request_Http;

        self::_sendDebug($req, $trace, $error['message']);

    }

    protected static function _sendDebug($req, $db, $title = null)
    {
        $errorNamespace = new Zend_Session_Namespace('Rad_Error_Handler');

        if (!isset($errorNamespace->count) || !$errorNamespace->count) {
            $errorNamespace->count = 1;
        } else {
            $errorNamespace->count++;
        }          

        $name = "E$errorNamespace->count";

        $errorNamespace->$name = $db;

        if ($req->isXmlHttpRequest()) {

            if (!headers_sent()) 
                header('HTTP/1.1 501');

            $output = array(
                'error' => $name,
                'title' => $title,
                'date'  => date('Y-m-d H:i:s')
            );

            // ver por que tira error de que no esta inicializado (por lo menos por direct)
            // Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();
            
            die(json_encode($output));

        } else {
            if (!headers_sent()) header('HTTP/1.1 500');
            
            die("Error: $title <a href='/error/getajaxerror/number/$name'>traza</a>");
        }
    }

    /**
     * Corta la ejecucion y muestra la traza en peticiones comunes o ajax 
     */
    public static function showTrace($title)
    {
        $trace = debug_backtrace();

        $html = Rad_Debug_Formatter::displayTrace($trace, $title);

        // creo un nuevo request pq puede no venir del MVC
        $req = new Zend_Controller_Request_Http;

        self::_sendDebug($req, $html);
    }

    public static function shutdown()
    {

    }
}