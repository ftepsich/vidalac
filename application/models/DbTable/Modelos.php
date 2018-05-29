<?php
require_once('Rad/Db/Table.php');

/**
 * Model_DbTable_Modelos
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Model_DbTable_Modelos
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_Modelos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "Modelos";
	protected $_sort = array("Descripcion Asc");

	// Relaciones
    protected $_referenceMap    = array(
            );

	protected $_dependentTables = array('Model_DbTable_ModulosModelos','Model_DbTable_RolesModelos');

	// public function updateFromTables()
	// {
	// 	$tablas  = $this->_db->fetchCol("SHOW TABLES");

	// 	$modelos = $this->_db->fetchCol("SELECT Descripcion FROM Modelos ");

	// 	foreach ($tablas as $tabla) {
	// 		if (!in_array($tabla, $modelos)) {
	// 			$data = array('Descripcion' => $tabla);

	// 			$this->insert($data);
	// 		}
	// 	}
	// }

}