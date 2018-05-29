<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TiposDeCuentas extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "TiposDeCuentas";

	// Relaciones
    protected $_referenceMap    = array(
            );
	
	protected $_dependentTables = array('Base_Model_DbTable_CuentasBancarias');	
	
}