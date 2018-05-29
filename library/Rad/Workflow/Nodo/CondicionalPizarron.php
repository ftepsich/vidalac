<?php
require_once 'Condicional.php';

/**
 * Workflow Condicional 
 * Compara SOLO tipos basicos leidos desde el pizarron
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow_Nodo_CondicionalPizarron extends Rad_Workflow_Nodo_Condicional
{
    private static $_tiposValidos = array('Entero', 'Decimal', 'Fecha', 'FechaHora', 'Texto'); 

    protected function analizarCondicion($data) 
    {
        $condicion = $this->getParametro('Condicion');

        return $this->ejecutarSentencia($condicion, $data);

    }

    protected function ejecutarSentencia($sentencia, $data)
    {
        $parse = explode(' ', $sentencia);
        foreach ($parse as $key => $value) {
            if ( $value === '' ) unset($parse[$key]);
        }

        if (count($parse) != 3) throw new Rad_Workflow_Exception("La condicion no tiene un formato valido!<br> Ej: valor1 = valor2");

        $pizarron  = $this->getPizarron();

        // Valor 1
        $tipo = $pizarron->getTipo($parse[0]);

        $this->verificarTipo($tipo);

        $value = $pizarron->getValor($parse[0]);

        // Valor 2
        $tipo = $pizarron->getTipo($parse[2]);

        $this->verificarTipo($tipo);

        $value2 = $pizarron->getValor($parse[2]);

        $op = $parse[1];
        
        return $this->_op($op, $value, $value2);
    }

    protected function verificarTipo($tipo)
    {
        if (!in_array($tipo, self::$_tiposValidos)) throw new Rad_Workflow_Exception("El tipo $tipo no es soportado por un Nodo del tipo Condicion Pizarron");
    }

    protected function _op($op, $value, $value2)
    {
        switch ($op) {
            case '<':
                return $value < $value2;
                break;
            
            case '=':
                return $value == $value2;
                break;
            case '>':
                return $value > $value2;
                break;
            default:
                throw new Rad_Workflow_Exception("El oeprador $op no esta soportado");
        }
    }

    protected function ejecutarCondicion($condicion, $data)
    {
        //Rad_Log::debug('ANALIZANDO CONDICION '.$condicion);
        $condicionClass = $this->_prefijoClase . $condicion;

        $nodo  = new $condicionClass(array(), $this->_pizarron, $this->_tipoSalidaPadre);
        
        return $nodo->procesar($data);
    }
}