<?php

class Facturacion_Model_TicketFacturasMapper extends Facturacion_Model_FacturasVentasMapper
{
    protected $_class = 'Facturacion_Model_DbTable_TicketFacturas';

    public function getIdComprobantePago($idFactura)
    {
        $id = $this->_model->getIdComprobantePago($idFactura);
        if (!$id) throw new Rad_Exception('No se encontro el comprobante de pagos');
        return $id;
    }

    /**
     * Retorna el Id del recibo y el saldo pendiente a pagar
     * @param  int $idFactura Id de Factura
     * @return array             Retorna el Id del recibo y el saldo pendiente a pagar
     */
    public function getIdComprobantePagoYSaldo($idFactura)
    {
        $id = $this->_model->getIdComprobantePago($idFactura);
        if (!$id) throw new Rad_Exception('No se encontro el comprobante de pagos');
    }
}
