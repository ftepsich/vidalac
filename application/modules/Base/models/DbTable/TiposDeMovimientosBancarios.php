<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TiposDeMovimientosBancarios extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "TiposDeMovimientosBancarios";

	// Relaciones
    protected $_referenceMap    = array(
            );
	
	protected $_dependentTables = array('Base_Model_DbTable_TransaccionesBancarias');
    
    public function fetchVenderDepositar($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TiposDeMovimientosBancarios.Id in (4,10)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }   
    
    	
	
}