<?php
/**
 * Workflow Condicion
 * Obtiene limite de credito
 *
 * @copyright SmartSoftware Argentina
 * @package Workflow
 * @subpackage Nodes
 * @author Martin Alejandro Santangelo
 */
class Workflow_Persona_ObtenerLimiteDeCredito extends Rad_Workflow_Nodo
{
	protected static $_tipoEntrada = 'Row\Persona';
    protected $_tipoSalida = 'Entero';
	
	protected static $_descripcion = 'Obtiene limite de Credito';

	public function procesar($data) 
	{
		return $data->LimiteDeCredito;
	}
}