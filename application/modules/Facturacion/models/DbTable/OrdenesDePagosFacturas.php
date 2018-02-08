<?php

require_once 'Rad/Db/Table.php';

/**
 * @class 		Facturacion_Model_DbTable_FacturasOrdenesDePagos
 * @extends		Facturacion_Model_DbTable_ComprobantesRelacionados
 *

 * Detalle de la cabecera de la tabla
 * Campos:
 * 		Id			-> Identificador Unico
 * 		ComprobantePadre	-> Identificador de la Orden de Pago
 * 		ComprobanteHijo		-> Identificador del Comprobente que se esta pagando (FC, NC, ND)
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_OrdenesDePagosFacturas extends Facturacion_Model_DbTable_ComprobantesPagosFacturas {

    /**
     * Mapa de Referencias
     * @var array
     */
    protected $_referenceMap = array(
        'ComprobantesPagados' => array(
            'columns'       => 'ComprobanteHijo',
            'refTableClass' => 'Facturacion_Model_DbTable_Facturas',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        ),
        'OrdenDePago' => array(
            'columns'       => 'ComprobantePadre',
            'refTableClass' => 'Facturacion_Model_DbTable_OrdenesDePagos',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        )
    );
    
    

}
