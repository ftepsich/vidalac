<?php

class Facturacion_Model_OrdenesDeComprasMapper extends Rad_Mapper
{
    protected $_class = 'Facturacion_Model_DbTable_OrdenesDeCompras';
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
}
