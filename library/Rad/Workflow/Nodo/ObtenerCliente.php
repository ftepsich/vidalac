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
class Rad_Workflow_Nodo_ObtenerPersona extends Rad_Workflow_Nodo
{
	protected static $_tipoEntrada = 'Comprobante';
	protected static $_tipoSalida  = 'Persona';

	protected static $_descripcion = 'Obtiener Cliente/Proveedor';

	public function procesar($data)
	{
		$persona = $data->findParentRow('Base_Model_DbTable_Personas');
		
		return $persona;
	}
}