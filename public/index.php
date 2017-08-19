<?php
require 'Common.php';

error_reporting(E_ALL ^ (E_NOTICE|E_WARNING|E_STRICT));

/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

// Si sos Martin
//if ($_SERVER['REMOTE_ADDR'] == '10.108.100.112') {
//    $options = $application->getOptions();
//
//    $options['resources']['db']['params']['dbname'] = 'vidalac';
//
//    $application->setOptions($options);
//}
$application->bootstrap()->run();
