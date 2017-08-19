<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_AfipGananciasEscalas
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipGananciasEscalas extends Rad_Db_Table
{
    protected $_name = 'AfipGananciasEscalas';

    protected $_sort = array('AfipEscalaPeriodo desc','Desde asc');

    protected $_referenceMap = array(
        'AfipGananciasEscalasPeriodos' => array(
            'columns'           => 'AfipEscalaPeriodo',
            'refTableClass'     => 'Afip_Model_DbTable_AfipGananciasEscalasPeriodos',
            'refJoinColumns'    => array('Id','FechaDesde','FechaHasta'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipGananciasEscalasPeriodos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
    );

    protected $_dependentTables = array();





}