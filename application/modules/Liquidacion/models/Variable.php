<?php

/**
 * Liquidacion_Model_Variable
 *
 * Representa a todas las variables del liquidador de sueldo
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Variable
{
    /**
     * se utiliza solo para almacenar el id del registro de almacenamiento
     */
    protected $_id;

    protected $_nombre;

    protected $_periodo;

    protected $_selector;

    protected $_resultado;

    protected $_caracteristicas = array();

    protected static $_sum_caracteristicas = array();

    /**
     * @var contiene la formula o el SQL de la variable
     */
    protected $_formula;

    public function __construct($id, $nombre, $codigo, $descripcion, Liquidacion_Model_Periodo $periodo, $formula = null, $selector = null)
    {
        $this->_id          = $id;
        $this->_nombre      = $nombre;
        $this->_periodo     = $periodo;
        $this->_formula     = $formula;
        $this->_selector    = $selector;
        $this->_codigo      = $codigo;
        $this->_descripcion = $descripcion;

        /*        
        if ($this->_formula) {
            $this->_formula = $this->_formula;
        }
        */
    }

    public function setCaracteristicas(array $c)
    {
        $this->_caracteristicas = $c;
    }

    public function getCaracteristica($n)
    {
        return $this->_caracteristicas[$n];
    }

    public function getNombre()
    {
        return $this->_nombre;
    }

    public function getResultado()
    {
        return $this->_resultado;
    }

    public function getFormula($servicio)
    {
        return $this->_formula;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function calcular($evaluador, $servicio)
    {
        $formula = $this->getFormula($servicio);

        if (!$formula) $formula = 0;

        $this->_preCalcular($formula, $evaluador, $servicio);

        Rad_Log::debug('calculando: '.$formula);

        // Calculamos
        $this->_resultado = $this->_calcular($evaluador,$formula);

        if (!$this->_resultado) $this->_resultado = 0;

        $this->_calcular_sum_caracteristicas();

        // Rad_Log::debug('dio : '.$valor);

        $this->_postCalcular($this->_resultado, $evaluador, $servicio);

        return $this->_resultado;
    }

    public static function initSumCaracteristicas()
    {
        self::$_sum_caracteristicas = array();
    }

    public static function getSumCaracteristica($nombre, $valor=1) 
    {
        if (self::$_sum_caracteristicas[$nombre])
            return self::$_sum_caracteristicas[$nombre][$valor];

        return 0;
    }

    protected function _calcular_sum_caracteristicas()
    {
        foreach ($this->_caracteristicas as $key => $value) {
            self::$_sum_caracteristicas[$key][$value] += $this->_resultado;
        }
    }

    protected function _calcular($evaluador, $formula)
    {
        //Rad_Log::debug($this->_nombre.': '. $formula);
        if (is_numeric($formula)) {
            $evaluador->setVar($this->_nombre, $formula);
            return $formula;
        } else {
            try {
                return $evaluador->execute($formula, $this->_nombre);
            } catch (Exception $e) {
                throw new Exception($e->getMessage().': En variable '.$this->_id.'  '.$formula);
            }
        }
    }

    public function calcSelector($evaluador, $servicio)
    {
        if (!trim($this->_selector)) return true;

        $this->_preCalcularSelector($this->_selector, $evaluador, $servicio);

        try {
            $r = $evaluador->execute(trim($this->_selector));
        } catch (Exception $e) {
	        if ($servicio) {
             	throw new Exception($e->getMessage().': En variable :'.$this->_id.'  servicio: '.$servicio->Id);
            } else { 
		        throw new Exception($e->getMessage().': En variable :'.$this->_id);
	        }
        }
        $this->_postCalcularSelector($r, $evaluador, $servicio);

        return $r;
    }

    protected function _postCalcular($valor, $evaluador, $servicio)
    {
        Rad_PubSub::publish('Liquidador/Variable/postCalcular', $valor, $servicio, $evaluador, $this);
    }

    protected function _preCalcular($formula, $evaluador, $servicio)
    {
        Rad_PubSub::publish('Liquidador/Variable/preCalcular',$formula, $servicio, $evaluador, $this);
    }

    protected function _postCalcularSelector($valor, $evaluador, $servicio)
    {
        Rad_PubSub::publish('Liquidador/Variable/postCalcularSelector', $valor, $servicio, $evaluador, $this);
    }

    protected function _preCalcularSelector($formula, $evaluador, $servicio)
    {
        Rad_PubSub::publish('Liquidador/Variable/preCalcularSelector',$formula, $servicio, $evaluador, $this);
    }
}
