<?php

/**
 * Liquidacion_Model_Variable
 *
 * Variables que ejecutan sql para obtener la formula
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Variable_Parametro extends Liquidacion_Model_Variable
{
    public function __construct($nombre, Liquidacion_Model_Periodo $periodo, $formula = null)
    {
        if (!$formula) {
            throw new Liquidacion_Model_Variable_Exception("El parametro `$nombre` debe tener un valor asignado.");
        }
        parent::__construct(0, $nombre, '', '', $periodo, $formula);
    }
}