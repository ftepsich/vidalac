<?php

use Rad\Util\Math\Evaluator;
use Rad\Util\Math\Expresion\Func;

/**
 * Liquidacion_Model_Evaluator
 *
 * Evaluador matematico utilizado para liquidacion de sueldos,
 * agrega funciones especificas para dicha tarea
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_VariableCollection
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Evaluator extends Evaluator
{
    public function __construct($periodo, $servicio)
    {
        parent::__construct();
        // Agrego funcion de lectura de tablas al evaluador matematico
        $this->addFunction(
            new Func(
                'fTabla',
                function ($tabla, $valor) use ($periodo){
                    return Rrhh_Model_DbTable_LiquidacionesTablas::getValor($tabla, $valor, $periodo);
                }
            )
        );

        // Agrego funcion de lectura de categoria al evaluador matematico
        $this->addFunction(
            new Func(
                'fBasicoCat',
                function ($categoria) use ($periodo){
                    $m = Service_TableManager::get('Rrhh_Model_DbTable_ConveniosCategoriasDetalles');
                    return $m->getBasicoCategoria($categoria, $periodo);
                }
            )
        );

        // Agrego funcion de lectura de categoria NR al evaluador matematico
        $this->addFunction(
            new Func(
                'fBasicoCatNR',
                function ($categoria) use ($periodo){
                    $m = Service_TableManager::get('Rrhh_Model_DbTable_ConveniosCategoriasDetalles');
                    return $m->getBasicoNRCategoria($categoria, $periodo);
                }
            )
        );

        $this->addFunction(
            new Func(
                'fServicioCaracteristica',
                function ($caracteristica) use ($servicio){
                    $CV     = Service_TableManager::get('Model_DbTable_CaracteristicasValores');
                    $modelo = 'Rrhh_Model_DbTable_Servicios';
                    return  $CV->getValor($servicio->Id, $caracteristica, $modelo);
                }
            )
        );

        $this->addFunction(
            new Func(
                'fEsAfiliado',
                function ($idOrganismo) use ($servicio,$periodo){
                    $m = Service_TableManager::get('Rrhh_Model_DbTable_PersonasAfiliaciones');
                    return $m->esAfiliado($servicio, $periodo, null, $idOrganismo);
                }
            )
        );

        $this->addFunction(
            new Func(
                'fSumRemunerativosPorMes',
                function ($anio,$mesInicio,$mesFin,$afiliacion,$organismo) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    /* Recorro mes a mes el año teniendo en cueta los meses */
                    /* Hago asi por el maximo ajuste que se hizo cada mes.  */


                    $monto = 0;

                    if ($afiliacion) {
                        $resultadoAfiliacion = ">= 1";
                    } else {
                        $resultadoAfiliacion = "= 0";
                    }

                    For($mes = $mesInicio; $mes <= $mesFin; $mes++) {

                        $sqlAfiliacion = '';
                        if ($organismo) {

                            $sqlAfiliacion = "
                                    AND (
                                    SELECT  count(PA.Id)
                                    FROM        PersonasAfiliaciones PA
                                    INNER JOIN  LiquidacionesPeriodos LP2 on LP2.Anio = $anio and LP2.Valor = $mes
                                    WHERE   PA.Organismo    = $organismo
                                    AND     PA.Persona      = {$servicio->Persona}
                                    AND     PA.FechaAlta    <= LP2.FechaHasta
                                    AND     ifnull(PA.FechaBaja,'2199-01-01') > LP2.FechaDesde
                                    ) $resultadoAfiliacion
                            ";
                        }


                        $sql = "    SELECT  ifnull(SUM(LRD.Monto),0)
                                    FROM    LiquidacionesRecibosDetalles LRD
                                    INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                    INNER JOIN Variables V              on V.Id  = VD.Variable
                                    INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                    INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                                    INNER JOIN Servicios S              on S.Id  = LR.Servicio AND S.TipoDeJornada in (1,2) /* Solo los de media jornada y reducida */
                                    WHERE   V.TipoDeConceptoLiquidacion    in (1,2) -- Remunerativos y Rem.Agrupados
                                    AND     LR.Ajuste = (
                                            SELECT   MAX(LR1.Ajuste)
                                            FROM    LiquidacionesRecibos LR1
                                            WHERE   LR1.Periodo  = (SELECT  LP1.Id
                                                                    FROM    LiquidacionesPeriodos LP1
                                                                    WHERE   LP1.Anio = $anio
                                                                    AND     LP1.Valor = $mes)
                                            AND     LR1.Persona  = {$servicio->Persona}
                                            AND     LR1.Servicio = LR.Servicio
                                    )
                                    -- AND     ifnull(V.EsSAC,99)  <> 1
                                    AND     LR.Persona  = {$servicio->Persona}
                                    -- AND     LR.Servicio = {$servicio->Id} /* no poner para que tome cuando cambia de servicio */
                                    AND     LP.Anio     = $anio
                                    AND     LP.Valor    = $mes
                                    $sqlAfiliacion
                        ";

                        $monto = $monto + $db->fetchOne($sql);
                    }
                    return $monto;
                }
            )
        );

        $this->addFunction(
            new Func(
                'fSumConceptoPorMes',
                function ($anio,$mesInicio,$mesFin,$concepto) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    /* Recorro mes a mes el año teniendo en cueta los meses */
                    /* Hago asi por el maximo ajuste que se hizo cada mes.  */

                    $monto = 0;

                    For($mes = $mesInicio; $mes <= $mesFin; $mes++) {

                        $sql = "    SELECT  ifnull(SUM(LRD.Monto),0)
                                    FROM    LiquidacionesRecibosDetalles LRD
                                    INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                    INNER JOIN Variables V              on V.Id  = VD.Variable
                                    INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                    INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                                    INNER JOIN Servicios S              on S.Id  = LR.Servicio AND S.TipoDeJornada in (1,2) /* Solo los de media jornada y reducida */
                                    WHERE   V.Id = $concepto
                                    AND     LR.Ajuste = (
                                            SELECT   MAX(LR1.Ajuste)
                                            FROM    LiquidacionesRecibos LR1
                                            WHERE   LR1.Periodo  = (SELECT  LP1.Id
                                                                    FROM    LiquidacionesPeriodos LP1
                                                                    WHERE   LP1.Anio = $anio
                                                                    AND     LP1.Valor = $mes)
                                            AND     LR1.Persona  = {$servicio->Persona}
                                            AND     LR1.Servicio = LR.Servicio
                                    )
                                    -- AND     ifnull(V.EsSAC,99)  <> 1
                                    AND     LR.Persona  = {$servicio->Persona}
                                    -- AND     LR.Servicio = {$servicio->Id} /* no poner para que tome cuando cambia de servicio */
                                    AND     LP.Anio     = $anio
                                    AND     LP.Valor    = $mes

                        ";

                        $monto = $monto + $db->fetchOne($sql);
                    }
                    return $monto;
                }
            )
        );

        /**
        *   Esta funcion devuelve el valor promedio mas alto de los conceptos variables
        *   para el calculo de las vacaciones.
        */
        $this->addFunction(
            new Func(
                'fMaxPromedioConceptosVariable',
                function ($anioVacaciones) use ($servicio,$periodo){

                    $db             = Zend_Registry::get("db");
                    $mesPeriodo     = $periodo->getMes();
                    $anioPeriodo    = $periodo->getAnio();

                    $sql = "    SELECT  Sum(LRD.Monto)
                                FROM    LiquidacionesRecibosDetalles LRD
                                INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                INNER JOIN Variables V              on V.Id  = VD.Variable
                                INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                WHERE   V.TipoDeConcepto    = 9 -- Conceptos Variables
                                AND     LR.Ajuste           = 0
                                AND     V.NoHabitual        = 1
                                AND     ifnull(V.EsSAC,99)  <> 1
                                AND     LR.Persona          = {$servicio->Persona}
                    ";

                    // modif 12/12/2016 en el anual tiene que tener en cueta las cosas hasta el mes anterior al actual
                    $whereAnual     = " AND LRD.PeriodoDevengado in (SELECT Id FROM LiquidacionesPeriodos WHERE Anio = $anioVacaciones and Valor < $mesPeriodo) ";
                    // Ultimos 6 meses a contar de este periodo

                    if ($mesPeriodo > 6) {
                        $whereMensual   = " AND LRD.PeriodoDevengado in (
                                                    SELECT  Id
                                                    FROM    LiquidacionesPeriodos
                                                    WHERE   Anio = $anioPeriodo
                                                    AND     Valor BETWEEN ".($mesPeriodo-5-1)." AND ".($mesPeriodo-1)."
                                                    )";
                    } else {
                        if ($mesPeriodo == 1) {
                            $whereMensual   = " AND LRD.PeriodoDevengado in (
                                                        SELECT  Id
                                                        FROM    LiquidacionesPeriodos
                                                        WHERE   Anio = ".($anioPeriodo-1)."
                                                        AND     Valor BETWEEN 7 AND 12
                                                        )";
                        } else {
                            $whereMensual   = " AND LRD.PeriodoDevengado in (
                                                        SELECT  Id
                                                        FROM    LiquidacionesPeriodos
                                                        WHERE   (Anio = $anioPeriodo AND Valor BETWEEN 1 AND ".($mesPeriodo-1)." )
                                                        OR      (Anio = ".($anioPeriodo-1)." AND Valor BETWEEN ".(12-(6-$mesPeriodo))." AND 12) )";
                        }
                    }

                    $promedioDiarioAnual            = 0;
                    $promedioDiarioSeisUltimosMeses = 0;

                    // controlo para que nos de igual que a ellos que el mes sea mayor a 04/2014
                    // se comento por maxi preguntar a pablo si realmente va --- se contempla ya en el $whereMensual --> (Pablo dice SI VA !!!!)

                    if (($anioPeriodo == 2014 && $mesPeriodo >= 10) || ($anioPeriodo > 2014)) {
                        $anual = $db->fetchOne($sql.$whereAnual);
                        
                        // ver bien esto pero es logico que sea asi
                        if ($anioPeriodo == $anioVacaciones) {
                            $promedioDiarioAnual = $anual / ($mesPeriodo-1) / 25;
                        } else {
                            $promedioDiarioAnual = $anual / 12 / 25;
                        }
                        
                    } else {
                        $anual = null;
                        $promedioDiarioAnual = 0;
                    }

                    $seisUltimosMeses = $db->fetchOne($sql.$whereMensual);
                    if (!$seisUltimosMeses) {
                        $promedioDiarioSeisUltimosMeses = 0;
                    } else {
                        $promedioDiarioSeisUltimosMeses = $seisUltimosMeses / 6 / 25;
                    }

                    return ( $promedioDiarioAnual > $promedioDiarioSeisUltimosMeses ) ? $promedioDiarioAnual : $promedioDiarioSeisUltimosMeses;
                }
            )
        );

