<?php

require_once 'Rad/EventDispatcher.php';

/**
 * Liquidacion_Model_Liquidador Servicio
 *
 * Calcula todos los conceptos para un servicio en un periodo dado
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_LiquidadorServicio
 * @copyright   SmartSoftware Argentina
 * @author      Martín Alejandro Santangelo
 */
class Liquidacion_Model_LiquidadorServicio
{
    public $debug = false;

    protected $_eventDispatcher;

    protected $_variablesProvider;

//    protected $_variableCollection;

    /**
     * VariableCollection que almacena los conceptos
     */
    protected static $_conceptos;

    /**
     * VariableCollection que almacena el resto de las variables
     */
    protected static $_variables;

    public function __construct(Liquidacion_Model_VariablesProvider $proveedor)
    {
        if (!$proveedor) {
            throw new Liquidacion_Model_Liquidador_Exception('No se paso el Proveedor de variables al liquidador');
        }

        $this->_variablesProvider    = $proveedor;

        $this->_eventDispatcher = new Rad_EventDispatcher(
            array('servicio_liquidado', 'concepto_liquidado', 'concepto_selector_negativo', 'variable_calculada')
        );
    }

    public function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    /**
     * retorna la coleccion de conceptos
     * @var Liquidacion_Model_VariableCollection
     */
    public static function getConceptos()
    {
        return self::$_conceptos;
    }

    /**
     * retorna la coleccion de variables
     * @var Liquidacion_Model_VariableCollection
     */
    public static function getVariables()
    {
        return self::$_variables;
    }

    /**
     * retorna el proveedor de variables
     */
    public function getVarProvider()
    {
        return $this->_variablesProvider;
    }

    /**
     * Retorna un evaluador matemático configurado
     * @param Liquidacion_Model_Periodo $periodo    periodo a liquidar
     * @param row                       $servicio   servicio a liquidar
     */
    public function getEvaluator(Liquidacion_Model_Periodo $periodo, Rad_Db_Table_Row $servicio)
    {
        // Evaluador matematico
        $evalMath = new Liquidacion_Model_Evaluator($periodo, $servicio);

        // Seteamos el buscador de variables no definidas
        $evalMath->setVariableHandler(array($this, 'getVars'));
        return $evalMath;
    }

    /**
     * Liquida un servicio dado
     * @param Rad_Db_Table_Row  $serv  registro de servicio
     */
    public function liquidarServicio(Rad_Db_Table_Row $serv, Liquidacion_Model_Periodo $periodo, $liquidacion)
    {
        // inicializo el sumador de remunerativos
        Liquidacion_Model_Variable_Concepto_RemunerativoBase::initSum();
        Liquidacion_Model_Variable_Concepto_NoRemunerativoBase::initSum();
        Liquidacion_Model_Variable::initSumCaracteristicas();

        // creamos el container para las variables que no son conceptos

        self::$_variables = new Liquidacion_Model_VariableCollection;

        $evalMath = $this->getEvaluator($periodo, $serv);

        // Obtengo los conceptos activos al perido especificado (Liquidacion_Model_VariableCollection)
 	    self::$_conceptos = $this->_variablesProvider->getConceptos($periodo, $serv, $liquidacion);
		//throw new Liquidacion_Model_Exception(print_r($this->_conceptos, true));
        // estas variables se usan en la funcion q busca las variables para el evaluador matematico(getVars)
        $this->_servicio = $serv;
        $this->_periodo  = $periodo;

        foreach (self::$_conceptos as &$cpto) {
            $this->_procesarConcepto($evalMath, $cpto, $serv, $periodo);
        }

        // Procesamos los conceptos extras (Que se calcula despues de todos los demas conceptos)
        self::$_conceptos = $this->_variablesProvider->getConceptosExtras($periodo, $serv);

        foreach (self::$_conceptos as &$cpto) {
            $this->_procesarConcepto($evalMath, $cpto, $serv, $periodo);
        }

        $this->_eventDispatcher->fire('servicio_liquidado', $serv, $evalMath, $this->_variablesProvider, $this, $periodo);

        // disparamos el evento de servicio liquidado
        Rad_PubSub::publish('Liquidador/ServicioLiquidado', $serv, $evalMath, $this->_variablesProvider);
    }

    protected function _procesarConcepto($evalMath, $cpto, $serv, $periodo)
    {
        // verificamos que el concepto no este calcualdo ya, en cuyo caso lo salteamos
        $var = $cpto->getNombre();

        if ($evalMath->getVar($var) !== null) {
            //Rad_Log::debug("concepto $var ya calculado, ignorado.");
            return;
        }

        try {
            // vemos si hay q ejecutar el concepto segun el selector
            echo "--- $var: calculando selector ---\n";
            if ($cpto->calcSelector($evalMath, $serv)) {
                // calculamos
		        echo "    Calculando concepto ---> $var ---\n";
                $this->calcularVariable($cpto, $evalMath, $serv, $periodo);

            } else {
                echo "    NO cumple selector  ---> $var ---\n";
                $this->_eventDispatcher->fire('concepto_selector_negativo', $serv, $evalMath, $cpto, $periodo);
            }
        } catch (EvalMath_Exception $e) {
            throw new Liquidacion_Model_Liquidador_Exception('Error al intentar evaluar el concepto '.$var);
        }
    }

    /**
     * Se encarga de obtener las variables que no se encuentran al calcular
     *
     * @param string    $v variable
     * @param Evaluator $t evaluador matematico
     */
    public function getVars($v, $t)
    {
        // primero miro q no sea un concepto y si no busco la variable
        if ($this->getConceptos()->hasByName($v)) {
	        $var = $this->getConceptos()->get($v);
        } else {
            $var = $this->getVarProvider()->getVariable($v, $this->_periodo, $this->_servicio);

            if ($var instanceof Liquidacion_Model_Variable) {
                // agrego la variable al container de variables
                $this->getVariables()->add($var);
            } else {
                return $var;
            }
        }

        if ($var) {
            // creo un evaluador limpio
            $eval = $this->getEvaluator($this->_periodo, $this->_servicio);
            // Le copio las variables
            $eval->setVars($t->getVars());

            // Rad_Log::debug('Encontre la variable:'.$v);
            try {
                if ($var->calcSelector($eval, $this->_servicio)) {
                    $resultado = $this->calcularVariable($var, $eval, $this->_servicio, $this->_periodo);
                    // me copio las variables del evaluador
                    $t->setVars($eval->getVars());
                } else {
                    $resultado = 0;
                    $t->setVars($eval->getVars());
                    $t->setVar($var->getNombre(), 0);
                }

                return $resultado;

            } catch (EvalMath_Exception $e) {
                throw new Liquidacion_Model_Liquidador_Exception('Error al intentar evaluar la variable '.$var->getNombre());
            }

        } else {
            throw new Liquidacion_Model_Liquidador_Exception('Error al intentar encontrar la variable '.$v);
        }
    }

    public function calcularVariable($var, $evalMath, $serv, $periodo)
    {
        if ($var instanceof Liquidacion_Model_Variable_Concepto) {
            $e = 'concepto_liquidado';
        } else {
            $e = 'variable_calculada';
        }

        // calculo el concepto(variable)
        $resultado = $var->calcular($evalMath, $serv);

        // disparo el evento
        $this->_eventDispatcher->fire($e, $resultado, $serv, $evalMath, $var, $periodo);

        return $resultado;
    }
}
