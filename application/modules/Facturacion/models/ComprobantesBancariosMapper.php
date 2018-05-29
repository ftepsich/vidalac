<?php

class Facturacion_Model_ComprobantesBancariosMapper extends Facturacion_Model_FacturasComprasMapper
{
    protected $_class = 'Facturacion_Model_DbTable_ComprobantesBancarios';

    public function cerrar ($id)
    {  
        try {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $this->_model->cerrar($id);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}