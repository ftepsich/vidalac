<?php

require_once 'Rad/Db/Table.php';

/**
 * @class       Facturacion_Model_DbTable_RecibosFacturas
 * @extends     Facturacion_Model_DbTable_ComprobantesRelacionados
 *
 *
 * Facturas Compras Remitos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *      Id                  -> Identificador Unico
 *      ComprobantePadre    -> Identificador del Recibo
 *      ComprobanteHijo     -> Identificador del Comprobente que se esta pagando (FC, NC, ND)
 *
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_RecibosFicticiosFacturas extends Facturacion_Model_DbTable_RecibosFacturas
{
    protected $noPermitirHijoAbierto = false;
}