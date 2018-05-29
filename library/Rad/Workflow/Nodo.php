<?php
/**
 * Workflow nodo
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */

/**
 * Workflow nodo
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
abstract class Rad_Workflow_Nodo
{
    /**
     * Tipo de entrada que acepta el nodo 
     *
     * son jerarquicos: por ejemplo 'Row\Cliente'
     * null si acepta cualquier tipo de entrada
     * @var string 
     */
	protected static $_tipoEntrada;

    /**
     * Tipo de entrada que acepta el nodo 
     *
     * son jerarquicos: por ejemplo 'Row\Cliente'
     * solo null en caso de que el tipo de entrada tambien lo sea, en cuyo caso toma el mismo tipo de salida del padre
     * @var string 
     */
	protected $_tipoSalida;

	/**
     * Descripcion de la funcionalidad del nodo para el usuario
     * @var string 
     */
	protected static $_descripcion;
    /**
     * Configuracion del nodo
     * @var array 
     */
	private $_configuracion;

    private $_pizarron;

    /**
     * Tipo de salida que tiene el padre de este nodo
     * @var string 
     */
    protected $_tipoSalidaPadre;

	/**
	 * Array de parametros REQUERIDOS
	 * 'Nodo' y 'Salida' son palabras reservadas y no pueden ser usados
	 * Estos parametros son utilizados para en funcionamiento interno del Nodo (Configuracion)
	 * NO TIENEN NADA QUE VER CON LA ENTRADA O SALIDA
	 */
	protected static $_parametros = array();


	public static function getDescripcion()
	{
		return static::$_descripcion;
	}

	public static function getTipoEntrada()
	{
		return static::$_tipoEntrada;
	}

	public  function getTipoSalida()
	{
		return $this->_tipoSalida;
	}

    protected function getPizarron()
    {
        return $this->_pizarron;
    }

    /**
     * Constructor de la clase
     * @param array $cfg array 
     * @param Rad_Workflow_Pizarron
     * @param string tipo de dato que recive del padre
     */
	public function __construct($cfg, $pizarron, $tipoSalidaPadre)
	{
		$this->_configuracion = $cfg;
        $this->_pizarron      = $pizarron;
        $this->_tipoSalidaPadre = $tipoSalidaPadre;

		// Verifico que existan los parametros requeridos
		foreach ($this->_parametros as $param) {
			$parametro = $this->getParametro($param);
			
			if (!$parametro) {
				throw new Rad_Workflow_Exception("El parametro $param es requerido en un Nodo ".get_class($this));
			}
		}
	}

    /**
     * Obtiene un parametro de configuracion
     * Tener en cuenta que los nombres 'Nodo', 'Salida' y 'Pizarron' son reservados
     *
     * @param string $param nombre del parametro 
     */
	public function getParametro($param)
	{
		if ($param == 'Nodo' || $param == 'Salida' || $param == 'Pizarron') {
			throw new Rad_Workflow_Exception("El parametro $param es una palabra reservada");
		}
		return $this->_configuracion[$param];
	}

	/**
	 * Ejecuta el nodo
	 *
	 * @param mixed $data informacion a procesar
	 */
	public function procesar($data)
	{
		return $data;
	}
}