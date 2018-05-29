<?php

class Base_Model_GeneradorDeChequesMapper extends Rad_Mapper
{
    protected $_class = 'Base_Model_DbTable_GeneradorDeCheques';
    
    /**
     * Devuelve el proximo numero de una factura de acuerdo al punto de venta
     */
    public function recuperarProximoNumeroCheque ($idChequera)
    {
        return $this->_model->recuperarProximoNumeroCheque($idChequera);
    }
}
