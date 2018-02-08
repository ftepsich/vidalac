<?php
require_once('OrdenesDeComprasArticulos.php');

/**
 * @class 		Facturacion_Model_DbTable_OrdenesDeComprasArticulos
 * @extends		Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * Ordenes de Compras Articulos
 * 
 * Detalle de la cabecera de la tabla 
 * Campos:
 * 		Id					-> Identificador Unico
 *		Comprobante			-> identificador de la Factura Compra	
 *		TipoDeComprobanteDetalle 	-> identidicador de Art, Serv, tiket
 *		Articulo			-> identificador del articulo, servicio, etc (puede ser null)
 * 		CuentaCasual		-> Cuenta del Plan de Cuenta a utilizar en el caso qeu no se indique el articulo
 *		Cantidad			-> Cantidad de elementos del articulo indicado
 *		PrecioUnitario		-> Precio por unidad del articulo expresado en moneda local
 *		PrecioUnitarioMExtranjera	-> Precio por unidad del articulo expresado en otra moneda
 *		DescuentoEnPocentaje		-> Descuento realizado sobre el precio unitario (rango 0.01 a 99.99) 
 *		Modificado			-> Bndera que indica si fue modificado manualmente
 *		Observaciones		-> Obs. internas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_OrdenesDeComprasArticulosVarios extends Facturacion_Model_DbTable_OrdenesDeComprasArticulos
{
    /**
	 * Inseta un Registro
	 *
	 * @param array $data
	 * @return mixed
	 */	
	public function insert($data)	{
		unset($data['Articulo']);
		return parent::insert($data);
	}

	/**
	 * updateo un registro 
	 *
	 * @param array $data
	 * @param mixwd $where
	 * @return mixed
	 */
	public function update($data,$where) 	{
		unset($data['Articulo']);
		return parent::update($data,$where);
	}
}
