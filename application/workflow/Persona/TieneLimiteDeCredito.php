<?php
/**
 * Workflow Condicion
 * Tiene limite de credito
 *
 * @copyright SmartSoftware Argentina
 * @package Workflow
 * @subpackage Nodes
 * @author Martin Alejandro Santangelo
 */
class Workflow_Persona_TieneLimiteDeCredito extends Rad_Workflow_Condicion
{
	protected static $_tipoEntrada = 'Row\Persona';
	
	protected static $_descripcion = 'Tiene limite de Credito?';

	protected function condicion($data) 
	{
		return $data->LimiteDeCredito > 0;
	}
}