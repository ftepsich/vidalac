<?php
/**
 * Workflow Nodo 
 * Verificar factura venta
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Nodo_VerificarFacturaVenta extends Rad_Workflow_Nodo
{
	protected static $_tipoEntrada = 'Comprobante';
	protected static $_tipoSalida = 'Comprobante';

	protected static $_descripcion = 'Error si no es Factura Venta';

	public function procesar($data)
	{
		//$tipoDeComprobante = $data->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
		if ($data->tipoDeComprobante != 6) {
			throw new Rad_Workflow_Exception("El comprobante no es una factura de venta");
		}

		return $data;
	}
}