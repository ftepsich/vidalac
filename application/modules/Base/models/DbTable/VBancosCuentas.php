<?php
class Base_Model_DbTable_VBancosCuentas extends Rad_Db_Table
{
    // Tabla mapeada
    protected $_name = 'VBancosCuentas';
    
    protected $_primary = "CuentaBancariaId";

    // Relaciones
    protected $_referenceMap    = array(
            );
    
    protected $_dependentTables = array();  
    
    public function fetchEsPropia ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Propia = 1 and Persona is not null and Cerrada = 0";";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll ($where , $order , $count , $offset );
    }

    public function fetchNoEsPropia ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Propia <> 1 and Persona is not null and Cerrada = 0";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll ($where , $order , $count , $offset );
    }  
}