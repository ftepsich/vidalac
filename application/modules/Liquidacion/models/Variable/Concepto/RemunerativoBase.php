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
class Liquidacion_Model_Variable_Concepto_RemunerativoBase extends Liquidacion_Model_Variable_Concepto_Remunerativo
{
    private static $_sumbase = 0;

    public static function initSum()
    {
        self::$_sumbase = 0;
        parent::initSum();
    }

    public static function getSum()
    {
        return self::$_sumbase;
    }

    protected function _calcular($evaluador, $formula)
    {
        $v = parent::_calcular($evaluador, $formula);
        self::$_sumbase += $v;
        return $v;
    }
}