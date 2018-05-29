<?php

class Contable_Model_CajasMovimientosMapper extends Rad_Mapper
{
    protected $_class = 'Contable_Model_DbTable_CajasMovimientos';
    
    /**
     * Permite mover dinero de una caja a otra.
     */
    public function movimientosEntreCajas($CajaOrigen,$CajaDestino,$Descripcion,$Monto,$Fecha){   
        return $this->_model->movimientosEntreCajas($CajaOrigen,$CajaDestino,$Descripcion,$Monto,$Fecha);
    }    
    
}
