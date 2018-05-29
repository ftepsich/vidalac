<?php
/**
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_DbTable_PersonasDeduccionesGanancias * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_PersonasDeduccionesGanancias extends Rad_Db_Table
{
    protected $_name = 'PersonasDeduccionesGanancias';

    protected $_referenceMap = array(  
        'LiquidacionesPeriodosFiscales' => array(
            'columns'           => 'PeriodoFiscal',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Valor','Anio'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesPeriodos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'DeduccionesGanancias' => array(
            'columns'           => 'DeduccionGanancia',
            'refTableClass'     => 'Liquidacion_Model_DbTable_DeduccionesGanancias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'DeduccionesGanancias',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Empleados' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'LiquidacionesPeriodosMensuales' => array(
            'columns'           => 'PeriodoMensual',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Valor','Anio'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesPeriodos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array();
}