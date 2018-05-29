<?php 
require_once 'Zend/Exception.php';

/**
 * Rad_Exception
 *
 * @category   Rad
 * @package    Rad_Exception
 * @copyright  Copyright (c) 2009 Smart Software
 */
class Rad_Exception extends Zend_Exception
{
    static public $genericError = null;
         
    /**
     * handleSessionStartError() - interface for set_error_handler()
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1325
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
    static public function handlePhpInternalErrors($errno, $errstr, $errfile, $errline, $errcontext)
    {
        self::$genericError = $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
    }
}