<?php
/**
 * @package     Aplicacion
 * @subpackage  Rrhh
 * @class       Model_DbTable_ServiciosHorasTrabajadas
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_ServiciosHorasTrabajadas extends Rad_Db_Table
{
    protected $_name = 'ServiciosHorasTrabajadas';

    protected $_validators = array(
        'LiquidacionPeriodo' => array(
            array(
                'Db_NoRecordExists',
                'ServiciosHorasTrabajadas',
                'LiquidacionPeriodo',
                'Id<>{Id} AND Servicio={Servicio}'
            ),
            'messages' => array('Ya hay horas cargadas para este periodo.')

        ),
        'CantidadHoras' => array(
            array('GreaterThan', 0),
            'messages' => array('Debe ser mayor que Cero')
        )
    );

    protected $_referenceMap = array(
        'LiquidacionesPeriodos' => array(
            'columns'           => 'LiquidacionPeriodo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesPeriodos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Servicios',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array();
}