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
class Liquidacion_Model_Variable_Primitiva extends Liquidacion_Model_Variable
{
    protected static $_primitivas = array();

    protected static $instances;

    public function __construct($nombre, Liquidacion_Model_Periodo $periodo, $formula = null, $selector = null)
    {
        if ($formula && !is_callable($formula)) {
            throw new Liquidacion_Model_Variable_Exception('El parametro formula de una primitva debe ser un callback');
        }

        parent::__construct(null, $nombre, '', '', $periodo, $formula, $selector);
    }

    // la formula se obtiene de ejecutar la funcion correspondiente
    public function getFormula($servicio)
    {
        $f = $this->_formula;
        return $f($servicio, $this->_periodo);
    }
}
