<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_MarcasDeTerceros extends Base_Model_DbTable_Marcas
{
    protected $_name = "Marcas";

    public $abmTitle = 'Marcas de Terceros';       
    
    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;
    
    protected $_defaultValues = array (
        'Propia' => '0',
            'Produccion' => '0'
    );
    // ----------------------------------------------------

    public function fetchAll ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Propia = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }       
    
}