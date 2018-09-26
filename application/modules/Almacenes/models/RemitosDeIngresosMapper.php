<?php

class Almacenes_Model_RemitosDeIngresosMapper extends Rad_Mapper
{
    protected $_class = 'Almacenes_Model_DbTable_RemitosDeIngresos';
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
}
