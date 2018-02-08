<?php

class Liquidacion_Model_Liquidador_PostProcess_AjustePorRedondeo extends Liquidacion_Model_Liquidador_PostProcess
{
    public function execute(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $agrupacion = null, $valor = null)
    {
        // Recupero todos los recibos del mes

        /*
        array('EMPRESA', 'CONVENIO', 'CATEGORIA', 'GRUPO_PERSONAS', 'SERVICIO', 'GENERICO');
        */
        /*
        Rad_Log::debug("------------------- Ajuste por Redondeo -------------------");
        Rad_Log::debug($tipo);
        Rad_Log::debug($agrupacion);
        Rad_Log::debug($valor);
        */
        $and = "";

        if ($agrupacion && $valor) {
            switch ($agrupacion) {
                case 'SERVICIO':
                    $and = "AND Servicio = $valor";
                    break;
                /*
                 case 'EMPRESA':
                    $and = "AND Servicio in (Select Id from Servicios where Empresa = '$valor'";
                    break;
                */
                default:
                    // # code...
                    break;
            }
        }

        $empresaId = $liquidacion->Empresa;
        $periodoId = $periodo->getId();

        $db     = Zend_Registry::get('db');
        $M_LR   = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibos);

        $R_LR = $M_LR->fetchAll("Ajuste = 0 and Periodo = $periodoId and Servicio in (Select Id from Servicios where Empresa = $empresaId) $and ");

        if ($R_LR) {

            $M_LRD       = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles);
            $M_Concepto  = Service_TableManager::get(Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles);

            foreach ($R_LR as $row) {

                $sql = "SELECT Sum(Monto) as monto FROM LiquidacionesRecibosDetalles where LiquidacionRecibo = {$row['Id']} and VariableDetalle <> 505";

                $monto = $db->fetchOne($sql);

                if ($monto) {

                    $monto = round($monto, 2, PHP_ROUND_HALF_UP);

                    $redondeo = ceil($monto) - $monto;
                    
                    // para que no sume 1
                    if ($redondeo > 0.999) $redondeo = 0;

                    $d = array(     'LiquidacionRecibo'   => $row['Id'],
                                    'VariableDetalle'     => 505,
                                    'Monto'               => round($redondeo, 2, PHP_ROUND_HALF_UP),
                                    'MontoCalculado'      => round($redondeo, 2, PHP_ROUND_HALF_UP),
                                    'PeriodoDevengado'    => $periodoId ,
                                    'Detalle'             => '',
                                    'ConceptoCodigo'      => $M_Concepto->getCodigo(505),
                                    'ConceptoNombre'      => $M_Concepto->getNombre(505)
                        );

                    $M_LRD->delete("LiquidacionRecibo = {$row['Id']} and VariableDetalle = 505");

                    $M_LRD->insert($d);

                }
            }
        }
    }
}