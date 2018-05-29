<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_GananciasConceptosTipos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_GananciasConceptosTipos extends Rad_Db_Table
{
    protected $_name = 'GananciasConceptosTipos';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap = array();

    protected $_dependentTables = array('Liquidacion_Model_DbTable_GananciasConceptos');
}