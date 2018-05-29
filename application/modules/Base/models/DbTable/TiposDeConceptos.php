<?php
require_once('Rad/Db/Table.php');
/**
 *
 * Model_DbTable_TiposDeConceptos
 *
 * Tipos De Conceptos
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_TiposDeConceptos
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_TiposDeConceptos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "TiposDeConceptos";

	// Relaciones
    protected $_referenceMap    = array(
            );
	
	protected $_dependentTables = array('Base_Model_DbTable_ConceptosImpositivos');
	
}