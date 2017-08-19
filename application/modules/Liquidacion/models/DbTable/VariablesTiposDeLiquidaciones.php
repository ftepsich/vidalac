<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Liquidacion_Model_DbTable_VariablesTiposDeLiquidaciones * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_VariablesTiposDeLiquidaciones extends Rad_Db_Table
{
    protected $_name = 'VariablesTiposDeLiquidaciones';

    protected $_referenceMap = array(
        'TiposDeLiquidaciones' => array(
            'columns'           => 'TipoDeLiquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeLiquidaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeLiquidaciones',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Variables' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Variables',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Variables',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'ConceptosLiquidaciones' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Variables',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'ConceptosLiquidacionesExtras' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Variables_ConceptosLiquidacionesExtras',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Variables',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array();
}