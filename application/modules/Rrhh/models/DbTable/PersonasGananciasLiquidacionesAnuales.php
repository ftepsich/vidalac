<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasLiquidacionesAnuales
 * @extends     Rrhh_Model_DbTable_PersonasGananciasLiquidaciones
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasLiquidacionesAnuales extends Rrhh_Model_DbTable_PersonasGananciasLiquidaciones
{
    protected $_name = 'PersonasGananciasLiquidaciones';

    /**
     * [generarAjusteGananciasAnual description]
     * @param  [type] $servicio   [description]
     * @param  [type] $periodoLiq [description]
     * @return [type]             [description]
     */
    /*
    public function generarAjusteGananciasAnual($servicio,$periodo,$liquidacion,$recibo) {

        $anio           = $periodo->getDesde()->format('Y') - 1;
        $idPersona      = $servicio->Persona;
        $idRecibo       = $recibo->Id;
        $idLiquidacion  = $liquidacion->Id;
    */
    public function generarAjusteGananciasAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {
        // Armo el mes 13 con el acumulado anual

        $this->delGananciaAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

        // verifoco que no sea otro el ente recaudador
        if ($this->retieneOtro($idPersona,$anio)) return false;

        // seteo el ingreso bruto
        echo '.....'.PHP_EOL.'.....'.PHP_EOL.'..... setIngresoBrutoAnual'.PHP_EOL.'.....'.PHP_EOL.'.....'.PHP_EOL;
        $this->setIngresoBrutoAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

        // seteo los descuentos del Recibo de Sueldo
        echo '.....'.PHP_EOL.'.....'.PHP_EOL.'..... setDeduccionesRecibosPropiosAnual'.PHP_EOL.'.....'.PHP_EOL.'.....'.PHP_EOL;
        $this->setDeduccionesRecibosPropiosAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

        // seteo los valores de Recibos de Terceros
        echo '.....'.PHP_EOL.'.....'.PHP_EOL.'..... setDeduccionesRecibosTercerosAnual'.PHP_EOL.'.....'.PHP_EOL.'.....'.PHP_EOL;
        $this->setDeduccionesRecibosTercerosAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

        // seteo las deducciones personales (presentadas en el 572)
        echo '.....'.PHP_EOL.'.....'.PHP_EOL.'..... setDeduccionesPersonales'.PHP_EOL.'.....'.PHP_EOL.'.....'.PHP_EOL;
        $this->setDeduccionesPersonalesAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

        // seteo los ajustes personales (presentadas en el 572)
        echo '.....'.PHP_EOL.'.....'.PHP_EOL.'..... setAjustesPersonalesAnual'.PHP_EOL.'.....'.PHP_EOL.'.....'.PHP_EOL;
        $this->setAjustesPersonalesAnual($anio,$idPersona,$idLiquidacion,$idRecibo);

        // seteo las deducciones personales (presentadas en el 572)
        echo '.....'.PHP_EOL.'.....'.PHP_EOL.'..... setPagosyDevolucionesAnuales'.PHP_EOL.'.....'.PHP_EOL.'.....'.PHP_EOL;
        $this->setPagosyDevolucionesAnuales($anio,$idPersona,$idLiquidacion,$idRecibo);
    }

    /**
     * Borra los registros de una persona para ese año
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function delGananciaAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $where = "          Persona             = $idPersona
                    AND     GananciaAnioPeriodo = $anio
                    AND     GananciaMesPeriodo  = 13
                    -- AND     Recibo           = $idRecibo
                ";

        $this->delete($where);

        /*
        $existe = $this->fetchRow($where);
        if ($existe) $this->delete($where);
        */
    }

    /**
     * Recupero el rango de ganancias que tiene a partir del mes Enero de 2015
     * Ajusta las deducciones segun los rangos salariales tomados en 2013
     * Sueldo Bruto menor de 15000, entre 15000 y 25000 y mayores a 25000
     * Tomar el mas alto devengado entre los meses Enero y Agosto del 2013
     * OJO ... Devengado es decir que se ubieran cobrado en esos meses.
     *
     * @param   int     $idPersona   identificador de la tabla persona
     * @return  int
     */
    public function getRangoDeducciones2015($idPersona) {

        $modelo         = 57; // Base_Model_DbTable_Empleados
        $caracteristica = 16; // TramosGanancias
        $empleado       = $idPersona;

        $M_CV   = new Model_DbTable_CaracteristicasValores;
        $rango  = $M_CV->getValor($empleado, $caracteristica, $modelo);

        if (!$rango) $rango = 7;

        return $rango;
    }

    public function retieneOtro($idPersona,$anio) {

        $sql = "    SELECT  PGPP.Id
                    FROM    PersonasGananciasPluriempleoPeriodos PGPP
                    INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PGPP.PersonaGananciaPluriempleo
                    WHERE   PGPP.FechaInicio                    <    '$anio-12-31'
                    AND     ifnull(PGPP.FechaFin,'2999-01-01')  >=   '$anio-01-01'
                    AND     PGP.Persona                         =    $idPersona
                    AND     ifnull(PGPP.EsEnteRecaudador,0)     =    0
        ";

        $R = $this->_db->fetchAll($sql);

        if ($R) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Devuelve el ingreso bruto anual sin diferenciar entre Habitual y no habitual
     *
     * @param   integer  $anio            Anio anterior al que estamos liquidando
     * @param   integer  $idPersona       Identificador de la persona que esta liquidando
     * @param   integer  $idRecibo        Identificador del recibo que esta liquidando
     * @param   integer  $idLiquidacion   Identificador de la liquidacion en curso
     * @return  none
     */
    public function setIngresoBrutoAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.Id   = LR.Periodo
                    WHERE   LR.Ajuste               = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5)
                    AND     LRD.Monto               <> 0
                    AND     LP.Anio                 = $anio
                    AND     LR.Persona              = $idPersona
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
                    -- Parche para que no sume el concepto beneficio de ganancia
                    AND     V.Id not in (102,108,262)
                    AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
                    AND     L.Empresa in ( SELECT  Lx.Empresa
                                            FROM    Liquidaciones Lx
                                            INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                            WHERE LRx.Id = $idRecibo)                    
        ";

        echo PHP_EOL.'-- sql -> getIngresoBrutoAnual -----------------------------------------';
        echo PHP_EOL.$sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $monto = $this->_db->fetchOne($sql);
        $monto = ($monto) ? $monto : 0;

        $d = array(     'Persona'             => $idPersona,
                        'Recibo'              => $idRecibo,
                        'Liquidacion'         => $idLiquidacion,
                        'GananciaConcepto'    => 36,
                        'Monto'               => 0,
                        'GananciaMesPeriodo'  => 13,
                        'GananciaAnioPeriodo' => $anio,
                        'MontoAcumulado'      => $monto
            );
        $this->insert($d);
    }

    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesRecibosPropiosAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $sql = "SELECT      GC.Id                      as Concepto,
                            ifnull(sum(LRD.Monto),0)   as Monto
                FROM        LiquidacionesRecibosDetalles    LRD
                INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                INNER JOIN  VariablesTiposDeConceptos       VTC ON  VTC.Id  = V.TipoDeConcepto
                INNER JOIN  GananciasConceptos              GC  ON  GC.Id   = VTC.GananciaConcepto
                INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.Id   = LR.Periodo
                WHERE   GC.GananciaConceptoTipo not in (6,7,8)
                AND     L.TipoDeLiquidacion in (1,2,3)
                AND     LR.Ajuste       = 0
                AND     LRD.Monto       <> 0
                AND     LP.Anio         =  $anio
                AND     LR.Persona     =  $idPersona
                AND     L.Empresa in ( SELECT  Lx.Empresa
                                        FROM    Liquidaciones Lx
                                        INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                        WHERE LRx.Id = $idRecibo)                
                GROUP BY GC.Id
        ";

        echo PHP_EOL.'-- sql -> setDeduccionesRecibosPropiosAnual ----------------------------';
        echo PHP_EOL.$sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $R = $this->_db->fetchAll($sql);

        // Si tiene deducciones
        if ($R) {
            // Las recorro y sumo
            foreach ($R as $row) {
                // Escribo el registro en Ganancias
                $d = array(     'Persona'             => $idPersona,
                                'Recibo'              => $idRecibo,
                                'Liquidacion'         => $idLiquidacion,
                                'GananciaConcepto'    => $row['Concepto'],
                                'Monto'               => 0,
                                'GananciaMesPeriodo'  => 13,
                                'GananciaAnioPeriodo' => $anio,
                                'MontoAcumulado'      => $row['Monto']
                    );
                $this->insert($d);
            }
        }
    }


    public function getSacNetoPluriempleo($anio, $idPersona) {

            // Esta funcion es parte del parche para el sac del segundo semestre del 2014

            $sql = "SELECT  ifnull(sum(RemuneracionBrutaTotal),0)   as RemuneracionBrutaTotal,
                            ifnull(sum(AporteJubilacion),0)         as AporteJubilacion,
                            ifnull(sum(AporteObraSocial),0)         as AporteObraSocial,
                            ifnull(sum(AporteSindical),0)           as AporteSindical,
                            ifnull(sum(ImporteRetribucionesNoHabituales),0)     as ImporteRetribucionesNoHabituales
                    FROM    PersonasGananciasPluriempleoDetalle PPD
                    INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                    WHERE   PPD.FechaDeLiquidacion <=   '$anio-12-31'
                    AND     PPD.FechaDeLiquidacion >=   '$anio-12-01'
                    AND     PGP.Persona            =    $idPersona
                    ";

            $r = $this->_db->fetchRow($sql);

            $netoSAC = 0;
            if ($r) {
                $Remunerativo = $r['RemuneracionBrutaTotal'] + $r['ImporteRetribucionesNoHabituales'];
                $Descuentos   = $r['AporteJubilacion'] - $r['AporteObraSocial'] - $r['AporteSindical'];
                $SAC          = $r['ImporteRetribucionesNoHabituales'];
                $netoSAC      = $SAC + ($Descuentos * $SAC / $Remunerativo);
            }

            return $netoSAC;
    }


    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo de un Tercero
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesRecibosTercerosAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $periodoGananciaFD  = $datos['anio']."-01-01";

        // Horrible... armo un array con los id ... muy feo !!!
        $conceptos = array ( 'RemuneracionBrutaTotal'           => 36,
                            'AporteJubilacion'                  => 30,
                            'AporteObraSocial'                  => 28,
                            'AporteSindical'                    => 29,
                            'ImporteRetribucionesNoHabituales'  => 37,
                            'RetencionGanancias'                => 34,
                            'DevolucionGanancia'                => 35,
                            'Ajustes'                           => 38
                    );

        // Esto esta harcodeado en AFIP y por arrastre lo tengo que harcodear
        $sql = "SELECT  ifnull(sum(RemuneracionBrutaTotal),0)   as RemuneracionBrutaTotal,
                        ifnull(sum(AporteJubilacion),0)         as AporteJubilacion,
                        ifnull(sum(AporteObraSocial),0)         as AporteObraSocial,
                        ifnull(sum(AporteSindical),0)           as AporteSindical,
                        ifnull(sum(ImporteRetribucionesNoHabituales),0)     as ImporteRetribucionesNoHabituales,
                        ifnull(sum(RetencionGanancias),0)       as RetencionGanancias,
                        ifnull(sum(DevolucionGanancia),0)       as DevolucionGanancia,
                        ifnull(sum(Ajustes),0)                  as Ajustes
                FROM    PersonasGananciasPluriempleoDetalle PPD
                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                WHERE   PPD.FechaDeLiquidacion <=   '$anio-12-31'
                AND     PPD.FechaDeLiquidacion >=   '$anio-01-01'
                AND     PGP.Persona            =    $idPersona
                ";

        $row = $this->_db->fetchRow($sql);

        echo PHP_EOL.'--SQL-------------------------------------------------------------------';
        echo PHP_EOL.$sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        // Si tiene deducciones
        if ($row) {

            //$row  = $R->current();

            //foreach ($R as $key => $value){

            $d = array(     'Persona'             => $idPersona,
                            'Recibo'              => $idRecibo,
                            'Liquidacion'         => $idLiquidacion,
                            'Monto'               => 0,
                            'GananciaMesPeriodo'  => 13,
                            'GananciaAnioPeriodo' => $anio,
                            'MontoAcumulado'      => $conceoptos[$key]
                );

            $d['GananciaConcepto']  = $conceptos['RemuneracionBrutaTotal'];
            $d['MontoAcumulado']    = $row['RemuneracionBrutaTotal'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['AporteJubilacion'];
            $d['MontoAcumulado']    = $row['AporteJubilacion'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['AporteObraSocial'];
            $d['MontoAcumulado']    = $row['AporteObraSocial'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['AporteSindical'];
            $d['MontoAcumulado']    = $row['AporteSindical'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['ImporteRetribucionesNoHabituales'];
            $d['MontoAcumulado']    = $row['ImporteRetribucionesNoHabituales'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['RetencionGanancias'];
            $d['MontoAcumulado']    = $row['RetencionGanancias'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['DevolucionGanancia'];
            $d['MontoAcumulado']    = $row['DevolucionGanancia'];
            $this->insert($d);

            $d['GananciaConcepto']  = $conceptos['Ajustes'];
            $d['MontoAcumulado']    = $row['Ajustes'];
            $this->insert($d);
            //}
        }
    }


   /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesPersonalesAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $deducciones = array();

        // Si el rango es 2 aumento un 20% las deducciones. (Ojo los rango 1 aunque no paguen son beneficiados con el 20% tambien)
        // Solo los casos 1 y 2 que provienen de las tablas de AFIP
        // No lo hago para los montos fijos pagados como ser seguros

        /*
        $rangoGanancia      = $this->getRangoDeducciones($idPersona);
        $incrementoAplicar  = ($rangoGanancia == 2 || $rangoGanancia == 1) ? 1.2 : 1;
        */

        $rango2015 = $this->getRangoDeducciones2015($idPersona);
        switch ($rango2015) {
            case 1: $incremento2015 = 1.5;  break;
            case 2: $incremento2015 = 1.44; break;
            case 3: $incremento2015 = 1.38; break;
            case 4: $incremento2015 = 1.32; break;
            case 5: $incremento2015 = 1.29; break;
            case 6: $incremento2015 = 1.26; break;
            case 7: $incremento2015 = 1.2;  break; // Rango 1
            case 8: $incremento2015 = 1;    break; // Rango 3
        }
        $incrementoAplicar = $incremento2015;
        // Parche 2016
        $incrementoAplicar     = 1;
        $incremento2015        = 1;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo $incrementoAplicar.PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;


        // Debo recorre mes a mes cuanto es el monto que le corresponde
        for ($mes = 1; $mes <= 12; $mes++) {

        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo $mes.PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
        echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;


            // Debo completar el array ya que existen deducciones que aparecen un solo mes y despues no las sume mas tarde
            if ($mes > 1) $deducciones[$mes] = $deducciones[$mes-1];

            $periodoFD = $anio."-".str_pad($mes, 2,"0",STR_PAD_LEFT)."-01";

            $R  = array();

            // Conceptos tabulados por Afip
            $sql = "    SELECT      GC.Id               as Concepto,
                                    (0 - AGDD.Monto)    as Monto,
                                    (0 - PGD.Monto)     as MontoParticular,
                                    AGD.TipoDeduccion   as Tipo,
                                    PGD.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesInicioImputacion
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesDetalles    AGDD    ON AGD.Id   =   AGDD.Deduccion
                        INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP    ON AGDP.Id  =   AGDD.Periodo
                        WHERE       AGDP.FechaDesde                                 <=  '$periodoFD'
                        AND         ifnull(AGDP.FechaHasta,'2199-01-01')            >   '$periodoFD'
                        AND         PGD.Persona                                     =   $idPersona
                        AND         PGD.AnioGanancia                                =   $anio
                        AND         PGD.MesDesde                                    <=  $mes
                        AND         PGD.MesHasta                                    >=  $mes
                        AND         PGD.GananciaDeduccion not in (18)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            //echo print_r($R, true).PHP_EOL;

            // Conceptos No tabulados por afip
            $sql = "    SELECT      GC.Id               as Concepto,
                                    (0)                 as Monto,
                                    (0 - PGD.Monto)     as MontoParticular,
                                    AGD.TipoDeduccion   as Tipo,
                                    PGD.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesInicioImputacion
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        WHERE       PGD.Persona                                     =   $idPersona
                        AND         PGD.AnioGanancia                                =   $anio
                        AND         PGD.MesDesde                                    <=  $mes
                        AND         PGD.MesHasta                                    >=  $mes
                        AND         AGD.Id not in (Select AGDD1.Deduccion From AfipGananciasDeduccionesDetalles    AGDD1)
                        AND         PGD.GananciaDeduccion not in (18)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);

            //echo PHP_EOL.PHP_EOL.print_r($R, true).PHP_EOL.PHP_EOL;

            // Conceptos que se aplican a todos ( ej: deducciones especiales )
            $sql = "    SELECT      GC1.Id               as Concepto,
                                    (0 - AGDD1.Monto)    as Monto,
                                    0                    as MontoParticular,
                                    AGD1.TipoDeduccion   as Tipo,
                                    AGD1.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesPresentacion
                        FROM        GananciasConceptos                  GC1
                        INNER JOIN  AfipGananciasDeducciones            AGD1     ON AGD1.Id   =   GC1.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesDetalles    AGDD1    ON AGD1.Id   =   AGDD1.Deduccion
                        INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP1    ON AGDP1.Id  =   AGDD1.Periodo
                        WHERE       AGDP1.FechaDesde                         <=  '$periodoFD'
                        AND         ifnull(AGDP1.FechaHasta,'2199-01-01')    >   '$periodoFD'
                        AND         AGD1.Id in (1,7)
                        AND         GC1.Id not in (18)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);

            echo print_r($R, true).PHP_EOL;

            // $R = $this->_db->fetchAll($sql);
            if ($R) {

                foreach ($R as $row){

                    switch ($row['Tipo']) {
                        case 1:
                            // Monto Fijo Anual
                            $MontoDeduccion                 = ($row['Monto'] / 12) * $incrementoAplicar;
                            $MontoDeduccionSinBeneficios    = ($row['Monto'] / 12);
                            break;
                        case 2:
                            // Monto Fijo por suceso
                            $MontoDeduccion                 = ($row['Monto'] / 12) * $incrementoAplicar;
                            $MontoDeduccionSinBeneficios    = ($row['Monto'] / 12);
                            break;
                        case 3:
                            // Monto Tope Anual;
                            // Por ahora no hago nada de control
                            $MontoDeduccion                 = $row['MontoParticular'];
                            $MontoDeduccionSinBeneficios    = $MontoDeduccion;
                            break;
                        case 4:
                            // Monto sin restriccion
                            $MontoDeduccion                 = $row['MontoParticular'];
                            $MontoDeduccionSinBeneficios    = $MontoDeduccion;
                            break;
                        default:
                            break;
                    }

                    // if ($mes == 12) $ValorMes[$row['Concepto']] = $MontoDeduccion;

                    // Acumulo
                    //$deducciones[$mes][$row['Concepto']]    = $deducciones[$mes-1][$row['Concepto']] + $MontoDeduccion;
                    // Ahora sumo con el mismo mes por que mas arriba complete el mes con los valores del mes anterior
                    $deducciones[$mes][$row['Concepto']]    = $deducciones[$mes][$row['Concepto']] + $MontoDeduccion;

                    $MontoDeduccion = 0;
                }
            }
        }

        // grabo

        if ($deducciones) {

            echo PHP_EOL.'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@'.PHP_EOL;
            echo 'deducciones'.PHP_EOL;
            echo PHP_EOL.'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@'.PHP_EOL;
            echo print_r($deducciones, true).PHP_EOL;
            echo PHP_EOL.'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@'.PHP_EOL;

            //$m1 = $datos['mes'];

            //$arr = $deducciones[$m1];

            //cho 'arr'.PHP_EOL;
            //echo print_r($arr, true).PHP_EOL;

            foreach ($deducciones as $Dmes => $arrValores){

                if ($Dmes == 12) {
                    foreach ($arrValores as $Dconcepto => $Dmonto) {

                        //$M  = $ValorMes[$Dconcepto];
                        $MA = $Dmonto;

                        /* Parche decreto 2354/2014 no pago ganancia para quienes cobraron por debajo de los 35.000 */

                        /*
                        if ($Dconcepto == 40) {
                            if ( !$this->getCobroMasDe35kEntreJulioDiciembre2014($idPersona) && ( $rangoGanancia == 2 ||  $rangoGanancia == 3)) {

                                //
                                $sql = "SELECT  LR.Id as idRecibo
                                        FROM    LiquidacionesRecibos LR
                                        INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                                        INNER JOIN Liquidaciones L          on L.Id = LR.Liquidacion
                                        WHERE   LP.Anio = 2014
                                        AND     LP.Valor = 12
                                        AND     LR.Persona = $idPersona
                                        AND     L.TipoDeLiquidacion = 1
                                        ";

                                $idReciboSac2sem2014 = $this->_db->fetchOne($sql);
                                if ($idReciboSac2sem2014 ) {

                                    $sac2sem2014            = $this->getSac2sem2014($idReciboSac2sem2014);
                                    $descuentosQueNoSuman   = $this->getDescuentosQueNoSuman($idPersona,$anio);
                                    $descuentos             = $this->getSumDescuentos($idPersona,$anio);
                                    $remunerativos          = $this->getSumRemunerativos($idPersona,$anio);
                                    $NetoSac2sem2014        = $sac2sem2014 + ( $sac2sem2014 / $remunerativos * ($descuentos - $descuentosQueNoSuman));

                                    echo "-- PPPPPPP --------------------------------------------------------------".PHP_EOL;
                                    echo 'sac2sem2014  '.$sac2sem2014.PHP_EOL;
                                    echo 'descuentosQueNoSuman  '.$descuentosQueNoSuman.PHP_EOL;
                                    echo 'descuentos  '.$descuentos.PHP_EOL;
                                    echo 'remunerativos  '.$remunerativos.PHP_EOL;
                                    echo 'NetoSac2sem2014  '.$NetoSac2sem2014.PHP_EOL;
                                    echo '------------------------------------------------------------------------'.PHP_EOL;

                                }

                                // Veo si tiene pluriempleo para netear la parte del pluriempleo que incrementa la deduccion especial
                                $NetoSacPluriempleo = $this->getSacNetoPluriempleo($anio, $idPersona);

                                // Ojo se resta porque son valores negativos
                                //$M  = $ValorMes[$Dconcepto] - $NetoSac2sem2014;
                                $MA = $Dmonto - $NetoSac2sem2014 - $NetoSacPluriempleo;
                            }
                        }
                        */

                        $d = array( 'Persona'             => $idPersona,
                                    'Recibo'              => $idRecibo,
                                    'Liquidacion'         => $idLiquidacion,
                                    'GananciaConcepto'    => $Dconcepto,
                                    'Monto'               => 0,
                                    'GananciaMesPeriodo'  => 13,
                                    'GananciaAnioPeriodo' => $anio,
                                    'MontoAcumulado'      => $MA
                        );
                        $this->insert($d);
                    }
                }
            }
        }
    }

   /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setAjustesPersonalesAnual($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $deducciones = array();

        // Debo recorre mes a mes cuanto es el monto que le corresponde
        for ($mes = 1; $mes <= 12; $mes++) {

            echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
            echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
            echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
            echo $mes.PHP_EOL;
            echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
            echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;
            echo PHP_EOL."^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^".PHP_EOL;


            // Debo completar el array ya que existen deducciones que aparecen un solo mes y despues no las sume mas tarde
            if ($mes > 1) $deducciones[$mes] = $deducciones[$mes-1];

            $periodoFD = $anio."-".str_pad($mes, 2,"0",STR_PAD_LEFT)."-01";

            $R  = array();

            // Conceptos No tabulados por afip
            $sql = "    SELECT      GC.Id               as Concepto,
                                    (0)                 as Monto,
                                    (0 - PGD.Monto)     as MontoParticular,
                                    AGD.TipoDeduccion   as Tipo,
                                    PGD.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesInicioImputacion
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        WHERE       PGD.Persona                                     =   $idPersona
                        AND         PGD.AnioGanancia                                =   $anio
                        AND         PGD.MesDesde                                    <=  $mes
                        AND         PGD.MesHasta                                    >=  $mes
                        AND         PGD.GananciaDeduccion in (18)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);


            // $R = $this->_db->fetchAll($sql);
            if ($R) {

                foreach ($R as $row){

                    if ($row['Monto'] <> 0) {
                        $MontoDeduccion = $row['Monto'];
                    } else {
                        $MontoDeduccion = $row['MontoParticular'];
                    }

                    /*
                    switch ($row['Tipo']) {
                        case 1:
                            // Monto Fijo Anual
                            $MontoDeduccion                 = ($row['Monto'] / 12) * $incrementoAplicar;
                            $MontoDeduccionSinBeneficios    = ($row['Monto'] / 12);
                            break;
                        case 2:
                            // Monto Fijo por suceso
                            $MontoDeduccion                 = ($row['Monto'] / 12) * $incrementoAplicar;
                            $MontoDeduccionSinBeneficios    = ($row['Monto'] / 12);
                            break;
                        case 3:
                            // Monto Tope Anual;
                            // Por ahora no hago nada de control
                            $MontoDeduccion                 = $row['MontoParticular'];
                            $MontoDeduccionSinBeneficios    = $MontoDeduccion;
                            break;
                        case 4:
                            // Monto sin restriccion
                            $MontoDeduccion                 = $row['MontoParticular'];
                            $MontoDeduccionSinBeneficios    = $MontoDeduccion;
                            break;
                        default:
                            break;
                    }
                    */
                    // if ($mes == 12) $ValorMes[$row['Concepto']] = $MontoDeduccion;

                    // Acumulo
                    //$deducciones[$mes][$row['Concepto']]    = $deducciones[$mes-1][$row['Concepto']] + $MontoDeduccion;
                    // Ahora sumo con el mismo mes por que mas arriba complete el mes con los valores del mes anterior
                    $deducciones[$mes][$row['Concepto']]    = $deducciones[$mes][$row['Concepto']] + $MontoDeduccion;

                    $MontoDeduccion = 0;
                }
            }
        }

        // grabo

        if ($deducciones) {

            echo PHP_EOL.'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@'.PHP_EOL;
            echo 'deducciones'.PHP_EOL;
            echo PHP_EOL.'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@'.PHP_EOL;
            echo print_r($deducciones, true).PHP_EOL;
            echo PHP_EOL.'@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@'.PHP_EOL;

            //$m1 = $datos['mes'];

            //$arr = $deducciones[$m1];

            //cho 'arr'.PHP_EOL;
            //echo print_r($arr, true).PHP_EOL;

            foreach ($deducciones as $Dmes => $arrValores){

                if ($Dmes == 12) {
                    foreach ($arrValores as $Dconcepto => $Dmonto) {

                        //$M  = $ValorMes[$Dconcepto];
                        $MA = $Dmonto;

                        $d = array( 'Persona'             => $idPersona,
                                    'Recibo'              => $idRecibo,
                                    'Liquidacion'         => $idLiquidacion,
                                    'GananciaConcepto'    => $Dconcepto,
                                    'Monto'               => 0,
                                    'GananciaMesPeriodo'  => 13,
                                    'GananciaAnioPeriodo' => $anio,
                                    'MontoAcumulado'      => $MA
                        );
                        $this->insert($d);
                    }
                }
            }
        }
    }


    /**
     * Devuelve los descuentos de un recibo
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getDescuentosQueNoSuman($idPersona,$anio,$idRecibo) {

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.Id   = LR.Periodo
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (4) -- Descuentos
                    AND     LRD.Monto   <> 0
                    AND     LR.Persona  = $idPersona
                    AND     LP.Anio     = $anio
                    AND     LP.Valor    = 12
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
                    AND     V.Id in (227, 105, 135, 262,263, 95, 97, 98, 267,268,88,139 ) -- 227: Amutcaer, 105 Descuentos de anticipos, 262 y 364 imp ganancia sujeta a benesficio y beneficio 12/2014
                    -- Para que no tome en cuenta las ganancias que en el calculo mensual no estan pero ahora si
                    -- 88 y 139 la OS de los adherentes extra
                    AND     L.Empresa in ( SELECT  Lx.Empresa
                                            FROM    Liquidaciones Lx
                                            INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                            WHERE LRx.Id = $idRecibo)                    
        ";

        echo '-- getAmutcaer -------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve los descuentos de un recibo
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getSumDescuentos($idPersona,$anio,$idRecibo) {

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.Id   = LR.Periodo
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (4) -- Descuentos
                    AND     LRD.Monto   <> 0
                    AND     LR.Persona  = $idPersona
                    AND     LP.Anio     = $anio
                    AND     LP.Valor    = 12
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
                    AND     L.Empresa in ( SELECT  Lx.Empresa
                                            FROM    Liquidaciones Lx
                                            INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                            WHERE LRx.Id = $idRecibo)
        ";

        echo '-- getSumDescuetos -------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve los remunerativos de un recibo
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getSumRemunerativos($idPersona,$anio,$idRecibo) {

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.Id   = LR.Periodo
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2)
                    AND     LRD.Monto   <> 0
                    AND     LR.Persona  = $idPersona
                    AND     LP.Anio     = $anio
                    AND     LP.Valor    = 12
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
                    AND     L.Empresa in ( SELECT  Lx.Empresa
                                            FROM    Liquidaciones Lx
                                            INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                            WHERE LRx.Id = $idRecibo)                    
        ";

        echo '-- getSumRemunerativos -------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve la suma de los Pagos de Ganancia del año indicado
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getPagos($idPersona,$anio,$idRecibo) {

        // *******************************************************************************************
        // Pagos Anteriores
        // *******************************************************************************************
        $sql = "SELECT      sum(LRD2.Monto) as MontoAcumulado
                FROM        LiquidacionesRecibosDetalles    LRD2
                INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                INNER JOIN  Liquidaciones                   L2   ON  L2.Id    = LR2.Liquidacion
                INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                WHERE   GC2.GananciaConceptoTipo in (6)
                AND     LR2.Ajuste       =  0
                AND     LRD2.Monto       <> 0
                AND     LP2.Anio         =  $anio
                AND     LP2.Valor        <  13
                AND     LR2.Persona      =  $idPersona
                AND     L2.Empresa in ( SELECT  Lx.Empresa
                                        FROM    Liquidaciones Lx
                                        INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                        WHERE LRx.Id = $idRecibo)                
                GROUP BY GC2.Id
                ";
        /*
        echo "--Pagos Anteriores -----------------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;
        */

        $pagos = $this->_db->fetchOne($sql);
        if (!$pagos) $pagos = 0;

        return $pagos;
    }

    /**
     * Devuelve la suma de las Devoluciones de Ganancia del año indicado
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getDevoluciones($idPersona,$anio,$idRecibo) {

        // *******************************************************************************************
        // Devoluciones
        // *******************************************************************************************
        $sql = "SELECT      sum(LRD2.Monto) as MontoAcumulado
                FROM        LiquidacionesRecibosDetalles    LRD2
                INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                INNER JOIN  Liquidaciones                   L2   ON  L2.Id    = LR2.Liquidacion
                INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                WHERE   GC2.GananciaConceptoTipo in (7)
                AND     LR2.Ajuste       = 0
                AND     LRD2.Monto       <> 0
                AND     LP2.Anio         =  $anio
                AND     LP2.Valor        <  13
                AND     LR2.Persona      =  $idPersona
                AND     L2.Empresa in ( SELECT  Lx.Empresa
                                        FROM    Liquidaciones Lx
                                        INNER JOIN LiquidacionesRecibos LRx ON LRx.Liquidacion = Lx.Id
                                        WHERE LRx.Id = $idRecibo)                
                GROUP BY GC2.Id
                ";
        /*
        echo "--Devoluciones Anteriores ----------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;
        */

        $devoluciones = $this->_db->fetchOne($sql);
        if (!$devoluciones) $devoluciones = 0;

        return $devoluciones;
    }

    /**
     * Devuelve el monto imponible para el calculo de ganancias
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getMontoImponible($idPersona,$anio) {

        // *******************************************************************************************
        // Monto imponible (ojo 41 es el beneficio en las deducciones)
        // OJO !!!! el beneficio YA esta incluida en las deduccions por lo tanto no debe ser
        // sumado NUNCA con las deducciones por lo tanto SIEMPRE el 41 Debe esta en el not del where
        // *******************************************************************************************
        $sql = "SELECT  sum(MontoAcumulado)
                FROM    PersonasGananciasLiquidaciones PGL
                WHERE   PGL.Persona             = $idPersona
                AND     PGL.GananciaMesPeriodo  = 13
                AND     PGL.GananciaAnioPeriodo = $anio
                AND     PGL.GananciaConcepto not in (34,35,41,42,43)
                AND     PGL.GananciaConcepto not in (18)
                ";

        /*
        echo "--Monto imponible ------------------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;
        */

        $MontoImponible = $this->_db->fetchOne($sql);
        if (!$MontoImponible) $MontoImponible = 0;

        return $MontoImponible;
    }

    /**
     * Devuelve el monto imponible para el calculo de ganancias
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getAjustes($idPersona,$anio) {

        // *******************************************************************************************
        // Monto imponible (ojo 41 es el beneficio en las deducciones)
        // OJO !!!! el beneficio YA esta incluida en las deduccions por lo tanto no debe ser
        // sumado NUNCA con las deducciones por lo tanto SIEMPRE el 41 Debe esta en el not del where
        // *******************************************************************************************
        $sql = "SELECT  sum(MontoAcumulado)
                FROM    PersonasGananciasLiquidaciones PGL
                WHERE   PGL.Persona             = $idPersona
                AND     PGL.GananciaMesPeriodo  = 13
                AND     PGL.GananciaAnioPeriodo = $anio
                AND     PGL.GananciaConcepto in (18)
                ";


        echo "--Monto imponible ------------------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Ajuste = $this->_db->fetchOne($sql);
        if (!$Ajuste) $Ajuste = 0;

        return abs($Ajuste);
    }



    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * y agrega el concepto al recibo de sueldo
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setPagosyDevolucionesAnuales($anio,$idPersona,$idLiquidacion,$idRecibo) {

        $rangoGanancia  = $this->getRangoDeducciones($idPersona);

        // *******************************************************************************************
        // Devoluciones
        // *******************************************************************************************
        $devoluciones   = $this->getDevoluciones($idPersona,$anio,$idRecibo);

        // *******************************************************************************************
        // Pagos
        // *******************************************************************************************
        $pagos          = $this->getPagos($idPersona,$anio,$idRecibo);

        // *******************************************************************************************
        // Monto imponible (ojo 41 es el beneficio en las deducciones)
        // OJO !!!! el beneficio YA esta incluida en las deduccions por lo tanto no debe ser
        // sumado NUNCA con las deducciones por lo tanto SIEMPRE el 41 Debe esta en el not del where
        // *******************************************************************************************
        $MontoImponible = $this->getMontoImponible($idPersona,$anio);

        // *******************************************************************************************
        // Ajustes
        // *******************************************************************************************
        $ajustes        = $this->getAjustes($idPersona,$anio);

        // *******************************************************************************************
        // Datos Tabla rangos Afip (Con Beneficios)
        // *******************************************************************************************

            $sql = "SELECT  *
                    FROM    AfipGananciasEscalas
                    WHERE   Desde < $MontoImponible
                    AND     Hasta >= $MontoImponible";

            echo "--Datos Tabla rangos Afip (Con Beneficios) -----------------------------".PHP_EOL;
            echo $sql.PHP_EOL;
            echo '------------------------------------------------------------------------'.PHP_EOL;

            $tablaAfip = $this->_db->fetchRow($sql);

            //if (!$tablaAfip) throw new Rad_Db_Table_Exception("Falta ingresar la tabla de datos de ganancia para el periodo seleccionado.");

            $limiteInferior     = $tablaAfip['Desde'];
            $Alicuota           = $tablaAfip['Alicuota'];
            $CanonFijo          = $tablaAfip['CanonFijo'];

            $montoIncremento    = (($MontoImponible - $limiteInferior) * ($Alicuota / 100))  + ($CanonFijo);
            $montoAPAgar        = $montoIncremento + $pagos + $devoluciones;

        // *******************************************************************************************
        // inserto cuanto deberia pagar
        // *******************************************************************************************

            $d = array(     'Persona'             => $idPersona,
                            'Recibo'              => $idRecibo,
                            'Liquidacion'         => $idLiquidacion,
                            'GananciaMesPeriodo'  => 13,
                            'GananciaAnioPeriodo' => $anio
            );

            // inserto lo que deberia pagar este periodo
            $d['GananciaConcepto']  = 43;
            $d['Monto']             = round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP);

            // pagosAnteriores          viene en negativo
            // devolucionesAnteriores   viene en positivo
            // montoAPAgar              esta en positivo
            // asi que debe ser -$montoAPAgar + $devolucionesAnteriores + $pagosAnteriores para sacar el acumulado

            $d['MontoAcumulado']    = -$montoAPAgar + $devoluciones + $pagos + $ajustes;
            $this->insert($d);

        // *******************************************************************************************
        // Grabo el registro en la tabla LiquidacionesRecibosDetalles
        // *******************************************************************************************

        echo "--Mi $mes-------------------------------------------------------------".PHP_EOL;
        echo 'montoAPagarSB:                '.$montoAPagarSB.PHP_EOL;
        echo 'montoAPAgar:                  '.$montoAPAgar.PHP_EOL;
        echo 'beneficioM:                   '.$beneficioM.PHP_EOL;
        echo 'beneficioM:                   '.$beneficioM.PHP_EOL;
        echo 'beneficioAcumulado            '.$beneficioAcumulado.PHP_EOL;
        echo 'MontoImponibleSinBeneficios:  '.$MontoImponibleSinBeneficios.PHP_EOL;
        echo 'MontoImponible:               '.$MontoImponible.PHP_EOL;
        echo 'MontoIncremento:              '.$montoIncremento.PHP_EOL;
        echo 'mes:                          '.$datos['mes'].PHP_EOL;
        echo 'limiteInferior:               '.$limiteInferior .PHP_EOL;
        echo 'Alicuota:                     '.$Alicuota .PHP_EOL;
        echo 'CanonFijo:                    '.$CanonFijo .PHP_EOL;
        echo "CanonFijo / 12 * mes :        ".$CanonFijo / 12 * $datos['mes'] .PHP_EOL;
        echo "devolucionesAnteriores:       ".$devoluciones .PHP_EOL;
        echo "pagosAnteriores:              ".$pagos .PHP_EOL;
        echo "montoAPAgar:                  ".$montoAPAgar .PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        // que no le descuente nada a los de rango 1
        // $montoAPAgar = ($datos['rangoGanancias'] <> 1) ? $montoAPAgar : 0;

        if ($montoAPAgar) {


            // 28/07/2016 -- Arreglo para que el proceso pueda usarse desde un concepto en el 
            // liquidacion del marzo del año siguiente o desde el postproceso en el caso de los que bajan

            //if ()

                // 348  Retencion   
                // 349  Devolucion

            $M_LRD       = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles);
            $M_Concepto  = Service_TableManager::get(Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles);

            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            // Ojo la ganancia viene con el signo cambiado
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            if ($montoAPAgar > 0) {
                // Se le debe retener el impuesto
                $VarDetalle         = 500;
                $conceptoGanancia   = 34;
            } else {
                // debemos devolverle plata
                $VarDetalle         = 484;
                $conceptoGanancia   = 35;
            }

            /* Insert para cuando es una baja */

            $sql = "SELECT Periodo FROM LiquidacionesRecibos where Id = $idRecibo"; 
            $idPeriodo = $this->_db->fetchOne($sql);

            $c = array(     'LiquidacionRecibo'   => $idRecibo,
                            'VariableDetalle'     => $VarDetalle,
                            'Monto'               => round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP),
                            'MontoCalculado'      => round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP),
                            'PeriodoDevengado'    => $idPeriodo,
                            'Detalle'             => '',
                            'ConceptoCodigo'      => $M_Concepto->getCodigo($VarDetalle),
                            'ConceptoNombre'      => $M_Concepto->getNombre($VarDetalle)
                );
            $M_LRD->delete("LiquidacionRecibo = $idRecibo and VariableDetalle = $VarDetalle");
            $M_LRD->insert($c);

            // OJO ... si es de los exceptuados y tiene devoluciones no hay que ponerlas
            /*
            if ($rangoGanancias <> 1) {

                $c = array(     'LiquidacionRecibo'   => $idRecibo,
                                'VariableDetalle'     => $VarDetalle,
                                'Monto'               => round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP),
                                'MontoCalculado'      => round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP),
                                'PeriodoDevengado'    => $datos['idPeriodo'],
                                'Detalle'             => '',
                                'ConceptoCodigo'      => $M_Concepto->getCodigo($VarDetalle),
                                'ConceptoNombre'      => $M_Concepto->getNombre($VarDetalle)
                );
                $M_LRD->delete("LiquidacionRecibo = {$datos['idRecibo']} and VariableDetalle = $VarDetalle");
                $M_LRD->insert($c);
            }
            */
            // inserto el monto que realmente pago ... mas arriba se inserto el que DEBERIA pagar
            $d['GananciaConcepto']  = $conceptoGanancia;
            $d['Monto']             = round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP);
            $d['MontoAcumulado']    = round(-$montoAPAgar, 2, PHP_ROUND_HALF_UP);
            $this->insert($d);

        }
    }
}