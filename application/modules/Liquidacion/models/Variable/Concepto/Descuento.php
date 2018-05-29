<?php

/**
 * Liquidacion_Model_Variable
 * 
 * Representa a todas las variables del liquidador de sueldo
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Variable_Concepto_Descuento extends Liquidacion_Model_Variable_Concepto
{
    protected function _calcular($evaluador, $formula)
    {
        return -1*parent::_calcular($evaluador, $formula);
    }
}