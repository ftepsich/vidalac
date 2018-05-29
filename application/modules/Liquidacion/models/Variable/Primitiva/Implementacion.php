<?php

/**
 * Liquidacion_Model_Variable_Primitiva_Implementacion
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable_Primitiva_Implementacion
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Variable_Primitiva_Implementacion
{
    protected static $_impl;

    public static function init()
    {
        $db = Zend_Registry::get("db");
        self::$_impl = array(

    /* ============================================================================================
        PRIMITIVAS de la ejecucion
    ============================================================================================ */
    /**
    *   Primitiva montoVacaciones
    *   Retorna la suma de los pagos por vacaciones en el recivo actual
    */
    '@rcMontoPagoVacaciones' => function() { return abs(Liquidacion_Model_Variable_Concepto::getSumTipo(19)); },
    /**
    *   Primitiva montoInasistenciaVacaciones
    *   Retorna la suma de los montos de pagos normales por los dias de vacaciones en el recivo actual
    */
    '@rcMontoInasistenciaVacaciones' => function() { return abs(Liquidacion_Model_Variable_Concepto::getSumTipo(17)); },



    /* ============================================================================================
        PRIMITIVAS
    ============================================================================================ */
            /**
            *   Primitiva Servicio
            *   Retorna el id de un servicio
            */
            '@servicio' => function($servicio) {
                return $servicio->Id;
            },
            /**
            *   Primitiva tipoLiquidacion
            *   Retorna el id de un tipo de Liquidacion
            *   En el caso que se llame desde el liquidador de Prueba que no tiene liquidacion devuelve 1 que es la liq normal
            */
            '@tipoLiquidacion' => function() {
                $liq = Liquidacion_Model_Liquidar::getLiquidacion();
                if ($liq) $L = $liq->TipoDeLiquidacion; else $L = 1;
                return $L;
            },
            /**
            *   Primitivas que son Constantes de los tipos de liq
            *   Retorna el tipo de liquidacion correspondiente al nombre
            */
            '@liqNormal'    => function() { return 1; },
            '@liqSAC'       => function() { return 2; },
            '@liqFinal'     => function() { return 3; },
            '@liqEspecial'  => function() { return 4; },
            /**
            *   Primitiva tipoLiquidacion
            *   Retorna el id de un tipo de Liquidacion
            */
            '@SumRemunerativosReciboNormal' => function($servicio) use ($db) {
                $liq        = Liquidacion_Model_Liquidar::getLiquidacion();
                if ($liq) {
                    $idPeriodo  = $liq->LiquidacionPeriodo;
                    $idServicio = $servicio->Id;
                    $sql = "    SELECT      ifnull(Valor,0) as Valor 
                                FROM        LiquidacionesVariablesCalculadas LVC
                                INNER JOIN  LiquidacionesRecibos LR         on LR.Id = LVC.LiquidacionRecibo
                                INNER JOIN  Liquidaciones L                 on L.Id  = LR.Liquidacion
                                WHERE       L.TipoDeLiquidacion     = 1
                                AND         LVC.Nombre              = '@sumRemunerativos'
                                AND         L.LiquidacionPeriodo    = $idPeriodo
                                AND         LR.Servicio             = $idServicio
                            ";
                    $S        = $db->fetchRow($sql);
                    return $S[Valor];
                } else {
                    // para el caso qeu se llame desde el liq de prueba
                    // no deberia suceder pero por las dudas
                    return 0;
                }
            },
            /**
            *   Primitiva SumRemunerativosReciboSAC
            *   Retorna la suma de los remunerativos del recibo de SAC cuando sac si paga separado
            */
            '@SumRemunerativosReciboSAC' => function($servicio) use ($db) {
                $liq        = Liquidacion_Model_Liquidar::getLiquidacion();
                if ($liq) {
                    $idPeriodo  = $liq->LiquidacionPeriodo;
                    $idServicio = $servicio->Id;
                    $sql = "    SELECT      ifnull(Valor,0) as Valor 
                                FROM        LiquidacionesVariablesCalculadas LVC
                                INNER JOIN  LiquidacionesRecibos LR         on LR.Id = LVC.LiquidacionRecibo
                                INNER JOIN  Liquidaciones L                 on L.Id  = LR.Liquidacion
                                WHERE       L.TipoDeLiquidacion     = 2
                                AND         LVC.Nombre              = '@sumRemunerativos'
                                AND         L.LiquidacionPeriodo    = $idPeriodo
                                AND         LR.Servicio             = $idServicio
                            ";
                    $S        = $db->fetchRow($sql);
                    return $S[Valor];
                } else {
                    // para el caso qeu se llame desde el liq de prueba
                    // no deberia suceder pero por las dudas
                    return 0;
                }
            },            
            /**
            *   Primitiva edad
            *   Retorna la edad... la hizo Martin... supongo que es un ejemplo
            */
            '@edad' => function($servicio, $periodo) {
                $persona = $servicio->findParentRow('Base_Model_DbTable_Personas');
                $fn      = new DateTime($persona->FechaNacimiento);
                $hoy     = $periodo->getHasta();
                $edad    = $hoy->diff($fn);
                return $edad->y;
            },
            /**
            *   Primitiva Servicio
            *   Retorna el id de un servicio
            */
            '@servicio' => function($servicio) {
                return $servicio->Id;
            },
            /**
            *   Primitiva Indice Sueldo
            *   Retorna el indice al que se debe liquidar un sueldo
            */
            '@indiceSueldo' => function($servicio, $periodo) use ($db) {

                $sql = "SELECT      TS.PorcentajePago, TS.Id
                        FROM        TiposDeJornadas TJ
                        INNER JOIN  TiposDeSueldos TS ON TJ.TipoDeSueldo = TS.Id
                        WHERE TJ.Id = ".$servicio['TipoDeJornada'];
                $S   = $db->fetchRow($sql);

                if (!$S) throw new Rad_Db_Table_Exception('Porcentaje de pago inexistente.');

                switch ($S['Id']) {
                    case 1: case 2: case 3:
                        return $S['PorcentajePago'];
                        break;
                    case 4:
                        // Retorna 0 para que anule el concepto Basico ya que este tipo de
                        // sueldo se paga con el concepto 135 Basico Proporciona por Horas
                        return 0;
                        break;
                    default:
                        throw new Rad_Db_Table_Exception('Porcentaje de pago inexistente.');
                        break; 
                }
            },
            /**
            *   Primitiva Tipo de indice de sueldo
            *   Retorna el id de la tabla Tipo de Sueldo
            *   1: Completo, 2: Media Jornada, 4: HS, 6: Reducida
            */
            '@tipoIndiceSueldo' => function($servicio, $periodo) use ($db) {

                $sql = "SELECT      TJ.TipoDeSueldo
                        FROM        TiposDeJornadas TJ
                        WHERE       TJ.Id = ".$servicio['TipoDeJornada'];
                $S   = $db->fetchRow($sql);

                if (!$S) throw new Rad_Db_Table_Exception('Forma de pago inexistente.');

                return $S['TipoDeSueldo'];
            },
            /**
            *   Primitiva HorasTrabajadasPeriodo
            *   Retorna la cantidad de horas trabajadas
            */
            '@horasTrabajadasPeriodo' => function($servicio, $periodo) use ($db){
                $sql    = "Select CantidadHoras From ServiciosHorasTrabajadas Where Servicio = $servicio->Id and LiquidacionPeriodo =". $periodo->getId() . " limit 1";
                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['CantidadHoras'];
            },
            /**
            *   Primitiva HorasFeriadosNoTrabajados
            *   Retorna la cantidad de horas en Feriados NO trabajados
            */
            '@horasFeriadosNoTrabajados' => function($servicio, $periodo) use ($db){
                $sql    = "Select CantidadHorasFeriadosNoTrabajados From ServiciosHorasTrabajadas Where Servicio = $servicio->Id and LiquidacionPeriodo =". $periodo->getId() . " limit 1";
                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['CantidadHorasFeriadosNoTrabajados'];
            },
            /**
            *   Primitiva HorasFeriadosTrabajados
            *   Retorna la cantidad de horas en Feriados Trabajados
            */
            '@horasFeriadosTrabajados' => function($servicio, $periodo) use ($db){
                $sql    = "Select CantidadHorasFeriadosTrabajados From ServiciosHorasTrabajadas Where Servicio = $servicio->Id and LiquidacionPeriodo =". $periodo->getId() . " limit 1";
                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['CantidadHorasFeriadosTrabajados'];
            },
            /**
            *   Primitiva HorasExtras50
            *   Retorna la cantidad de horas extras realizadas al 50%
            */
            '@horasExtras50' => function($servicio, $periodo) use ($db){

                $mes    = $periodo->getDesde()->format('n');
                $anio   = $periodo->getDesde()->format('Y');

                $sql    = " Select  Horas
                            From    ServiciosHorasExtras
                            Where   Servicio = $servicio->Id
                            and     Mes = $mes and Anio = $anio and TipoDeHoraExtra = 1 limit 1";

                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['Horas'];
            },
            /**
            *   Primitiva HorasExtras100
            *   Retorna la cantidad de horas extras realizadas al 100%
            */
            '@horasExtras100' => function($servicio, $periodo) use ($db){

                $mes    = $periodo->getDesde()->format('n');
                $anio   = $periodo->getDesde()->format('Y');

                $sql    = " Select  Horas
                            From    ServiciosHorasExtras
                            Where   Servicio = $servicio->Id
                            and     Mes = $mes and Anio = $anio and TipoDeHoraExtra = 2 limit 1";
                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['Horas'];
            },
            /**
            *   Primitiva HorasExtras50
            *   Retorna la cantidad de horas extras realizadas al 50% en dias inhabiles o fines de semana
            */
            '@horasExtras50i' => function($servicio, $periodo) use ($db){

                $mes    = $periodo->getDesde()->format('n');
                $anio   = $periodo->getDesde()->format('Y');

                $sql    = " Select  Horas
                            From    ServiciosHorasExtras
                            Where   Servicio = $servicio->Id
                            and     Mes = $mes and Anio = $anio and TipoDeHoraExtra = 3 limit 1";

                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['Horas'];
            },
            /**
            *   Primitiva HorasExtras100
            *   Retorna la cantidad de horas extras realizadas al 100% en dias inhabiles o fines de semana
            */
            '@horasExtras100i' => function($servicio, $periodo) use ($db){

                $mes    = $periodo->getDesde()->format('n');
                $anio   = $periodo->getDesde()->format('Y');

                $sql    = " Select  Horas
                            From    ServiciosHorasExtras
                            Where   Servicio = $servicio->Id
                            and     Mes = $mes and Anio = $anio and TipoDeHoraExtra = 4 limit 1";
                $R      = $db->fetchRow($sql);
                if (!$R) return 0;
                return $R['Horas'];
            },            
        /* ----------------------------------------------------------------------------------------
            GANANCIA
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva Basico
            *   Retorna el monto del basico de una categoria determinada para una fecha determinada
            */
            '@ajusteAnualGanancias' =>  function($servicio, $periodo) use ($db){

                // Al anio tengo que restarle uno ya que se trata del resumen del año anterior
                $anio       = $periodo->getDesde()->format('Y') - 1;
                $idPersona  = $servicio->Persona;
                $empresa    = $servicio->Empresa;
                $idPeriodo  = $periodo->getId();

                $sql = "    SELECT  L.Id as idLiquidacion, LR.Id as idRecibo
                            FROM    Liquidaciones L
                            INNER JOIN LiquidacionesRecibos LR on L.Id = LR.Liquidacion
                            INNER JOIN LiquidacionesPeriodos LP on LP.Id = L.LiquidacionPeriodo
                            WHERE   L.Empresa            = $empresa 
                            AND     LP.anio              = $anio
                            AND     LP.valor             = 12
                            AND     L.TipoDeLiquidacion  = 1
                            AND     LR.Ajuste            = 0
                            AND     LR.Persona           = $idPersona";

                $R = $db->fetchRow($sql);
                if ($R) {
                    $idLiquidacion  = $R['idLiquidacion'];
                    $idRecibo       = $R['idRecibo'];
                } else {
                    $idLiquidacion  = 1;
                    $idRecibo       = 1;                   
                }

                $G = Service_TableManager::get('Rrhh_Model_DbTable_PersonasGananciasLiquidacionesAnuales');
                $G->generarAjusteGananciasAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

                // Para los de Rango 1 le pongo 0 en el recibo, posteriormente hay que hacer qeu lo compence con 
                // un beneficio
                if ($G->getRangoDeducciones($idPersona) == 1) return 0;

                $sql = "    SELECT  MontoAcumulado
                            FROM    PersonasGananciasLiquidaciones
                            WHERE   GananciaConcepto    = 43 
                            AND     GananciaAnioPeriodo = $anio
                            AND     Persona             = $idPersona";

                $mes = " AND     GananciaMesPeriodo  = 12";
                $montoMes12 = $db->fetchOne($sql.$mes);
                $mes = " AND     GananciaMesPeriodo  = 13";
                $montoMes13 = $db->fetchOne($sql.$mes);

                $ajuste = $montoMes13 - $montoMes12;

                return $ajuste;
            },

        /* ----------------------------------------------------------------------------------------
            BASICO
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva Basico
            *   Retorna el monto del basico de una categoria determinada para una fecha determinada
            */
            '@basico' =>  function($servicio, $periodo) {
                return Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoServicio($servicio, $periodo);
            },
            /**
            *   Primitiva Basico No Remunerativo
            *   Retorna el monto No remunerativo del basico de una categoria determinada para una fecha determinada
            */
            '@basicoNR' =>  function($servicio, $periodo) {
                return Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoNRServicio($servicio, $periodo);
            },
        /* ----------------------------------------------------------------------------------------
            Despidos
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva maxDevengadoUltimos12Meses
            *   Retorna el monto del sueldo Bruto mas alto de los ultimos 12 meses
            */
            '@maxDevengadoUltimos12Meses' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getMaxDevengadoUltimos12Meses($servicio, $periodo, 1);
            },
            /**
            *   Primitiva mesMaxDevengadoUltimos12Meses
            *   Retorna el mes del sueldo Bruto mas alto de los ultimos 12 meses
            */
            '@mesMaxDevengadoUltimos12Meses' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getMaxDevengadoUltimos12Meses($servicio, $periodo, 2);
            },
            /**
            *   Primitiva antiguedad
            *   Retorna el valor de los anios trabajados en formato numerico (años nada mas).
            */
            '@aniosTrabajadosParaIndemnizacion' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getAniosTrabajadosParaIndemnizacion($servicio, $periodo);
            },

        /* ----------------------------------------------------------------------------------------
            SAC
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva SacSemestre 
            *   Retorna 1 si es el primer semestre y 2 si es el segundo
            */
            '@SacSemestre' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getSacSemestre($servicio, $periodo);
            },
            /**
            *   Primitiva aguinaldoDiasTrabajados
            *   Retorna la cantidad de dias que trabajo en un semestre, descontando aquellas situaciones de revista
            *   que no debe tener en cuenta para el SAC como ser maternidad
            */
            '@aguinaldoDiasTrabajados' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getAguinaldoDiasTrabajados($servicio, $periodo);
            },
            /**
            *   Primitiva aguinaldoMaxDevengado
            *   Retorna el mejor devengado a una persona en el semestre y lo fracciona segun trabajo en el mismo
            */
            '@aguinaldoMaxDevengado' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getAguinaldoDevengado($servicio, $periodo,1);
            },
            /**
            *   Primitiva SacSemestre
            *   Retorna el mejor devengado a una persona en el semestre y lo fracciona segun trabajo en el mismo
            */
            '@aguinaldoMesMaxDevengado' =>  function($servicio, $periodo) {
                return Base_Model_DbTable_Empleados::getAguinaldoDevengado($servicio, $periodo,2);
            },

        /* ----------------------------------------------------------------------------------------
            ANTIGUEDAD
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva antiguedad
            *   Retorna el valor de la antiguedad en formato numerico (años nada mas).
            *   Hay que ver de implementar la antiguedad reconocida a una fecha
            */
            '@antiguedad' =>  function($servicio, $periodo) {
                // return Base_Model_DbTable_Empleados::getAntiguedad($servicio, $periodo);
                return Base_Model_DbTable_Empleados::getAniosAntiguedadEstandar($servicio, $periodo);
            },
            /**
            *   Primitiva antiguedad Camioneros
            *   Retorna el valor de la antiguedad en formato numerico (años nada mas) para un camionero.
            *   Hay que ver de implementar la antiguedad reconocida a una fecha
            */
            '@antiguedadCamioneros' =>  function($servicio, $periodo) use ($db) {
                $M = Service_TableManager::get('Base_Model_DbTable_Empleados');
                // return $M->getAntiguedadCamioneros($servicio, $periodo);
                return $M->getAniosAntiguedadCamioneros($servicio, $periodo);
            },
        /* ----------------------------------------------------------------------------------------
            DIAS EN GENERAL
        ---------------------------------------------------------------------------------------- */

            /**
            *   Primitiva Mes de Liquidacion
            *   Devuelve el mes de liquidacion
            */
            '@mesLiquidacion' => function($servicio, $periodo) use ($db){
                $mes    = $periodo->getDesde()->format('n');
                return $mes;
            },
            /**
            *   Primitiva Anio de Liquidacion
            *   Devuelve el año de liquidacion
            */
            '@anioLiquidacion' => function($servicio, $periodo) use ($db){
                $anio   = $periodo->getDesde()->format('Y');
                return $anio;
            },
            /**
            *   Primitiva Dias de un Periodo
            *   Devuelve la cantidad de dias que tiene un periodo
            */
            '@diasPeriodo' => function($servicio, $periodo) {
                return $periodo->getHasta()->diff($periodo->getDesde())->d + 1;
            },
            /**
            *   Primitiva Dias sin Servicio
            *   Retorna la cantidad de dias del periodo que la persona no tiene servico
            *   Esto se da cuando la persona es contratada despues del dia 1ro del periodo
            *   o cuando es despedida o renuncia antes que se termina el periodo
            */
            '@diasSinServicio' => function($servicio, $periodo)  {
                $dias           = 0;
                $inicioServicio = new Datetime ( $servicio->FechaAlta );
                $finServicio    = new Datetime ( ($servicio->FechaBaja) ? $servicio->FechaBaja : '2199-01-01' );
                // Dias al comienzo del periodo (ojo... no sumar uno al resultado)
                if ($periodo->getDesde() < $inicioServicio) $dias = $inicioServicio->diff($periodo->getDesde())->d;
                // Dias al final del periodo (ojo... no sumar uno al resultado)
                if ($periodo->getHasta() > $finServicio)    $dias = $dias + $finServicio->diff($periodo->getHasta())->d;
                return $dias;
            },
            /**
            *   Primitiva Dias de Vacaciones 2012 cobradas
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasVacaciones2012' => function($servicio, $periodo) {
                $where      = " SituacionDeRevista in (22,23) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Vacaciones 2013 cobradas
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasVacaciones2013' => function($servicio, $periodo) {
                $where      = " SituacionDeRevista in (46,47) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Vacaciones 2014 cobradas
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasVacaciones2014' => function($servicio, $periodo) {
                $where      = " SituacionDeRevista in (48,49) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Vacaciones 2015 cobradas
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasVacaciones2015' => function($servicio, $periodo) {
                $where      = " SituacionDeRevista in (50,51) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            }, 
            /**
            *   Primitiva Dias de Vacaciones 2016 cobradas
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasVacaciones2016' => function($servicio, $periodo) {
                $where      = " SituacionDeRevista in (52,53) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },                        
            /**
            *   Primitiva Dias del periodo sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasVacaciones' => function($servicio, $periodo) {
                $idCodAFIPVacaciones = 13;
                $where      = " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE CodigoAFIP = $idCodAFIPVacaciones) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de inasistencia
            *   Devuelve la cantidad de dias dentro del periodo que el agente no asistio a su trabajo por cualquier razon y sin tener en cuenta si son con o sin sueldo
            */
            '@diasInasistencia' => function($servicio, $periodo) {
                $where  .= " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE Aplicacion = 1) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de inasistencia Con Sueldo
            *   Devuelve la cantidad de dias dentro del periodo que el agente no asistio a su trabajo por cualquier razon y son con sueldo
            *   Incluye tanto al 100% como al 50%
            */
            '@diasInasistenciaCS' => function($servicio, $periodo) {
                $where  .= " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE Aplicacion = 1 and TipoDeSueldo in (1,2)) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de inasistencia sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo que el agente no asistio a su trabajo por cualquier razon y son sin sueldo
            */
            '@diasInasistenciaSS' => function($servicio, $periodo) {
                $where  .= " SituacionDeRevista in (SELECT  Id FROM SituacionesDeRevistas 
                                                    WHERE   Aplicacion   = 1 
                                                    AND     TipoDeSueldo = 3 
                                                    AND     Id not in (14,15,57)) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de inasistencia sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo que el agente no asistio a su trabajo por cualquier razon y son sin sueldo
            */
            '@diasLSS' => function($servicio, $periodo) {
                $where  .= " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE Aplicacion = 1 and TipoDeSueldo = 3 and Id = 14) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },            
            /**
            *   Primitiva Semestre
            *   Devuelve 1 si es el primer semestre y 2 si es el segundo
            */
            '@semestre' => function($periodo) {
                return $periodo->getSemestre();
            },
            /**
            *   Primitiva Semestre
            *   Devuelve 1 si es el primer semestre y 2 si es el segundo
            */
            '@fechaInicioSemestre' => function($periodo) {
                return $periodo->getFechaInicioSemestre();
            },
            /**
            *   Primitiva Semestre
            *   Devuelve 1 si es el primer semestre y 2 si es el segundo
            */
            '@fechaFinSemestre' => function($periodo) {
                return $periodo->getFechaFinSemestre();
            },
        /* ----------------------------------------------------------------------------------------
            DIAS FERIADOS
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva Dias Feriados Trabajados
            *   Devuelve la cantidad de dias feriados que trabajo sin discriminar entre
            *   dias laborables y domingos
            */
            '@diasFeriadosTrabajados' =>  function($servicio, $periodo) use ($db) {
                return Rrhh_Model_DbTable_ServiciosFeriados::getFeriadosTrabajados($servicio, $periodo);
            },
            /**
            *   Primitiva Dias Feriados Laborables Trabajados
            *   Devuelve la cantidad de dias laborables feriados que trabajo el empleado
            */
            '@diasFeriadosLaborablesTrabajados' =>  function($servicio, $periodo) use ($db) {
                return Rrhh_Model_DbTable_ServiciosFeriados::getFeriadosLaborablesTrabajados($servicio, $periodo);
            },
            /**
            *   PrimitRrhh_Model_DbTable_ServiciosSituacionesDeRevistasiva Dias Feriados Domingos Trabajados
            *   Devuelve la cantidad de dias Domingos feriados que trabajo el empleado
            *   Estos dias por lo general se pagan doble
            */
            '@diasFeriadosDomingosTrabajados' =>  function($servicio, $periodo) use ($db) {
                return Rrhh_Model_DbTable_ServiciosFeriados::getFeriadosDomingosTrabajados($servicio, $periodo);
            },
            /**
            *   Primitiva Dias Feriados
            *   Devuelve la cantidad de dias feriados de un periodo
            */
            '@diasFeriados' =>  function($servicio, $periodo) use ($db) {
                return Rrhh_Model_DbTable_ServiciosFeriados::getFeriadosPeriodo($servicio, $periodo);
            },

        /* ----------------------------------------------------------------------------------------
            DIAS DE LICENCIA POR FORMA DE PAGO SIN IMPORTAR EL TIPO DE SITUACION DE REVISTA
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva Dias del periodo con sueldo al 100%
            *   Devuelve la cantidad de dias dentro del periodo qeu deben ser abonadas al 100%
            */
            '@diasRemunerados' => function($servicio, $periodo) {
                // Sueldo al 100%
                $idSueldo   = 1;
                $where      = " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE TipoDeSueldo = $idSueldo) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);            },
            /**
            *   Primitiva Dias del periodo con sueldo al 50%
            *   Devuelve la cantidad de dias dentro del periodo qeu deben ser abonadas al 50%
            */
            '@diasRemunerados50' => function($servicio, $periodo) {
                // Sueldo al 50%
                $idSueldo   = 2;
                $where      = " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE TipoDeSueldo = $idSueldo) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);            },
            /**
            *   Primitiva Dias del periodo sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu no deben ser abonados
            */
            '@diasNoRemunerados' => function($servicio, $periodo) {
                // Sin Sueldo
                $idSueldo   = 3;
                $where      = " SituacionDeRevista in (SELECT Id FROM SituacionesDeRevistas WHERE TipoDeSueldo = $idSueldo) ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
        /* ----------------------------------------------------------------------------------------
            DIAS DE LICENCIA POR FORMA DE PAGO
        ---------------------------------------------------------------------------------------- */
            /**
            *   Primitiva Dias de licencia sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia sin sueldo
            */
            '@diasLicenciasSS' => function($servicio, $periodo) {
                $where  .= " SituacionDeRevista IN (SELECT Id FROM SituacionesDeRevistas WHERE TipoDeSueldo = 3 AND Aplicacion = 1)";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de licencia con sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia sin sueldo
            */
            '@diasLicenciasCS' => function($servicio, $periodo) {
                $where  .= " SituacionDeRevista IN (SELECT Id FROM SituacionesDeRevistas WHERE TipoDeSueldo in (1,2) AND Aplicacion = 1)";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
        /* ----------------------------------------------------------------------------------------
            DIAS DE LICENCIA POR MOTIVOS (Situacion de Revista) --> Se usan casi directo en los conceptos
        ---------------------------------------------------------------------------------------- */

            /**
            *   Primitiva Dias de reserva de puesto
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen reserva de puesto (art 211 LCT)
            */
            '@diasReservaPuesto' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 15 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Suspension por causas disciplinarias
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Suspension por causas disciplinarias (art 211 LCT)
            */
            '@diasSuspensionCausasDisciplinarias' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 57 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Maternidad

            /**
            *   Primitiva Dias de licencia por maternidad
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por maternidad
            */
'@diasLicenciasMaternidad' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 6 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de licencia por excedencia
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por excedencia
            *
            *   Sin Sueldo
            *
            */
'@diasLicenciasExcedencia' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 11 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de licencia por maternidad down
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por maternidad down
            */
'@diasLicenciasMaternidadDown' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 12 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de licencia por nacimiento
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por nacimiento
            */
'@diasLicenciasNacimiento' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 26 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Adopcion
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Adopcion
            */
'@diasLicenciasAdopcion' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 37";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Enfermedad

            /**
            *   Primitiva Dias de licencia por enfermedad
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por enfermedad
            */
'@diasLicenciasEnfermedad' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 25 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Familiar Enfermo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Familiar Enfermo
            */
'@diasLicenciasEnfermedadFamiliar1erG' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 35 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Familiar Enfermo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Familiar Enfermo
            */
'@diasLicenciasEnfermedadConyuge' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 44 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Familiar Enfermo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Familiar Enfermo
            */
'@diasLicenciasEnfermedadHijo' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 45 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Accidente de trabajo

            /**
            *   Primitiva Dias de licencia por ILT primeros 10 dias
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por ILT primeros 10 dias
            */
'@diasLicenciasILTEmpleador' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 19 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de licencia por ILT dia 11 y siguientes
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por ILT dia 11 y siguientes
            */
'@diasLicenciasILTArt' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 20 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Fallecimiento

            /**
            *   Primitiva Dias de licencia por fallecimiento conyuge
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por allecimiento conyuge
            */
'@diasLicenciasFallecimientoConyuge' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 28 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Fallecimiento Familiar 1er Grado
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Fallecimiento Familiar 1er Grado
            */
'@diasLicenciasFallecimientoFam1erG' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 29 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Fallecimiento Familiar 2do Grado
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Fallecimiento Familiar 2do Grado
            */
'@diasLicenciasFallecimientoFam2doG' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 30 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Gremial

            /**
            *   Primitiva Dias de Licencia Gremial sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia Gremial sin sueldo
            *
            *   Sin Sueldo
            *
            */
'@diasLicenciasGremialSS' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 40";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia Gremial con sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia Gremial con sueldo
            */
'@diasLicenciasGremialCS' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 41";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Matrimonio

            /**
            *   Primitiva Dias de licencia por matrimonio
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por matrimonio
            */
'@diasLicenciasMatrimonio' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 27 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Matrimonio Hijo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Matrimonio Hijo
            */
'@diasLicenciasMatrimonioHijo' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 31 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Vacaciones

            /**
            *   Primitiva Dias de Licencia por Vacaciones Pagas Y Gozadas
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Vacaciones Pagas Y Gozadas
            */
            '@diasLicenciasVacacionesPagasGozadas' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 22 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Vacaciones Pagas Y No Gozadas
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Vacaciones Pagas Y No Gozadas
            */
            '@diasLicenciasVacacionesPagasNoGozadas' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 23 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Compensatorio por vacaciones pagas
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Compensatorio por vacaciones pagas
            */
            '@diasCompensatorioVacacionesPagas' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 24 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },

        // ----------- Otras

            /**
            *   Primitiva Dias de licencia sin Sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia sin sueldo
            *
            *   OJO... se refiere al motivo SinSuelo ... no a la caracteristica sin sueldo
            *
            *   No tiene un concepto asociado en forma directa
            */
            '@diasLicenciasSinSueldo' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 14 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Mudanza
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Mudanza
            */
'@diasLicenciasMudanza' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 32 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de licencia por estudios
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen licencia por estudios
            */
'@diasLicenciasEstudios' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 33 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Donar Sangre
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Donar Sangre
            */
'@diasLicenciasPorDonarSangre' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 34 ";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Estudios Médicos
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Estudios Médicos
            */
            '@diasLicenciasEstudioMedico' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 36";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia por Capacitación
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia por Capacitación
            */
            '@diasLicenciasCapacitacion' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 38";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva Dias de Licencia de los accidentes y enfermedades inculpables
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Licencia de los accidentes y enfermedades inculpables
            */
            /*
            '@diasLicenciasAccEnfInculpables' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 39";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            */
            /**
            *   Primitiva Dias de Inasistencia Injustificada sin sueldo
            *   Devuelve la cantidad de dias dentro del periodo qeu tienen Inasistencia Injustificada sin sueldo
            *
            *   Sin Sueldo
            */
            '@diasInasistenciaInjustificada' => function($servicio, $periodo) {
                $where  = " SituacionDeRevista = 42";
                return Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas::getDias($servicio, $periodo, $where);
            },
            /**
            *   Primitiva MontoVariablesDelPeriodo
            *   Devuelve el monto de los conceptos variables del periodo (pagados en ese recibo)
            */
            '@montoConceptosVariablesDelPeriodo' => function($servicio, $periodo) use ($db) {

                $sql = "    SELECT  Sum(LRD.Monto)
                            FROM    LiquidacionesRecibosDetalles LRD
                            INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                            INNER JOIN Variables V              on V.Id  = VD.Variable
                            INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                            INNER JOIN Liquidaciones L          on L.Id  = LR.Liquidacion
                            WHERE   V.TipoDeConcepto    = 9 -- Conceptos Variables
                            AND     LR.Persona          = {$servicio->Persona}
                            AND     LR.Servicio         = {$servicio->Id}
                            AND     LR.Periodo          = {$periodo->getId()}
                            AND     L.TipoDeLiquidacion = 1 -- Normal (ver bien despues)
                ";

                $montoVariable = $db->fetchOne($sql);
                if(!$montoVariable) $montoVariable = 0;
                return $montoVariable;

            },

    /* ============================================================================================
        SELECTORES
    ============================================================================================ */
            /**
            *   Selector tiene Baja en este mes
            *   Devuelve verdadero si el servicio tiene motivo de baja en este mes
            *   Ojo...
            */
            '@tieneBajaDefinitiva' => function($servicio, $periodo) use ($db) {

                $pd = $periodo->getDesde()->format('Y-m-d');
                $ph = $periodo->getHasta()->format('Y-m-d');

                $sql = "    SELECT  TipoDeBaja
                            FROM    Servicios
                            WHERE   Id = {$servicio->Id}
                            AND     TipoDeBaja is not null
                            AND     ifnull(FechaBaja,'2999-01-01') >= '$pd'
                            AND     ifnull(FechaBaja,'2999-01-01') <= '$ph'
                            AND     IFNULL(EsBajaYcontinuidad,'0') = 0
                        ";

                $tieneBaja = $db->fetchOne($sql);
                if($tieneBaja) return 1;
                return 0;
            },
            /**
            *   Selector tiene Baja por renuncia
            *   Devuelve verdadero si el servicio tiene motivo de baja por renuncia este mes
            *   Ojo...
            */
            '@tieneBajaPorRenuncia' => function($servicio, $periodo) use ($db) {

                $pd = $periodo->getDesde()->format('Y-m-d');
                $ph = $periodo->getHasta()->format('Y-m-d');

                $sql = "    SELECT  TipoDeBaja
                            FROM    Servicios
                            WHERE   Id = {$servicio->Id}
                            AND     TipoDeBaja in (3,9) /*Renuncia y abandono*/
                            AND     ifnull(FechaBaja,'2999-01-01') >= '$pd'
                            AND     ifnull(FechaBaja,'2999-01-01') <= '$ph'
                        ";

                $renuncia = $db->fetchOne($sql);
                if($renuncia) return 1;
                return 0;
            },            
            /**
            *   Selector tiene Obra Social
            *   Devuelve verdadero si tiene Obra Social
            */
            '@tieneObraSocial' => function($servicio, $periodo) {
                $M_PA   = Service_TableManager::get('Rrhh_Model_DbTable_PersonasAfiliaciones');
                $r      = $M_PA->tieneObraSocial($servicio, $periodo);
                if ($r) return 1;
                return 0;
            },
            /**
            *   Selector tiene Sindicato
            *   Devuelve verdadero si tiene Sindicato
            */
            '@tieneSindicato' => function($servicio, $periodo) {
                $M_PA   = Service_TableManager::get('Rrhh_Model_DbTable_PersonasAfiliaciones');
                $r      = $M_PA->tieneSindicato($servicio, $periodo);
                if ($r) return 1;
                return 0;
            },
            /**
            *   Selector tiene Mutual
            *   Devuelve verdadero si tiene Mutual
            */
            '@tieneMutual' => function($servicio, $periodo) {
                $M_PA   = Service_TableManager::get('Rrhh_Model_DbTable_PersonasAfiliaciones');
                $r      = $M_PA->tieneMutual($servicio, $periodo);
                if ($r) return 1;
                return 0;
            },
            /**
            *   Selector tiene Mutual
            *   Devuelve verdadero si tiene Mutual
            */
            '@tieneHijoCapacidadesDiferentes' => function($servicio, $periodo) {
                $M_PA   = Service_TableManager::get('Rrhh_Model_DbTable_PersonasAfiliaciones');
                $r      = $M_PA->tieneMutual($servicio, $periodo);
                if ($r) return 1;
                return 0;
            },
        );
    }

    public static function getImplementacion($nombre)
    {
        if (!is_callable(self::$_impl[$nombre])) {
            throw new Liquidacion_Model_Variable_Primitiva_Exception("La primitiva $nombre no tiene implementacion.");
        }
        return self::$_impl[$nombre];
    }
}