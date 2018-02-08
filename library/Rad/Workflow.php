<?php
/**
 * Workflow
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */

/**
 * Workflow
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Workflow
 * @author Martin Alejandro Santangelo
 */
class Rad_Workflow
{
    /**
     * array de configuracion del workflow
     * @var array 
     */
    protected $_nodos;

    /**
     * tipo de entrada que acepta el nodo 
     * son jerarquicos: por ejemplo 'Row\Cliente'
     * @var string 
     */
    protected $_tipoEntrada;

    /**
     * Prefijo de las clases que forman parte del workflow
     * @var string 
     */
    protected $_prefijoClases = 'Workflow_';

    /**
     * Prefijo de las clases que forman parte del workflow
     * @var string 
     */
    protected $_prefijoClasesReservadas = 'Rad_Workflow_Nodo_';

    /**
     * Pizarron
     * @var Rad_Workflow_Pizarron 
     */
    protected $_pizarron;


    /**
     * Nombres de nodos reservados
     * @var array 
     */
    protected static $_nombresReservados = array(
        'Condicional',
        'Secuencia',
        'Log',
        'LeerPizarron',
        'EscribirPizarron',
        'MostrarError',
        'CondicionalPizarron'
    );
    
    public function setTipoEntrada($tipo)
    {
        $this->_tipoEntrada = $tipo;
    }

    public function getTipoEntrada()
    {
        return $this->_tipoEntrada;
    }

    public function getPizarron()
    {
        return $this->_pizarron;
    }

    public function __construct()
    {
        $this->_pizarron = new Rad_Workflow_Pizarron();
    }

    /**
     * Carga el arbol 
     */
    public function cargar($nodos)
    {
        $this->_nodos = $nodos;
        // Ejemplo
        /*
        $w = array(
            'Nodo' => 'Condicional',
            'Condicion' => 'Comprobante_EsFacturaVenta',
            'Salida' => array(
                'No' => array(
                    'Nodo'   => 'Secuencia',
                    'Salida' => array(
                        array(
                            'Nodo'   => 'Comprobante_ObtenerPersona',
                            'Salida' =>  array(
                                'Nodo' => 'Condicional',
                                'Condicion' => 'Persona_TieneLimiteDeCredito and (Persona_EsProveedor and Persona_EsProveedor)',
                                'Salida' =>  array(
                                    'Si' => true,
                                    'No' => false
                                )
                            )
                        ),
                        array(
                            'Nodo'   => 'Comprobante_TraerDetalles',
                            'Salida' =>   array(
                                'Nodo'   => 'Log',
                                'Salida' => true
                            )
                        )
                    )
                    
                ),
                'Si'=> array(
                   'Nodo'   => 'EscribirPizarron',
                   'Parametro'=> 'Compo',
                   'Salida' => array(
                        'Nodo'   => 'LeerPizarron',
                        'Parametro' => 'Compo',
                        'Salida' => array(
                            'Nodo' => 'Log'
                        )
                   )
                )
            )

        );

        /*
        $this->_nodos = array(
            'Nodo'   => 'VerificarFacturaVenta',
            'Salida' => array(
                'Nodo'   => 'ObtenerPersona',
                'Salida' =>  array(
                    'Nodo' => 'Condicional',
                    'Condicion' => 'TieneLimiteDeCredito',
                    'Salida' =>  array(
                        'Si'   =>  array(
                            'Nodo'    => 'MostrarError',
                            'Mensaje' => 'Este cliente tiene limite de Credito',
                            'Salida'  => null  
                        ),
                        'No'   =>  array(
                            'Nodo'    => 'MostrarError',
                            'Mensaje' => 'Este cliente No tiene limite de Credito',
                            'Salida'  => null  
                        )
                    )
                )
            )
        );
            /*
            'Nodo'   => 'ObtenerCliente',
            'Salida' =>  array(
                'Nodo'   => 'Secuencia',
                'Salida' =>  array(
                    array(
                        'Nodo'   => 'VerificarLimiteCreditoCliente',
                        'Salida' => null  
                    ),
                    array(
                        'Nodo'   => 'VerificarLimiteCreditoGenerico',
                        'Salida' => null  
                    )
                )
            )*/
    }

    public function verificar() {
        //TODO: verifica que el workflow sea coherente
    }

