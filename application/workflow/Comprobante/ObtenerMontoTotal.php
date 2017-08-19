<?php
/**
 * Workflow Nodo 
 * Obtener persona de un comprobante
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Workflow_Comprobante_ObtenerMontoTotal extends Rad_Workflow_Nodo
{
	protected static $_tipoEntrada = 'Row\Comprobante';
	protected $_tipoSalida  = 'Decimal';

	protected static $_descripcion = 'Obtiener Cliente/Proveedor';

	public function procesar($data)
	{

		$persona = $data->findParentRow('Base_Model_DbTable_Personas');
		
		return $persona;
	}
}