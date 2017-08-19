<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TiposDeArticulos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "TiposDeArticulos";

	// Relaciones
    protected $_referenceMap    = array(
        
	        'PlanesDeCuentas' => array(
            'columns'           => 'CuentaBase',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist/fetch/PlanCuentaImputable',
            'refTable'			=> 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize' => 20
        )    
    );
	
	protected $_dependentTables = array('Base_Model_DbTable_Articulos');	
	
	
	    public function fetchValidos ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TiposDeArticulos.Id <> 2";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
}