<?php
require_once 'LiquidadorServicio.php';

/**
 * Liquidacion_Model_Liquidador
 *
 * Clase encargada de la ejecucion de una liquidacion a un grupo de servicios
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Liquidador
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Liquidador
{
    public function __construct(Liquidacion_Model_LiquidadorServicio $liq)
    {
        $this->_liquidador = $liq;
    }
    /**
     * Liquida el periodo especificado para un grupo dado (todos si no se pasa grupo)
     * @param int|Row  $periodo
     * @param int      $tipo
     * @param Row      $liquidacion
     * @param string   $jerarquia
     * @param mixed    $valor
     */
    // public function liquidar(Liquidacion_Model_Periodo $periodo, $tipo,Liquidacion_Model_DbTable_Liquidaciones $liquidacion, $jerarquia = null, $valor = null)
    public function liquidar(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $jerarquia = null, $valor = null)
    {
        $this->_liquidar($periodo, $tipo, $liquidacion, $jerarquia, $valor);
    }

    /**
     * Liquida el periodo especificado para un grupo dado (todos si no se pasa grupo)
     * @param int|Row  $periodo
     * @param string   $jerarquia
     * @param mixed    $valor
     */
    protected function _liquidar(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $jerarquia = null, $valor = null)
    {

        // instancio servicios
        $modelServicios = Service_TableManager::get('Rrhh_Model_DbTable_Servicios');

        // obtenemos los servicios activos en el periodo (fitrados por jerarquia si se pasan los parametros)
        $servicios = $modelServicios->getServiciosPeriodo($periodo, $jerarquia, $valor, $liquidacion);
        //Rad_Log::debug(print_r($servicios, true));
        //throw new Liquidacion_Model_Exception(print_r($servicios->toArray(), true));
        foreach ($servicios as $serv) {
            try {

                $this->_liquidador->liquidarServicio($serv, $periodo, $liquidacion);

            } catch(Exception $e) {
                // throw new Liquidacion_Model_Exception('Error al liquidar (Servicio '.$serv->Id.', Periodo '.$periodo->getId().'):'.$e->getMessage(),$e->getCode(),$e);
                throw $e;
            }
        }
    }
}
