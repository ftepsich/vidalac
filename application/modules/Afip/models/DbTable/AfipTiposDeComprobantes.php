<?php
/**
 * @package     Aplicacion
 * @subpackage  Afip
 * @class       Afip_Model_DbTable_AfipTiposDeComprobantes
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipTiposDeComprobantes extends Rad_Db_Table
{
    protected $_name = 'AfipTiposDeComprobantes';

    protected $_sort = array("Descripcion desc");

    protected $_referenceMap = array();

    protected $_dependentTables = array('Facturacion_Model_DbTable_TiposDeComprobantes');
}