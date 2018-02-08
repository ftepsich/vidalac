<?php

class Facturacion_Model_RecibosMapper extends Rad_Mapper
{
    protected $_class = 'Facturacion_Model_DbTable_Recibos';
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }	
	
    public function getTotal($id)
    {
        return $this->_model->recuperarMontoTotal($id);
    }
	
    /**
     * Devuelve el proximo numero de un Recibo de acuerdo al punto del recibo
     */
    public function generarNumeroRecibo($punto,$tipo)
    {
        return $this->_model->generarNumeroRecibo($punto,$tipo);
    }	
    
    public function getControlTotalConcepto($id)
    {
        //funcion que controla q el total del concepto sea mayor a 0 (cero)
        $M_CI = new Facturacion_Model_DbTable_ComprobantesImpositivos(array(), false);
        $M_CI->controlTotalConcepto($id);
    }  
	    
    public function cambiarImputacionIva($idComprobante, $IdLibroIVA){
        $this->_model->cambiarImputacionIVA($idComprobante, $IdLibroIVA);
    }
}
