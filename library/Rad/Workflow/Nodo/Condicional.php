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
class Rad_Workflow_Nodo_Condicional extends Rad_Workflow_Nodo_Selector
{
    protected static $_parametros = array('Condicion');

    protected $_prefijoClase = 'Workflow_';

    /**
     * Define las salidas posibles del nodo en caso de tener mas de una
     */
    protected static $_salidas = array('Si', 'No');

    protected function seleccionar($data)
    {
        if ($this->condiccion($data)) {
            $this->_resultado = 'Si';
        } else {
            $this->_resultado = 'No';
        }
    }

    /**
     * SOBREESCRIBIR
     */
    protected function condiccion($data) 
    {
        return $this->analizarCondicion($data);
    }

    protected function analizarCondicion($data) 
    {
        $condicion = $this->getParametro('Condicion');

        $parser = new Rad_Util_ParenthesisParser();

        $resultado = $parser->run("($condicion)");

        $this->_condResultados = array();

        //Rad_Log::debug($resultado,'resutaldo');

        foreach( $resultado as $k => $parse) {
           $parse = substr($parse,1,-1);

           // si tiene parentesis hay q reemplazar
           if (preg_match('~\([^\(\)]*\)~', $parse, $match)) {
                for ($i = $k; $i >= 0; $i--){
                    $parse = str_replace($resultado[$i], $this->_condResultados[$i], $parse);
                }
           }

           $this->_condResultados[$k] = ($this->ejecutarSentencia($parse, $data))?'true':'false';
        }
        return $this->_condResultados[$k] === 'true';
    }

    protected function ejecutarSentencia($sentencia, $data)
    {
        $condiciones = explode(' ', $sentencia);

        $resultado = '';

        foreach ($condiciones  as $key => $value) {
            if ($value === '') continue;
            

            if (strtolower($value) == 'not') {
                $not = true;
            } else if (strtolower($value) == 'and') {
                if ($resultado === '')  throw new Rad_Workflow_Exception("Error, el operador AND debe unir condiciones cerca de: $sentencia");
                $bool = 'and';
            } 
            else if (strtolower($value) == 'or') {
                if ($resultado === '')  throw new Rad_Workflow_Exception("Error, el operador OR debe unir condiciones cerca de: $sentencia");
                $bool = 'or';
            } 
            else if (strtolower($value) == 'true') {
                $resultado = $this->_bollean($bool, true, $resultado);
            }
            else if (strtolower($value) == 'false') {
                $resultado = $this->_bollean($bool, false, $resultado);
            } else {
                $res = $this->ejecutarCondicion($value, $data);
                if ($not) $res = !$res;

                if ($bool) {
                    $resultado = $this->_bollean($bool, $res, $resultado);
                } else {
                    $resultado = $res;
                }

                $bool = '';
                $not = false;
            }
        }
        //Rad_Log::debug($sentencia." = ".(( $resultado)?'true':'false'));
        return $resultado;
    }

    protected function _bollean($op, $value, $value2)
    {
        switch ($op) {
            case 'or':
                return $value || $value2;
                break;
            
            case 'and':
                return $value && $value2;
                break;
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