/*
        $this->addFunction(
            new Func(
                'fSumConceptosVariableAnioVacaciones',
                function ($anioVacaciones) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    $sql = "    SELECT  Sum(LRD.Monto)
                                FROM    LiquidacionesRecibosDetalles LRD
                                INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                INNER JOIN Variables V              on V.Id  = VD.Variable
                                INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                WHERE   V.TipoDeConcepto    = 9 -- Conceptos Variables
                                AND     LR.Ajuste           = 0
                                AND     V.NoHabitual        = 1
                                AND     LR.Persona          = {$servicio->Persona}
                                AND     LRD.PeriodoDevengado in (SELECT Id FROM LiquidacionesPeriodos WHERE Anio = $anioVacaciones) ";

                    $anual = $db->fetchOne($sql);

                    if(!$anual)  $anual = 0;
                    return $anual;
                }
            )
        );

       $this->addFunction(
            new Func(
                'fSumConceptosVariablesUltimosSeisMeses',
                function ($anioVacaciones) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    $sql = "    SELECT  Sum(LRD.Monto)
                                FROM    LiquidacionesRecibosDetalles LRD
                                INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                INNER JOIN Variables V              on V.Id  = VD.Variable
                                INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                WHERE   V.TipoDeConcepto    = 9 -- Conceptos Variables
                                AND     LR.Ajuste           = 0
                                AND     V.NoHabitual        = 1
                                AND     LR.Persona          = {$servicio->Persona}
                    ";

                    $mesPeriodo     = $periodo->getMes();
                    $anioPeriodo    = $periodo->getAnio();
                    if ($mesPeriodo >= 6) {
                        $whereMensual   = " AND LRD.PeriodoDevengado in (
                                                    SELECT  Id
                                                    FROM    LiquidacionesPeriodos
                                                    WHERE   Anio = $anioVacaciones
                                                    AND     Valor BETWEEN ".($mesPeriodo-5)." AND $mesPeriodo)";
                    } else {
                        $whereMensual   = " AND LRD.PeriodoDevengado in (
                                                    SELECT  Id
                                                    FROM    LiquidacionesPeriodos
                                                    WHERE   (Anio = $anioPeriodo AND Valor BETWEEN 1 AND $mesPeriodo)
                                                    OR      (Anio = ".($anioPeriodo-1)." AND Valor BETWEEN ".(12-(6-$mesPeriodo))." AND 12) )";
                    }

                    $seisUltimosMeses = $db->fetchOne($sql.$whereMensual);
                    if (!$seisUltimosMeses) $seisUltimosMeses = 0;
                    return $seisUltimosMeses;
                }
            )
        );
*/

        /**
        * fMontoRemunerativo
        * OJO... esta funcion devuelve el monto del Remunerativo hasta ese momento con lo cual si se calcula otro
        * Remunerativo despues del concepto que llame a esta funcion no sera tenido en cuenta para esta funcion.
        * Se usa en la liq definitiva cuanddo esta en el recibo comun
        */
        $this->addFunction(
            new Func(
                'fMontoRemunerativo',
                function () {
                    return Liquidacion_Model_Variable_Concepto_Remunerativo::getSum();
                }
            )
        );

        $this->addFunction(
            new Func(
                'absx',
                function ($val) {
                    // return abs($val);
                    return str_replace('-','',$val);
                }
            )
        );

        $this->addFunction(
            new Func(
                'fAcumuladoGananciaRetencionesAMes',
                function ($anio,$mes) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    $sql = "    SELECT  ifnull(Sum(abs(LRD.Monto)),0)
                                FROM    LiquidacionesRecibosDetalles LRD
                                INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                INNER JOIN Variables V              on V.Id  = VD.Variable
                                INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                                WHERE   V.TipoDeConcepto    = 13 -- Retenciones
                                AND     LR.Ajuste           = 0
                                AND     LP.Anio             = $anio
                                AND     LP.Valor            BETWEEN 1 AND $mes
                                AND     LR.Persona          = {$servicio->Persona}
                            ";

                    $monto = $db->fetchOne($sql);

                    return $monto;
                }
            )
        );

        $this->addFunction(
            new Func(
                'fAcumuladoGananciaDevolucionesAMes',
                function ($anio,$mes) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    $sql = "    SELECT  ifnull(Sum(abs(LRD.Monto)),0)
                                FROM    LiquidacionesRecibosDetalles LRD
                                INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                INNER JOIN Variables V              on V.Id  = VD.Variable
                                INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                                WHERE   V.TipoDeConcepto    = 14 -- Devolucion
                                AND     LR.Ajuste           = 0
                                AND     LP.Anio             = $anio
                                AND     LP.Valor            BETWEEN 1 AND $mes
                                AND     LR.Persona          = {$servicio->Persona}
                            ";

                    $monto = $db->fetchOne($sql);

                    return $monto;
                }
            )
        );

        $this->addFunction(
            new Func(
                'fAcumuladoGananciaBeneficiosAMes',
                function ($anio,$mes) use ($servicio,$periodo){

                    $db = Zend_Registry::get("db");

                    $sql = "    SELECT  ifnull(Sum(abs(LRD.Monto)),0)
                                FROM    LiquidacionesRecibosDetalles LRD
                                INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                                INNER JOIN Variables V              on V.Id  = VD.Variable
                                INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                                INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                                WHERE   V.TipoDeConcepto    = 22 -- Beneficios
                                AND     LR.Ajuste           = 0
                                AND     LP.Anio             = $anio
                                AND     LP.Valor            BETWEEN 1 AND $mes
                                AND     LR.Persona          = {$servicio->Persona}
                            ";

                    $monto = $db->fetchOne($sql);

                    return $monto;
                }
            )
        );


    }
}