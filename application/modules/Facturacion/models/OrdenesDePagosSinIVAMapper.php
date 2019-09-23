<?php

class Facturacion_Model_OrdenesDePagosSinIVAMapper extends Rad_Mapper
{
    protected $_class = 'Facturacion_Model_DbTable_OrdenesDePagosSinIVA';
  
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
  
    public function getTotal($id)
    {
        return $this->_model->recuperarMontoaPagar($id);
    }
    
}
