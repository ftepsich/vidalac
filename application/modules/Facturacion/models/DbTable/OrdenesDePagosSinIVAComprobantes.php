<?php

require_once 'Rad/Db/Table.php';

/**
 * @class 		Facturacion_Model_DbTable_FacturasOrdenesDePagos
 * @extends		Facturacion_Model_DbTable_ComprobantesRelacionados
 *

 * Detalle de la cabecera de la tabla
 * Campos:
 * 		Id			-> Identificador Unico
 * 		ComprobantePadre	-> Identificador de la Orden de Pago Sin IVA
 * 		ComprobanteHijo		-> Identificador del Comprobante Sin IVA que se esta pagando 
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_OrdenesDePagosSinIVAComprobantes extends Facturacion_Model_DbTable_ComprobantesPagosSinIVA {

    /**
     * Mapa de Referencias
     * @var array
     */
    protected $_referenceMap = array(
        'ComprobantesPagados' => array(
            'columns'       => 'ComprobanteHijo',
            'refTableClass' => 'Facturacion_Model_DbTable_ComprobantesSinIVA',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        ),
        'OrdenDePago' => array(
            'columns'       => 'ComprobantePadre',
            'refTableClass' => 'Facturacion_Model_DbTable_OrdenesDePagosSinIVA',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        )
    );
    
    

}
