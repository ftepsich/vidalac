<?php

class Facturacion_Model_OrdenesDePedidosMapper extends Rad_Mapper
{
    protected $_class = 'Facturacion_Model_DbTable_OrdenesDePedidos';
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
}
