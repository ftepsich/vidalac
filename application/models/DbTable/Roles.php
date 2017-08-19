<?php
require_once('Rad/Db/Table.php');

/**
 * Model_DbTable_Roles
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Model_DbTable_Roles
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_Roles extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "Roles";
        
    protected $_sort = array("Descripcion Asc");

	// Relaciones
	
	protected $_dependentTables = array('Model_DbTable_RolesModelos','Model_DbTable_RolesModulos','Model_DbTable_Usuarios');	
}