    /**
     * Procesa los nodo de forma iterativa
     */
    protected function _procesar($data, $nodoCfg, $tipoSalidaPadre) {

        $class = $this->getNodeClass($nodoCfg['Nodo']);

        // Creo el nodo
        $nodo  = new $class($nodoCfg, $this->_pizarron, $tipoSalidaPadre);

        Rad_Log::debug('ejecutando '.$class);

        // Ejecuto el Nodo
        $rtnData = $nodo->procesar($data);
		
        // obtengo el tipo de salida del nodo
        $tipoSalida = $nodo->getTipoSalida();

        // de no tener entonces tomo la del padre
        if (!$tipoSalida) {
            $tipoSalida = $tipoSalidaPadre;
        }


        // Si el nodo es un iterador
        if ($nodo instanceof Rad_Workflow_Nodo_Iterador) {
            $sigNodoCfg = $nodoCfg['Salida'];
            $classSig   = $this->getNodeClass($sigNodoCfg['Nodo']);
            // verifico compatibilidad de tipos
            $this->_tiposCompatibles($tipoSalida, $classSig);
            

            foreach ($rtnData as $dataSig) {
                //paso el control al siguiente nodo
                $this->_procesar($dataSig, $sigNodoCfg, $tipoSalida);
            }

        } else if ($nodo instanceof Rad_Workflow_Nodo_Secuencia) {
            // Es un nodo Secuencia? Ejecuto todos los hijos secuencialmente...

            foreach ($nodoCfg['Salida'] as $sigNodoCfg) {
                $classSig   = $this->getNodeClass($sigNodoCfg['Nodo']);
                // verifico compatibilidad de tipos
                $this->_tiposCompatibles($tipoSalida, $classSig);

                //paso el control al siguiente nodo
                $rtnDataSec = $this->_procesar($rtnData, $sigNodoCfg, $tipoSalida);
                Rad_Log::debug($rtnDataSec);
                if ($rtnDataSec === false) break;
            }

        } else {
            // Es un selector
            if ($nodo instanceof Rad_Workflow_Nodo_Selector) {
                
                $salida = $nodo->getResultado();

                $sigNodoCfg = $nodoCfg['Salida'][$salida];

            // es tipo nodo
            } else if ($nodo instanceof Rad_Workflow_Nodo || $nodo instanceof Rad_Workflow) {
                $sigNodoCfg = $nodoCfg['Salida'];

            } else {
                throw new Rad_Workflow_Exception("El tipo de nodo es desconocido");
            }

            // si no hay siguiente nodo termino
            if ($sigNodoCfg === null){
                return $rtnData;   
            }

            // si es un boleano retorno directamente
            if (is_bool($sigNodoCfg)){
                return $sigNodoCfg;   
            }

            $classSig   = $this->getNodeClass($sigNodoCfg['Nodo']);

            // verifico compatibilidad de tipos
            $this->_tiposCompatibles($tipoSalida, $classSig);

            //paso el control al siguiente nodo
            return $this->_procesar($rtnData, $sigNodoCfg, $tipoSalida);
        }
    }

    protected function getNodeClass($nombre)
    {
        if (in_array($nombre, static::$_nombresReservados)){
            return $this->_prefijoClasesReservadas.$nombre;
        } else {
            return $this->_prefijoClases.$nombre;
        }
    }

    /**
     *  Verifica la compatibilidad entre la salida de un nodo y la entrada del otro
     */
    protected function _tiposCompatibles($tipoSalida, $entrada) {
        $tipoEntrada = $entrada::getTipoEntrada();

        $lenEntrada = strlen($tipoEntrada)+1;

        // si el nodo acepta cualquier entrada entonces es compatible y salgo
        if ($tipoEntrada === null) return;

        if ($tipoEntrada == $tipoSalida) return;

        if ($tipoEntrada."\\"== substr($tipoSalida, 0, $lenEntrada)) return;

        throw new Rad_Workflow_Exception("El nodo '$entrada' tiene una entrada tipo '$tipoEntrada' y no es compatible con la salida $tipoSalida");
    }

    public function procesar($data)
    {
        if (!is_array($this->_nodos) || empty($this->_nodos)) throw new Rad_Workflow_Exception("El workflow esta vacio y no puede ejecutarse!");
        if (!$this->_tipoEntrada) throw new Rad_Workflow_Exception("Tipo de entrada no definida el workflow no puede ejecutarse!");
        
        $class = $this->getNodeClass($this->_nodos['Nodo']);

        $this->_tiposCompatibles($this->_tipoEntrada, $class);
        
        return $this->_procesar($data, $this->_nodos, $this->_tipoEntrada);
    }
}