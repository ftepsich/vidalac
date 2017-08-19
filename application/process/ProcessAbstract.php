<?php
/**
 * Clase base para crear scripts de linea de comando que utilicen el entorno de la app
 * 
 * @author Martín A. Santangelo
 */
abstract class ProcessAbstract
{
    /**
     * Inicializa la aplicacion, el loader, etc.
     */
    protected function _init()
    {

        // paso el working directory al que contiene este archivo para q no tiren error los includes
        chdir(dirname(__FILE__));

        ini_set('display_errors', 'stderr');

        define('APPLICATION_ENV', 'command');

        /** Zend_Application **/
        require_once 'Zend/Application.php';
        require 'Zend/Loader.php';
        require '../../public/Common.php';

        // Create application, bootstrap, and run
        $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $application->getBootstrap()
                    ->bootstrap('db')
                    ->bootstrap('Registry')
                    ->bootstrap('AutoLoad')
                    ->bootstrap('PubSub')
                    //->bootstrap('FastCache')
                    //->bootstrap('SlowCache')
                    ->bootstrap('Modules');

        // Zend Session no soporta el entorno CLI por lo que usamos este workaround para zafar
        Zend_Session::$_unitTestEnabled = true;

    }

    public function __construct()
    {
        $this->_init();
    }

    abstract public function run();
}