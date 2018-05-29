<?php

class Facturacion_Model_RecibosDetallesMapper extends Rad_Mapper
{
    protected $_class = 'Facturacion_Model_DbTable_RecibosDetalles';

    /**
     * Agrega cobros efectivo
     */
    public function agregarEfectivo($idRecibo, $monto) {

        $caja = $this->_getCajaFacturaAsociadaRecibo($idRecibo);

        $respuesta = json_encode($this->_model->insertPagoEfectivo($idRecibo, $monto, $caja));
        return $respuesta;
    }

    protected function _getCajaFacturaAsociadaRecibo($recibo)
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT p.Caja FROM PuntosDeVentas p join Comprobantes f ON f.Punto = p.Id join ComprobantesRelacionados cr ON cr.ComprobanteHijo = f.Id and cr.ComprobantePadre = $recibo");
    }

    public function agregarTarjeta($idRecibo, $monto, $cuotas, $numTarjeta, $tipo)
    {
        $this->_model->agregarTarjeta($idRecibo, $monto, $cuotas, $numTarjeta, $tipo);
    }

    public function agregarCheques($idRecibo, $idCheques)
    {
        $respuesta = $this->_model->insertPagosCheques($idRecibo, $idCheques);
        return $respuesta;
    }

    public function agregarTransaccionBancaria($idRecibo, $idTransacciones)
    {
        $respuesta = $this->_model->insertPagosTransacciones($idRecibo, $idTransacciones);
        return $respuesta;
    }
}