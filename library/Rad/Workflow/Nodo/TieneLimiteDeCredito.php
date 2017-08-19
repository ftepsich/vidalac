<?php
/**
 * Workflow Condicion
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Nodo_TieneLimiteDeCredito extends Rad_Workflow_Condicion
{
	protected static $_tipoEntrada = 'Persona';
	
	protected static $_descripcion = 'Tiene limite de Credito?';

	protected function condicion($data) 
	{
		return $data->LimiteDeCredito > 0;
	}
}