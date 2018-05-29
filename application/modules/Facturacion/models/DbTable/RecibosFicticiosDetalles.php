<?php

/**
 * Almacena los pagos realizados en un recibo ficticio
 * Estos son los que llevan los pagos de facturas en efectivo para representar el pago de las mismas
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 */
class Facturacion_Model_DbTable_RecibosFicticiosDetalles extends Facturacion_Model_DbTable_RecibosDetalles
{
    /**
     * Permite agregar un pago en efectivo de una caja determinada
     *
     * @param int $idComprobante    Identificador de la Orden de Pago
     * @param int $idCaja           Identificador de la Caja que se utilizara
     * @param decimal $monto        Monto a retirar de la caja y asignar al pago
     *
     * @return unknown_type
     */
//    public function insertPagoEfectivo($idComprobante, $Monto, $Caja)
//    {
//         try {
//            $this->_db->beginTransaction();
//
//            $respuesta = parent::insertPagoEfectivo($idComprobante, $Monto, $Caja);
//
//            $fiscalizador = new Facturacion_Model_Fiscalizar();
//
//            //obetener la factura q se esta pagando!!!
//            $facturaPagada = Facturacion_Model_DbTable_OrdenesDePagosFacturas::retornarComprobantePagado($idComprobante);
//
//            if (empty($facturaPagada)) throw new Rad_Db_Table_Exception('El recibo ficticio no tiene factura asociada');
//            if (count($facturaPagada) > 1) throw new Rad_Db_Table_Exception('El recibo ficticio no debe tener mas de una factura asociada');
//
//            $modelTicket = new Facturacion_Model_DbTable_TicketFacturas;
//            $factura = $modelTicket->fetchRow('Id = '.$facturaPagada[0]['Id']);
//
//            if (!$factura) throw new Rad_Db_Table_Exception('No se encontro la factura a pagar');
//
//            $numFactura   = $fiscalizador->agregarPago('Efectivo', $Monto, $factura);
//
//            $this->_db->commit();
//            return $respuesta;
//        } catch (Exception $e) {
//            $this->_db->rollBack();
//            throw $e;
//        }
//    }

    // public function delete($where)
    // {
    //     throw new Rad_Db_Table_Exception('No pueden borrarse los pagos ya impresos en el comprobante');

    // }
}