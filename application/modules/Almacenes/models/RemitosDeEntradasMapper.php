<?php

class Almacenes_Model_RemitosDeEntradasMapper extends Rad_Mapper
{
    protected $_class = 'Almacenes_Model_DbTable_RemitosDeEntradas';
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
}
