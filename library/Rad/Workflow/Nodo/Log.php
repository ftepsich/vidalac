<?php
/**
 * Loguea al firebug la informacion enviada
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Nodo_Log extends Rad_Workflow_Nodo
{
	protected static $_descripcion = 'Loguear dato';

    protected static $_tipoEntrada = 'Row';

	public function procesar($data)
	{

		Rad_Log::debug($data);
		
		return $data;
	}
}