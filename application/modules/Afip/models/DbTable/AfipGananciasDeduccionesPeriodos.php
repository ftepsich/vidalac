<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_AfipGananciasDeduccionesPeriodos 
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipGananciasDeduccionesPeriodos extends Rad_Db_Table
{
    protected $_name = 'AfipGananciasDeduccionesPeriodos';

    protected $_sort = array('FechaHasta desc');

    protected $_referenceMap = array();

    protected $_dependentTables = array('Afip_Model_DbTable_AfipGananciasDeduccionesDetalles');
}