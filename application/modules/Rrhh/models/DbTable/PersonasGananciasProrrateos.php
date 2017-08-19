<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasProrrateos 
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasProrrateos extends Rad_Db_Table
{
    protected $_name = 'PersonasGananciasProrrateos';

    protected $_referenceMap = array(
        
    'LiquidacionesRecibos' => array(
        'columns'           => 'LiquidacionRecibo',
        'refTableClass'     => 'Model_DbTable_LiquidacionesRecibos',
        'refJoinColumns'    => array('e'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'LiquidacionesRecibos',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
,
    'Meses1' => array(
        'columns'           => 'MesHasta',
        'refTableClass'     => 'Model_DbTable_Meses',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Meses',
        'refColumns'        => 'Id',
        'comboPageSize'     => '12'
    )
,
    'Meses2' => array(
        'columns'           => 'MesDesde',
        'refTableClass'     => 'Model_DbTable_Meses',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Meses',
        'refColumns'        => 'Id',
        'comboPageSize'     => '12'
    )
,
    'Meses3' => array(
        'columns'           => 'MesInicioImputacion',
        'refTableClass'     => 'Model_DbTable_Meses',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Meses',
        'refColumns'        => 'Id',
        'comboPageSize'     => '12'
    )    
    );

    protected $_dependentTables = array();
}