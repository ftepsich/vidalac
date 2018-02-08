<?php
/**
 * Workflow Nodo 
 * Verificar factura venta
 *
 * @copyright SmartSoftware Argentina
 * @package Workflow
 * @subpackage Nodes
 * @author Martin Alejandro Santangelo
 */
class Workflow_Comprobante_EsFacturaVenta extends Rad_Workflow_Condicion
{
	protected static $_tipoEntrada = 'Row\Comprobante';

	protected static $_descripcion = 'Error si no es Factura Venta';

	public function condicion($data)
	{
		$tipoDeComprobante = $data->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
		return $tipoDeComprobante->Grupo == 6;
	}
}