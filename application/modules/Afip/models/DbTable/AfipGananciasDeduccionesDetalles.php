<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_AfipGananciasDeduccionesDetalles
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipGananciasDeduccionesDetalles extends Rad_Db_Table
{
    protected $_name = 'AfipGananciasDeduccionesDetalles';

    protected $_sort = array('Periodo desc','Deduccion asc');

    protected $_referenceMap = array(
        'AfipGananciasDeduccionesPeriodos' => array(
            'columns'           => 'Periodo',
            'refTableClass'     => 'Afip_Model_DbTable_AfipGananciasDeduccionesPeriodos',
            'refJoinColumns'    => array('Id','FechaDesde','FechaHasta'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipGananciasDeduccionesPeriodos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'AfipGananciasDeducciones' => array(
            'columns'           => 'Deduccion',
            'refTableClass'     => 'Afip_Model_DbTable_AfipGananciasDeducciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipGananciasDeducciones',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_dependentTables = array();
}