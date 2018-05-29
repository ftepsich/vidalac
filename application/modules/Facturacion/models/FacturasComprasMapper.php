<?php

class Facturacion_Model_FacturasComprasMapper extends Rad_Mapper 
{
    protected $_class = 'Facturacion_Model_DbTable_FacturasCompras';
    
    public function anular ($id)
    {
        $this->_model->anular($id);
    }
    
    public function generarOrdenDePagoDesdeControlador ($id,$caja)
    {  
        try {
            $db = Zend_Registry::get("db");
            $db->beginTransaction();

            if($caja){
                $this->_model->generarOrdenDePago($id,$caja);   
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    }  
    
    public function cerrar ($id, $caja)
    {  
        try {
            $db = Zend_Registry::get("db");
            $db->beginTransaction();

            $this->_model->cerrar($id);
            if ($caja){
                $this->_model->generarOrdenDePago($id,$caja);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function insertarConceptos ($id)
    {
        $this->_model->setFetchWithAutoJoins(false);
        $this->_model->setFetchWithCalcFields(false);
        $this->_model->insertarConceptosDesdeControlador($id);
    }
    
    public function cambiarImputacionIva($idComprobante, $IdLibroIVA)
    {
        $this->_model->setFetchWithAutoJoins(false);
        $this->_model->setFetchWithCalcFields(false);
        $this->_model->cambiarImputacionIVA($idComprobante, $IdLibroIVA);
        return $this->_model->find($idComprobante)->current()->toArray();
    }
}
