<?php
/**
 * Clase abstracta de post proceso de liquidaciones
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Liquidador_PostProcess
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
abstract class Liquidacion_Model_Liquidador_PostProcess {
    abstract public function execute(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $agrupacion = null, $valor = null);
}