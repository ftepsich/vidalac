<?php

/**
 * Liquidacion_Model_LiquidadorRetroactivos
 *
 * se encarga de liquidar los retroactivos
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_LiquidadorRetroactivos
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_LiquidadorRetroactivos extends Liquidacion_Model_Liquidador
{
    /**
     * Liquida el periodo especificado para un grupo dado (todos si no se pasa grupo)
     * @param int|Row  $periodo
     * @param string   $jerarquia
     * @param mixed    $valor
     */
    protected function _liquidar(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $jerarquia = null, $valor = null)
    {

        //TODO: tiene que traer los servicios y periodos a liquidar por retroactivos

        $modelPeriodo       = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesPeriodos');
        $modelServicios     = Service_TableManager::get('Rrhh_Model_DbTable_Servicios');
        $modelNovedades     = Service_TableManager::get('Liquidacion_Model_DbTable_NovedadesDeLiquidaciones');
        $modelLiquidaciones = Service_TableManager::get('Liquidacion_Model_DbTable_Liquidaciones');

        // Ojo aca se pasa jerarquia y valor ya que se puede estar liquidando un subconjunto de personas por alguna jerarquia
        // o simplemente reliquidando a una persona nomas
        $retros = $modelNovedades->getRetroactivos($periodo, $liquidacion, $jerarquia, $valor);

        // Rad_Jobs_Base::log('Retros: '.print_r($retros,true));

        /*
        
        Lo cambio para que no se pise las variables que llamo del array con las que me llegan a la funcion ya que las necesito
        
        foreach ($retros as $periodo => $servicios) {
            foreach ($servicios as $servicio => $liqs) {
                foreach ($liqs as $liq => $v) {        

         */
        foreach (           $retros                 as $periodoR     => $serviciosRetros) {
            foreach (       $serviciosRetros        as $servicioR    => $liquidacionesRetros) {
                foreach (   $liquidacionesRetros    as $liquidacionR => $v) {

                    $s = $modelServicios->find($servicioR)->current();

                    $pActualFD = $periodo->getDesde()->format('Y-m-d');
                    $pActualFH = $periodo->getHasta()->format('Y-m-d');

                    $sFD = new DateTime($s->FechaAlta);
                    $sFD = $sFD->format('Y-m-d');

                    if ($s->FechaBaja) {
                        $sFH = new DateTime($s->FechaBaja);
                    } else {
                        $sFH = new DateTime('2999-01-01');
                    }
                    $sFH = $sFH->format('Y-m-d');

                    // Verifico que al periodo que se le esta calculando un retro exista este periodo 
                    if ($sFH >= $pActualFD && $sFD <= $pActualFH ) {

                        $p = $modelPeriodo->getPeriodo($periodoR);
                        $l = $modelLiquidaciones->find($liquidacionR)->current();

                        $this->_liquidador->liquidarServicio($s, $p, $l);

                    }
                    // Hay que verificar que ese servicio este activo este mes
                    // OJO cuando cambian de servicio... en este caso voy a controlar
                    // que no tenga mas servicios en la misma empresa en este periodo

                }
            }
        }
    }
}