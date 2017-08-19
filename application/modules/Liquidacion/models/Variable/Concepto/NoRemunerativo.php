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
class Liquidacion_Model_Variable_Concepto_NoRemunerativo extends Liquidacion_Model_Variable_Concepto
{
    private static $_sum = 0;

    public static function initSum()
    {
        self::$_sum = 0;
        parent::initSum();
    }

    public static function getSum()
    {
        return self::$_sum;
    }

    protected function _calcular($evaluador, $formula)
    {
        $v = parent::_calcular($evaluador, $formula);
        self::$_sum += $v;
        return $v;
    }
}