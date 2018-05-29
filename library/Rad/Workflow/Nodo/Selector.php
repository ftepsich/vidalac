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
 * Workflow Nodo Selector
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
abstract class Rad_Workflow_Nodo_Selector extends Rad_Workflow_Nodo
{
	/**
	 * Define las salidas posibles del nodo en caso de tener mas de una
	 * @var array 
	 */
	protected static $_salidas = array();

	/**
	 * Define las salidas posibles del nodo en caso de tener mas de una
	 * @var string 
	 */
	protected $_resultado;

	/**
	 * Sobreescribir en las clasese hijas
	 * Setear resultado...
	 * NO MODIFICAR DATA LOS SELECTORES NO DEBEN REALIZAR PROCESO ALGUNO
	 */
	protected function seleccionar($data)
	{
		//$_resultado = 'Si';
	}

	final public function procesar($data)
	{
		$this->seleccionar($data);

		if (!in_array($this->_resultado, static::$_salidas)) {
			throw new Rad_Workflow_Exception("el resultado '$this->_resultado' de la condición no es un resultado válido para ".get_class($this));
		}

		return $data;
	}

	public static function getSalidas() {
		self::$_salidas;
	}

	public function getResultado() {
		return $this->_resultado;
	}
}