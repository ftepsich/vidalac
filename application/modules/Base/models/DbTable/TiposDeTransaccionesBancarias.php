<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TiposDeTransaccionesBancarias extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "TiposDeTransaccionesBancarias";

	// Relaciones
    protected $_referenceMap    = array(
            );
	
	protected $_dependentTables = array('Base_Model_DbTable_TransaccionesBancarias');	
	
}