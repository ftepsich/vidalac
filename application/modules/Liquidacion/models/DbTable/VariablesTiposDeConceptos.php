<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_VariablesTiposDeConceptos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_VariablesTiposDeConceptos extends Rad_Db_Table
{
    protected $_name = 'VariablesTiposDeConceptos';

    protected $_sort = array('Id asc');

    protected $_referenceMap = array(
            );

    protected $_dependentTables = array('Model_DbTable_Variables');
}