<?php
/**
 * Workflow Nodo 
 * muestra un error al usuario
 *
 * @copyright SmartSoftware Argentina
 * @package Workflow
 * @subpackage Nodes
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Nodo_MostrarError extends Rad_Workflow_Nodo
{
	protected static $_parametros = array('Mensaje');

	protected static $_descripcion = 'Muestra un Error al Usuario';

	public function procesar($data)
	{
		$mensaje = $this->getParametro('Mensaje');

		throw new Rad_Workflow_Exception($mensaje);
	}
}