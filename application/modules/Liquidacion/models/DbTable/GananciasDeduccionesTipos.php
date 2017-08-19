<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_GananciasDeduccionesTipos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_GananciasDeduccionesTipos extends Rad_Db_Table
{
    protected $_name = 'GananciasDeduccionesTipos';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap = array();

    protected $_dependentTables = array('Liquidacion_Model_DbTable_GananciasConceptos');
}