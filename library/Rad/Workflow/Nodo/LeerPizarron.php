<?php
/**
 * Workflow Nodo Leer pizarron
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */

/**
 * Workflow Leer pizarron
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Nodo_LeerPizarron extends Rad_Workflow_Nodo
{
    protected static $_parametros = array('Parametro');

    /**
     * Constructor de la clase
     * @param array $cfg array 
     */
    public function __construct($cfg, $pizarron, $tipoSalidaPadre)
    {
        parent::__construct($cfg, $pizarron, $tipoSalidaPadre);

        $this->_param = $this->getParametro('Parametro');

        $pizarron  = $this->getPizarron();

        $tipoSalida = $pizarron->getTipo($this->_param);

        if (!$tipoSalida) throw new Rad_Workflow_Exception("El Pizarron no contiene el parametro $parametro");

        // Seteo el tipo de salida del nodo
        $this->_tipoSalida = $tipoSalida;
    }

    /**
     * Ejecuta el nodo
     *
     * @param mixed $data informacion a procesar
     */
    public function procesar($data)
    {
        $pizarron  = $this->getPizarron();
        $data = $pizarron->getValor($this->_param);
        return $data;
    }
}