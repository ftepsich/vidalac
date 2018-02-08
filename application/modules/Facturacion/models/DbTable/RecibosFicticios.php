<?php

/**
 * Recibos Ficticios
 * Se usan para las facturas o ticket factura pagados en efectivo
 * Se genera un recibo ficticio por cada factura para representar el pago de la misma en el sistema
 * Ya que en estos casos la factura es el comprobante tanto de venta como de pago
 *
 *
 * @class       Facturacion_Model_DbTable_RecibosFicticios
 * @extends     Facturacion_Model_DbTable_Recibos
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_RecibosFicticios extends Facturacion_Model_DbTable_Recibos
{
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '8,9'
     * 'Punto'             => 999
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => 58
    );

    protected $unicoAbierto = false;
}