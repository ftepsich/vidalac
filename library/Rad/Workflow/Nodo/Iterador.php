<?php
/**
 * Workflow Nodo Selector
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */

/**
 * Workflow Nodo Iterador
 * 
 * Este nodo retorna n datos q seran procesados por sus hijos 
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
abstract class Rad_Workflow_Nodo_Iterador extends Rad_Workflow_Nodo
{
	/** 
	 *	debe sobreescribirse en las clases hijas y retornar n datos del tipo tipoSalida
	 *  array(
	 *		data1,
	 *		data2,
	 *		data3
	 *	)
	 */
	public function procesar($data)
	{
		return array();
	}
}