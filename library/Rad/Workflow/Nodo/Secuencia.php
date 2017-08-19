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
 * Workflow Nodo Secuencia
 * 
 * Este nodo ejecuta todos los hijos secuencialmente
 * NO puede tener tipos de entradas o salidas
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
final class Rad_Workflow_Nodo_Secuencia extends Rad_Workflow_Nodo
{
	public function __construct() 
	{
		// este tipo de nodo no puede ser tipado, simplemente ejecuta en secuencia con la misma entrada
		self::$_tipoEntrada = null;
		$this->_tipoSalida  = null;
	}

	final public function procesar($data)
	{
		return $data;
	}
}