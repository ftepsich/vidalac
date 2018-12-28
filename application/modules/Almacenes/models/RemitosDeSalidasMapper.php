<?php

class Almacenes_Model_RemitosDeSalidasMapper extends Rad_Mapper
{
    protected $_class = 'Almacenes_Model_DbTable_RemitosDeSalidas';
        
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
    
    
    public function refiscalizar($idRemito)
    {

        $db = Zend_Registry::get('db');
        $idRemito = $db->quote($idRemito, 'INTEGER');
      
        $remito = $this->_model->imprimir($idRemito);
    }
}
