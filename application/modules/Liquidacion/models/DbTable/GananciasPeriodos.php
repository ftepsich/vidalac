<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_GananciasPeriodos * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_GananciasPeriodos extends Rad_Db_Table
{
    protected $_name = 'GananciasPeriodos';

    protected $_referenceMap = array(
            );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasGananciasDeducciones',
                                        'Rrhh_Model_DbTable_PersonasGananciasLiquidaciones');
}