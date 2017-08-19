<?php

/**
 * Workflow
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */

/**
 * Workflow
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Pizarron
{
    protected $_datos = array();

    public function getValor($nombre)
    {
        return $this->_datos[$nombre]['valor'];
    }

    public function getTipo($nombre)
    {
        return $this->_datos[$nombre]['tipo'];
    }

    public function setTipo($nombre, $tipo)
    {
        $this->_datos[$nombre]['tipo']  = $tipo;
    }
    public function setValor($nombre, $valor)
    {
        $this->_datos[$nombre]['valor'] = $valor;

        Rad_Log::debug($this->_datos);
    }
}