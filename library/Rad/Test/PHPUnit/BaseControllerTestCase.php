<?php

/**
 * Rad_Test_PHPUnit_BaseControllerTestCase
 *
 * Clase base para el testing de controladores del sistema
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Test
 * @author Martin Alejandro Santangelo
 */
class Rad_Test_PHPUnit_BaseControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }
}