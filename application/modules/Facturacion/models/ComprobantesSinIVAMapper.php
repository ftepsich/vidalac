<?php

class Facturacion_Model_ComprobantesSinIVAMapper extends Rad_Mapper 
{
    protected $_class = 'Facturacion_Model_DbTable_ComprobantesSinIVA';
    

    public function generarOrdenDePagoSinIVADesdeElControlador ($id,$caja)
    {  
        try {
            $db = Zend_Registry::get("db");
            $db->beginTransaction();

            if($caja){
                $this->_model->generarOrdenDePagoSinIVA($id,$caja);   
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    }  

    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function cerrar ($id, $caja)
    {  
        try {
            $db = Zend_Registry::get("db");
            $db->beginTransaction();
            $this->_model->cerrar($id);
            if ($caja){
                $this->_model->generarOrdenDePagoSinIVA($id,$caja);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
}
