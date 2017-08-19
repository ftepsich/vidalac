<?php
/**
 * Es Proveedor
 *
 * @copyright SmartSoftware Argentina
 * @package Workflow
 * @subpackage Nodes
 * @author Martin Alejandro Santangelo
 */
class Workflow_Persona_EsProveedor extends Rad_Workflow_Condicion
{
	protected static $_tipoEntrada = 'Row\Persona';
	
	protected static $_descripcion = 'Es Proveedor?';

	protected function condicion($data) 
	{
		return $data->EsProveedor == 1;
	}
}