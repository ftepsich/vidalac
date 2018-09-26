<?php

class Almacenes_Model_RemitosSinRemitoMapper
{
    protected $_class = 'Almacenes_Model_DbTable_RemitosSinRemito';
    
    public function __construct ()
    {
        $this->_model = new $this->_class();
    }
    
    public function get ($id)
    {
        $row = $this->_model->find($id);
        if (count($row)) {
            return $row->current()->toArray();
        }
        
    }
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
}
