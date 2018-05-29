<?php
/**
 * Workflow Condicion
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */

/**
 * Workflow Condicion
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Condicion extends Rad_Workflow_Nodo
{
	protected $_tipoSalida = 'boolean';

	/**
	 * @return boolean
	 */
	final public function procesar($data)
	{
		return $this->condicion($data);
	}

	protected function condicion($data) 
	{
		return true;
	}
}