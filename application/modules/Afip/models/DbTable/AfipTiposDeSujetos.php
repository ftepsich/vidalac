<?php
/**
 * @package     Aplicacion
 * @subpackage  Afip_
 * @class       Afip_Model_DbTable_AfipTiposDeSujetos * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipTiposDeSujetos extends Rad_Db_Table
{
    protected $_name = 'AfipTiposDeSujetos';

    protected $_referenceMap = array();

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Base_Model_DbTable_AfipCuitPaises','Base_Model_DbTable_PaisesCuit');
}