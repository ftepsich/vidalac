<?php
require_once 'OrdenesDeCompras.php';

/**
 *
 * Ordenes Compras Varios
 *
 * Detalle de la cabecera de la tabla
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 * @class 		Facturacion_Model_DbTable_OrdenesDeComprasVarios
 * @extends		Facturacion_Model_DbTable_Comprobantes
 *
 */
class Facturacion_Model_DbTable_OrdenesDeComprasVarios extends Facturacion_Model_DbTable_OrdenesDeCompras 
{
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '19'
     * 'Punto'             => 1
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => 51,
        'Punto' => 1
    );
}