<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_AfipGananciasDeduccionesTipos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipGananciasDeduccionesTipos extends Rad_Db_Table
{
    protected $_name = 'AfipGananciasDeduccionesTipos';

    protected $_sort = array('Id desc');

    protected $_referenceMap = array();

    protected $_dependentTables = array('Afip_Model_DbTable_AfipGananciasDeducciones');
}