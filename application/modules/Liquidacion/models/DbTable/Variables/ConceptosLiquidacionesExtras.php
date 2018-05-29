<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_Variables_ConceptosLiquidacionesExtras
 *
 * Conceptos de liquidaciones Extras
 *
 * Se ejecutan luego de calculados los demas conceptos
 *
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones
 * @extends Liquidacion_Model_DbTable_VariablesAbstractas
 */
class Liquidacion_Model_DbTable_Variables_ConceptosLiquidacionesExtras extends Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones
{

    protected $_permanentValues  = array(
        'TipoDeVariable'    => 5,
        'VariableCategoria' => 1
    );
    protected $_defaultValues    = array(
        'TipoDeVariable'    => 5,
        'VariableCategoria' => 1,
        'Desactivado'       => 0
    );

    protected $_dependentTables = array(
        'Liquidacion_Model_DbTable_VariablesTiposDeLiquidaciones'
    );
}