<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Afip_Model_DbTable_AfipMonedas * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipMonedas extends Rad_Db_Table
{
    protected $_name = 'AfipMonedas';

    protected $_sort = array("Descripcion asc");

    protected $_referenceMap = array();

    protected $_dependentTables = array('Base_Model_DbTable_TiposDeDivisas');
}