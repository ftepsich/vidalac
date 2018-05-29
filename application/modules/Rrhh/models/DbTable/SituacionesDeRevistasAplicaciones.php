<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_SituacionesDeRevistasAplicaciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_SituacionesDeRevistasAplicaciones extends Rad_Db_Table
{
    protected $_name = 'SituacionesDeRevistasAplicaciones';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap = array(
            );

    protected $_dependentTables = array('Rrhh_Model_DbTable_SituacionesDeRevistas');
}