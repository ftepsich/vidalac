<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_LiquidacionesLog * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Model_DbTable_LiquidacionesLog extends Rad_Db_Table
{
    protected $_name = 'LiquidacionesLog';

    protected $_referenceMap = array(
        
    'Liquidaciones' => array(
        'columns'           => 'IdLiquidacion',
        'refTableClass'     => 'Model_DbTable_Liquidaciones',
        'refJoinColumns'    => array('i'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Liquidaciones',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
,
    'Usuarios' => array(
        'columns'           => 'Usuario',
        'refTableClass'     => 'Model_DbTable_Usuarios',
        'refJoinColumns'    => array('Nombre'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Usuarios',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
    );

    protected $_dependentTables = array();
}