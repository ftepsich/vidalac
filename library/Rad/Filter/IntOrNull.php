<?php
/**
 * Rad_Filter_IntOrNull
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Filter
 * @author Martin Alejandro Santangelo
 */
require_once 'Zend/Filter/Interface.php';


/**
 * Rad_Filter_IntOrNull
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Filter
 * @author Martin Alejandro Santangelo
 */
class Rad_Filter_IntOrNull implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns (int) $value
     *
     * @param  string $value
     * @return integer
     */
    public function filter($value)
    {
        if (((string)$value) == '') {
            return null;   
        }
        return (int) ((string) $value);
    }
}