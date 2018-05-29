<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasLiquidaciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasLiquidaciones extends Rad_Db_Table
{
    protected $_name = 'PersonasGananciasLiquidaciones';

    protected $_gridGroupField  = 'GananciasConceptosGananciasConceptosTiposDescripcion';

    protected $_sort            = array('GananciasConceptosGananciasConceptosTiposDescripcion asc','GananciaConcepto asc');

    protected $_gridGroupFieldOrderDirection = 'asc';

    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'GananciasConceptos' => array(
            'columns'           => 'GananciaConcepto',
            'refTableClass'     => 'Liquidacion_Model_DbTable_GananciasConceptos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'GananciasConceptos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Liquidaciones' => array(
            'columns'           => 'Liquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Liquidaciones',
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Liquidaciones',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array();

    public function init()
    {
        parent::init();
        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('GananciasConceptos')
              ->joinRef('GananciasConceptosTipos', array('Descripcion'));
        }
    }

    /**
     * Retorna el monto a pagar de ganancias para un periodo
     * en el caso que no este calculado llama al calculador para que arme el acumulado
     * @param  int      $servicio   identificador de servicio
     * @param  Rad_db   $periodo    objeto LiquidacionPeriodo
     * @return decimal              monto a retener o devolver
     */
    public function getMontoGanancias($servicio,$periodo) {
        return 0;
    }

    /**
     * [generarGananciasPeriodo description]
     * @param  [type] $servicio   [description]
     * @param  [type] $periodoLiq [description]
     * @return [type]             [description]
     */
    public function generarCuadroGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo) {

        $datos = array( 'idPeriodo'             => $periodo->getId(),
                        'idServicio'            => $servicio->Id,
                        'idRecibo'              => $recibo->Id,
                        'idEmpresa'				=> $servicio->Empresa,
                        'idPersona'             => $servicio->Persona,
                        'idLiquidacion'         => $liquidacion->Id,
                        'mes'                   => $periodo->getDesde()->format('m'),
                        'anio'                  => $periodo->getDesde()->format('Y'),
                        'periodoFD'             => $periodo->getDesde()->format('Y-m-d'),
                        'periodoFH'             => $periodo->getHasta()->format('Y-m-d'),
                        'rangoGanancias'        => $this->getRangoDeducciones($servicio->Persona),
                        'rangoGanancias2015'    => $this->getRangoDeducciones2015($servicio->Persona),
                        'tipoDeLiquidacion'     => $liquidacion->TipoDeLiquidacion
                );

        // echo print_r($datos, true);

        // Si existe ganancias para este recibo los borra y calcula nuevamente
        $this->delGananciaLiquidada($datos);

        // Salgo si la liq no suma para ganacias
        if($this->liquidacionSinGanancias($liquidacion->TipoDeLiquidacion)) return false;
        //if ($liquidacion->TipoDeLiquidacion != 1 && $liquidacion->TipoDeLiquidacion != 3) return false;

        // verifoco que no sea otro el ente recaudador
        if ($this->retieneOtro($datos)) return false;

        // Si no tiene Liquidacion debe ser todo 0 pero el proceso se hace igual
        $this->setGananciaBruta($datos);

        // seteo los descuentos del Recibo de Sueldo
        $this->setDeduccionesRecibosPropios($datos);

        // seteo los valores de Recibos de Terceros
        $this->setDeduccionesRecibosTerceros($datos);

        // seteo las deducciones personales (presentadas en el 572)
        $this->setDeduccionesPersonales($datos);

        // $this->setPagosyDevoluciones($datos);


        if (!$this->retieneOtro($datos)) {
            // seteo los pagos o devoluciones
            $this->setPagosyDevoluciones($datos);
        }


    }


    public function liquidacionSinGanancias($tipoDeLiquidacion) {
        $sql = "SELECT * FROM TiposDeLiquidaciones WHERE Id = $tipoDeLiquidacion AND NoCuentaParaGanancia = 1";
        $R   = $this->_db->fetchAll($sql);
        if ($R) { return true; } else { return false; }
    }

    public function retieneOtro($datos) {

        $sql = "    SELECT  PGPP.Id
                    FROM    PersonasGananciasPluriempleoPeriodos PGPP
                    INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PGPP.PersonaGananciaPluriempleo
                    WHERE   PGPP.FechaInicio                    <=    '{$datos['periodoFH']}'
                    AND     ifnull(PGPP.FechaFin,'2999-01-01')  >=   '{$datos['periodoFD']}'
                    AND     PGP.Persona                         =    {$datos['idPersona']}
                    AND     ifnull(PGPP.EmpresaQueRetiene,999) not in (1,2,3)
                    -- AND     PGPP.EsEnteRecaudador               =    0
        ";

        $sql = "SELECT  count(PGP.Id) as cant
                FROM    PersonasGananciasPluriempleo PGP
                INNER JOIN PersonasGananciasPluriempleoPeriodos PGPP on PGP.Id = PGPP.PersonaGananciaPluriempleo
                WHERE   PGP.Persona                          =  {$datos['idPersona']}
                AND     PGPP.FechaInicio                     <= '{$datos['periodoFD']}'
                AND     ifnull(PGPP.FechaFin,'2999-01-01')   >= '{$datos['periodoFD']}'
                AND     PGPP.EsEnteRecaudador                =  0
                AND     ifnull(PGPP.EmpresaQueRetiene,999) not in (1,2,3)
                ";

        $RetienOtro = $this->_db->fetchOne($sql);
        if ($RetienOtro) { return true; } else { return false; }
    }

    public function gananciaNormal($d) { 

		$sql = "	SELECT count(Id) as cantidad 
					from   	Servicios 
					where   Empresa <> 	{$d['idEmpresa']}
					and     Persona = 	{$d['idPersona']}
					-- Baja en este año
					and		ifnull(FechaBaja,'2999-01-01') >= '2016-01-01'
					and 	ifnull(FechaBaja,'2999-01-01') <= '2016-12-31'
					-- En otro servicio que no sea el que estoy liquidando
					and 	Id <> {$d['idServicio']}
					-- Y que tenga ganancia tipo 2
					and 	ifnull(GananciaAlBajar,1) <> 1
					-- Y que la baja sea en un periodo anterior o en este mismo
					-- and     {$d['$periodoFD']} >  ifnull(FechaBaja,'2999-01-01')
					and     '{$d['periodoFH']}' >=  ifnull(FechaBaja,'2999-01-01') 
		";
		echo '++ sql gananciaNormal ++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
        echo $sql . PHP_EOL;
        echo '++ sql gananciaNormal ++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;

        $r = $this->_db->fetchOne($sql);
        if ($r) { return false; } else { return true; }
    }


    public function setGananciaBruta(&$datos) {

        $datos['MontoNoHabitualProrrateado'] = 0;

        // Recupero el ingreso bruto habitual
        $datos['MontoHabitual']                 = $this->getIngresoBrutoHabitual($datos, null);
        //$datos['MontoHabitual'] = $datos['MontoHabitual'] + 666.518;


        // Recupero el ingreso bruto No habitual
        $datos['MontoNoHabitual']               = $this->getIngresoBrutoNoHabitual($datos, null);
        $m2 = $datos['MontoNoHabitual'];

        // Recupero el ingreso bruto No habitual No Remunerativo
        $datos['MontoNoHabitualNoRemunerativo'] = $this->getIngresoBrutoNoHabitualNoRemunerativo($datos, null);
        $m3 = $datos['MontoNoHabitualNoRemunerativo'];

        $datos['MontoPlusVacaciones']   = $this->getPlusVacaciones($datos, null);

        // Veo que lo No habitual sea menor a lo habitual (moviendo el plus a su vez)
        $datos['MontoHabitual']     = $datos['MontoHabitual'] - $datos['MontoPlusVacaciones'];
        $datos['MontoNoHabitual']   = $datos['MontoNoHabitual'] + $datos['MontoPlusVacaciones'];

        $datos['MontoNoHabitualOriginal'] = $datos['MontoNoHabitual'];

        $datos['MontoTotal']        = $datos['MontoHabitual'] + $datos['MontoNoHabitual'];
        // Veo la proporcion que es lo no habitual del total

        // Se usa para el prorrateo de las deducciones
        $sumRem = $this->getSumRemunerativos($datos);
        if ($sumRem) {
            $datos['proporcionNoHabitual'] = ($datos['MontoNoHabitual'] - $datos['MontoNoHabitualNoRemunerativo']) * 100 / $this->sumRem;
        } else {
            $datos['proporcionNoHabitual'] = 100;
        }

        // Este is es para marcar cuando arrancamos a implementar prorrateo
        if (	( $datos['anio'] > 2014 || ($datos['anio'] == 2014 && $datos['mes'] >= 2 )) && ( $this->gananciaNormal($datos) ) ) {

        // Recupero el plus de Vacaciones si hay... esto es un caso particular del Sindicato
        // Luego debere restarlo a lo habitual y sumarlo en lo no habitual

            $veintePorcientoMH = $datos['MontoHabitual'] * 0.2;

            if ($datos['MontoNoHabitual'] > $veintePorcientoMH && $datos['mes'] != 12) {
                // en este caso debo prorratear lo no habitual por los meses que quedan
                // he informar el monto ese solamente

                // 1. Veo la cantidad de meses que faltan contando este (el +1 es para incluir este mes)
                $mes = (int)$datos['mes'];
                $datos['MesesPendientes'] = 12 - $mes + 1;

                $datos['MontoNoHabitualProrrateado'] = $this->setProrrateoNoHabitual($datos);
                $datos['MontoNoHabitual'] = 0;
            }
        }

        echo '++ Resultados ++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
        echo '$Anio '.                          $datos['anio'].PHP_EOL;
        echo '$Mes '.                           $datos['mes'].PHP_EOL;
        echo '$MontoNoHabitualProrrateado '.    $datos['MontoNoHabitualProrrateado'].PHP_EOL;
        echo '$MontoPlusVacaciones '.           $datos['MontoPlusVacaciones'].PHP_EOL;
        echo '$MontoHabitual '.                 $datos['MontoHabitual'] .PHP_EOL;
        echo '$MontoNoHabitual Pre'.            $m2 .PHP_EOL;
        echo '$MontoNoHabitualNoRemunerativo'.  $m3 .PHP_EOL;
        echo '$MontoNoHabitual '.               $datos['MontoNoHabitual'] .PHP_EOL;
        echo '++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;


        // Busco los acumulados mes a mes
        // Como el bruto no cambia sumo mes a mes lo que tiene y en el caso que tenga prorrateo solo tomo del vamos
        // lo habitual menos el plus y agrego el prorrateo acumulado para ese mes

        $sql = "    SELECT  distinct(LR.Periodo) as Periodo
                    FROM    LiquidacionesRecibos LR
                    INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                    WHERE   LR.Persona      =   {$datos['idPersona']}
                    AND     LP.FechaDesde   <= '{$datos['periodoFD']}'
                    AND     LP.FechaDesde   >= '{$datos['anio']}-01-01'
        ";

        echo '--Buscador Acumulados-------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $R = $this->_db->fetchAll($sql);
        if ($R) {

            $M_LP = new Liquidacion_Model_DbTable_LiquidacionesPeriodos;

            foreach ($R as $row){

                // Inicializo
                $MontoProrrateo   = false;
                $periodoBusqueda  = $M_LP->getPeriodo($row['Periodo']);

                // 1. Veo se se prorrateo algo esa liquidacion de ser asi no calculo el monto no habitual.
                $total = false;
                if ( $this->gananciaNormal($datos)) {
                	$MontoProrrateoDelMes       = $this->getProrrateoNoHabitual($datos,$periodoBusqueda);
                } else {
                	$MontoProrrateoDelMe = 0;
                }
             

                // 2. Calculo el plus para restarlo del habitual
                $MontoPlusVacaciones        = $this->getPlusVacaciones($datos,$periodoBusqueda);

                // 3. Calculo el Habitual
                $MontoHabitual              = $this->getIngresoBrutoHabitual($datos,$periodoBusqueda);

                // 4. Calculo lo no habitual
                $MontoNoHabitual            = $this->getIngresoBrutoNoHabitual($datos,$periodoBusqueda);

                // 5. Recupero lo que tiene prorrateado para ese mes
                $total = true;
                if ( $this->gananciaNormal($datos)) {
                	$MontoProrrateoTotalDelMes  = $this->getProrrateoNoHabitual($datos,$periodoBusqueda,$total);
				} else {
                	$MontoProrrateoTotalDelMes = 0;
                }

                // 6. Acumulo
                $maH   += $MontoHabitual - $MontoPlusVacaciones;
                $maP   += $MontoProrrateoTotalDelMes;

                if ($MontoProrrateoDelMes) {
                    $maNH    += 0;
                } else {
                    $maNH    += $MontoNoHabitual + $MontoPlusVacaciones;
                }

                echo '++ Resultados Acumulados +++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
                echo '$Anio '.$datos['anio'].PHP_EOL;
                echo '$Mes '.$periodoBusqueda->getDesde()->format('m');
                echo '$MontoProrrateoDelMes '.$MontoProrrateoDelMes.PHP_EOL;
                echo '$MontoPlusVacaciones '.$MontoPlusVacaciones.PHP_EOL;
                echo '$MontoHabitual '.$MontoHabitual.PHP_EOL;
                echo '$MontoNoHabitual '.$MontoNoHabitual.PHP_EOL;
                echo '$MontoProrrateoTotalDelMes '.$MontoProrrateoTotalDelMes.PHP_EOL;
                echo '++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
            }

            $datos['MontoHabitualAcumulado']    = $maH;
            $datos['MontoNoHabitualAcumulado']  = $maNH;
            $datos['MontoProrrateoAcumulado']   = $maP;

            // 7. Inserto todo

            // 7.1 ---> Habitual
            $dH = array(    'Persona'             => $datos['idPersona'],
                            'Recibo'              => $datos['idRecibo'],
                            'Liquidacion'         => $datos['idLiquidacion'],
                            'GananciaConcepto'    => 36,
                            'Monto'               => $datos['MontoHabitual'],
                            'MontoAcumulado'      => $datos['MontoHabitualAcumulado'],
                            'GananciaMesPeriodo'  => $datos['mes'],
                            'GananciaAnioPeriodo' => $datos['anio']
                );
            $this->insert($dH);

            // 7.2 ---> No Habitual
            $dNH = array(   'Persona'             => $datos['idPersona'],
                            'Recibo'              => $datos['idRecibo'],
                            'Liquidacion'         => $datos['idLiquidacion'],
                            'GananciaConcepto'    => 37,
                            'Monto'               => $datos['MontoNoHabitual'],
                            'MontoAcumulado'      => $datos['MontoNoHabitualAcumulado'],
                            'GananciaMesPeriodo'  => $datos['mes'],
                            'GananciaAnioPeriodo' => $datos['anio']
                );
            $this->insert($dNH);

			if ( $this->gananciaNormal($datos)) {
	            // 7.3 ---> acumulado prorrateo ()
	            $dP = array(    'Persona'             => $datos['idPersona'],
	                            'Recibo'              => $datos['idRecibo'],
	                            'Liquidacion'         => $datos['idLiquidacion'],
	                            'GananciaConcepto'    => 44,
	                            'Monto'               => $datos['MontoNoHabitualProrrateado'],
	                            'MontoAcumulado'      => $datos['MontoNoHabitualProrrateado'],
	                            //'MontoAcumulado'      => $datos['MontoProrrateoAcumulado'],
	                            'GananciaMesPeriodo'  => $datos['mes'],
	                            'GananciaAnioPeriodo' => $datos['anio']
	                );
	            $this->insert($dP);
	        }

            // $MAT = ($datos['MontoHabitual'] + $datos['MontoNoHabitual'] + $datos['MontoNoHabitualProrrateado']) /12;
            $MAT = $this->getSumRemunerativos($datos);
            if ($MAT) $MAT = $MAT / 12;
            $dd  = array(   'Persona'             => $datos['idPersona'],
                            'Recibo'              => $datos['idRecibo'],
                            'Liquidacion'         => $datos['idLiquidacion'],
                            'GananciaConcepto'    => 53,
                            'Monto'               => $MAT,
                            'MontoAcumulado'      => $MAT,
                            'GananciaMesPeriodo'  => $datos['mes'],
                            'GananciaAnioPeriodo' => $datos['anio']
                );
            $this->insert($dd);
            // OJO... para los meses siguientes falta el resto del prorrateo

            // 7.4 ---> prorrateados de meses anteriores
            $MontoProrrateadoAcumulado = 0;

			if ( $this->gananciaNormal($datos)) {

	            for ($mes = 1; $mes <= $datos['mes']; $mes++) {

	                $sql = "    SELECT  sum(PGP.Monto)
	                            FROM    PersonasGananciasProrrateos PGP
	                            INNER JOIN LiquidacionesRecibos LR on LR.Id = PGP.LiquidacionRecibo
	                            WHERE   PGP.AnioGanancia        =   {$datos['anio']}
	                            AND     PGP.MesDesde            <=  $mes
	                            AND     PGP.MesHasta            >=  $mes
	                            AND     PGP.Persona             =   {$datos['idPersona']}
	                            AND     PGP.LiquidacionRecibo   <>  {$datos['idRecibo']}
	                ";

	                $MPA = $this->_db->fetchOne($sql);
	                $MontoProrrateadoAcumulado += ($MPA) ? $MPA : 0;
	            }

	            // Guardo el valor de $MPA ya que es el total de los otros prorrateos que existen y se
	            // aplican en este meses

	            $dPa = array(   'Persona'             => $datos['idPersona'],
	                            'Recibo'              => $datos['idRecibo'],
	                            'Liquidacion'         => $datos['idLiquidacion'],
	                            'GananciaConcepto'    => 45,
	                            'Monto'               => $MPA,
	                            'MontoAcumulado'      => $MontoProrrateadoAcumulado,
	                            'GananciaMesPeriodo'  => $datos['mes'],
	                            'GananciaAnioPeriodo' => $datos['anio']

	                );
	            $this->insert($dPa);
	        }
        }
    }

    public function getGananciaNetaAcumulada($mes,$anio,$personaId) {

        $sql = "    SELECT  SUM(MontoAcumulado) as GananciaNetaAcumulada
                    from    PersonasGananciasLiquidaciones PGL
                    inner   join GananciasConceptos GC            on GC.Id = PGL.GananciaConcepto
                    inner   join GananciasConceptosTipos GCT      on GCT.Id = GC.GananciaConceptoTipo
                    where   PGL.Persona             = $personaId
                    and     PGL.Liquidacion         in
                                  (
                                  SELECT    L.Id
                                  From      LiquidacionesPeriodos LP
                                  inner join    Liquidaciones L                   on LP.Id = L.LiquidacionPeriodo
                                  where     LP.Anio     = $anio
                                  and       LP.Valor    = $mes
                                  and       L.TipoDeLiquidacion in (1,2,3)
                                  )
                    and     GC.GananciaConceptoTipo in (1,2,3,5)
        ";

        //            echo '                     -- getGananciaNetaAcumulada -------------------------------------------------'.PHP_EOL;
        //            echo $sql.PHP_EOL;
        //            echo '                     ------------------------------------------------------------------------'.PHP_EOL;

        $M = $this->_db->fetchOne($sql);
        if (!$M || $M < 0) {
            $M = 0;
        }

        return $M;

    }

    public function getMontoImpuesto($datos,$mes,$GananciaNetaAcumulada) {

        $sql = "    SELECT  E.*
                    FROM    AfipGananciasEscalas E
                    INNER JOIN AfipGananciasEscalasPeriodos P ON E.AfipEscalaPeriodo = P.Id
                    WHERE   E.Desde/12*$mes <  $GananciaNetaAcumulada
                    AND     E.Hasta/12*$mes >= $GananciaNetaAcumulada
                    AND     P.FechaDesde >= '".$datos['periodoFD']."'";

        $tablaAfip = $this->_db->fetchRow($sql);

        //if (!$tablaAfip) throw new Rad_Db_Table_Exception("Falta ingresar la tabla de datos de ganancia para el periodo seleccionado.");

        $limiteInferior     = $tablaAfip['Desde'] / 12 * $mes;
        $Alicuota           = $tablaAfip['Alicuota'];
        $CanonFijo          = $tablaAfip['CanonFijo'];

        $montoImpuestoAcumulado   = (($GananciaNetaAcumulada - $limiteInferior) * ($Alicuota / 100))  + ($CanonFijo / 12 * $mes);

        /*
                    echo '                     -- getMontoImpuesto -------------------------------------------------'.PHP_EOL;
                    echo '                     $limiteInferior : '.$limiteInferior.PHP_EOL;
                    echo '                     $mes : '.$mes.PHP_EOL;
                    echo '                     $GananciaNetaAcumulada : '.$GananciaNetaAcumulada.PHP_EOL;
                    echo '                     $Alicuota  : '. $Alicuota .PHP_EOL;
                    echo '                     $CanonFijo : '.$CanonFijo.PHP_EOL;
                    echo '                     $montoImpuestoAcumulado : '.$montoImpuestoAcumulado.PHP_EOL;
                    echo '                     ------------------------------------------------------------------------'.PHP_EOL;
        */
        return $montoImpuestoAcumulado;
    }

    public function putGananciaBeneficios($liquidacion, $agrupacion = null, $valor = null) {

        echo '-- Arranco -------------------------------------------------'.PHP_EOL;

        $M_LR           = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibos);
        $M_LRD          = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles);
        $M_LP           = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesPeriodos);
        $M_P            = Service_TableManager::get(Base_Model_DbTable_Personas);
        $M_Concepto     = Service_TableManager::get(Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles);

        // echo ' liquidacion->LiquidacionPeriodo : '.$liquidacion->LiquidacionPeriodo.PHP_EOL;

        $periodo = $M_LP->getPeriodo($liquidacion->LiquidacionPeriodo);

        $and = "";

        if ($agrupacion && $valor) {
            switch ($agrupacion) {
                case 'SERVICIO':
                    $and = " AND Servicio = $valor ";
                    break;
                /* --- va siempre en el filtro de empresa, no hace falta aca

                case 'EMPRESA':
                    $and = "AND Servicio in (Select Id from Servicios where Empresa = $valor";
                    break;
                */
               default:
                    # code...
                    break;
            }
        }

        $liquidacionId  = $liquidacion->Id;
        $empresaId      = $liquidacion->Empresa;
        $periodoId      = $periodo->getId();
        $mes            = $periodo->getMes();
        $anio           = $periodo->getAnio();

        $sql = "Ajuste = 0 and Periodo = $periodoId and Servicio in (Select Id from Servicios where Empresa = $empresaId) $and";

        $sql = "Liquidacion = $liquidacionId and Ajuste = 0 and Periodo = $periodoId $and";

        echo $sql.PHP_EOL;

        $R_LR = $M_LR->fetchAll($sql);

        // Si existen liquidaciones para ese mes calculo
        if ($R_LR) {
            //$M_PGL  = new Rrhh_Model_DbTable_PersonasGananciasLiquidaciones;
            $M_CV   = new Model_DbTable_CaracteristicasValores;

            $modelo         = 57; // Base_Model_DbTable_Empleados
            $caracteristica = 15; // TramosGanancias

            foreach ($R_LR as $row) {
                // Recupero la Persona
                $persona        = $row['Persona'];
                $pDesc          = $M_P->find($row['Persona'])->current();
                $personaDesc    = $pDesc->LegajoNumero.' '.$pDesc->RazonSocial;
                $reciboId       = $row['Id'];
                $rango          = $M_CV->getValor($persona, $caracteristica, $modelo);

                echo PHP_EOL.PHP_EOL.'*********************************************************************'.PHP_EOL;
                echo '     Persona : '.$persona. '   '. $personaDesc. PHP_EOL;
                echo '*********************************************************************'.PHP_EOL;


                if (!$rango || $rango == 1) {

                    // ------------ Ganancias Netas -------------------
                    $GananciaNetaAcumulada              = $this->getGananciaNetaAcumulada($mes,$anio,$persona); // ($mes,$persona);

                    if ($mes == 1) {
                        $GananciaNetaAcumuladaAnterior  = 0;
                        $mesAnterior = 0;
                    } else {
                        $mesAnterior = $mes - 1;
                        $GananciaNetaAcumuladaAnterior  = $this->getGananciaNetaAcumulada($mesAnterior,$anio,$persona); //($mesAnterior,$persona);
                    }

                    $GananciaNetaActual = $GananciaNetaAcumulada - $GananciaNetaAcumuladaAnterior;

                    // ------------ Beneficios -------------------

                    $MontoImpuesto                  = $this->getMontoImpuesto($datos,$mes,$GananciaNetaAcumulada);
                    if ($mesAnterior) {
                        $montoImpuestoMesAnterior   = $this->getMontoImpuesto($datos,$mesAnterior,$GananciaNetaAcumuladaAnterior);
                    } else {
                        $montoImpuestoMesAnterior   = 0;
                    }

                    if ($MontoImpuesto > 0 && $montoImpuestoMesAnterior > 0 && ( $montoImpuestoMesAnterior < $MontoImpuesto)) {

                    $Beneficio          = $MontoImpuesto - $montoImpuestoMesAnterior;
                    $BeneficioAcumulado = $MontoImpuesto;

                    echo '-- var -------------------------------------------------'.PHP_EOL;
                    echo '$GananciaNetaAcumulada : '.$GananciaNetaAcumulada.PHP_EOL;
                    echo '$mes : '.$mes.PHP_EOL;
                    echo '$mesAnterior : '.$mesAnterior.PHP_EOL;
                    echo '$GananciaNetaAcumuladaAnterior : '.$GananciaNetaAcumuladaAnterior.PHP_EOL;
                    echo '$GananciaNetaActual : '.$GananciaNetaActual.PHP_EOL;
                    echo '$MontoImpuesto : '.$MontoImpuesto.PHP_EOL;
                    echo '$montoImpuestoMesAnterior : '.$montoImpuestoMesAnterior.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;

                    // Inserto en PersonasGananciasLiquidaciones

                        // Borro si existen
                        $this->delete("Liquidacion = $liquidacionId and GananciaConcepto = 41 and GananciaMesPeriodo = $mes and GananciaAnioPeriodo = $anio and Persona = $persona");

                        // Inserto
                        $d = array( 'Persona'             => $persona,
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $liquidacionId,
                                    'GananciaMesPeriodo'  => $mes,
                                    'GananciaAnioPeriodo' => $anio,
                                    'GananciaConcepto'    => 41,
                                    'Monto'               => $Beneficio,
                                    'MontoAcumulado'      => $BeneficioAcumulado
                        );

                        echo '-- 111 -------------------------------------------------'.PHP_EOL;
                        print_r($d);
                        echo '------------------------------------------------------------------------'.PHP_EOL;

                        $this->insert($d);

                    // Inserto en LiquidacionesRecibosDetalles

                        // Borro si existen
                        $M_LRD->delete("LiquidacionRecibo = $reciboId and VariableDetalle in (500,499)");

                        // Inserto

                        // var 95: Beneficio 1242/2012 --> varDetalle = 500

                        // OJO ... beneficio viene en negativo y lo paso a positivo
                        $c = array(     'LiquidacionRecibo'   => $reciboId,
                                        'VariableDetalle'     => 500,
                                        'Monto'               => -$Beneficio,
                                        'MontoCalculado'      => -$Beneficio,
                                        'PeriodoDevengado'    => $periodoId,
                                        'Detalle'             => '',
                                        'ConceptoCodigo'      => $M_Concepto->getCodigo(500),
                                        'ConceptoNombre'      => $M_Concepto->getNombre(500)
                            );

                        echo '-- 222 -------------------------------------------------'.PHP_EOL;
                        print_r($c);
                        echo '------------------------------------------------------------------------'.PHP_EOL;

                        $M_LRD->insert($c);

                        // 108: concepto 509 Pago Ganancias -->
                        $c = array(     'LiquidacionRecibo'   => $reciboId,
                                        'VariableDetalle'     => 499,
                                        'Monto'               => $Beneficio,
                                        'MontoCalculado'      => $Beneficio,
                                        'PeriodoDevengado'    => $periodoId,
                                        'Detalle'             => '',
                                        'ConceptoCodigo'      => $M_Concepto->getCodigo(499),
                                        'ConceptoNombre'      => $M_Concepto->getNombre(499)
                            );

                        echo '-- 333 -------------------------------------------------'.PHP_EOL;
                        print_r($c);
                        echo '------------------------------------------------------------------------'.PHP_EOL;

                        $M_LRD->insert($c);
                    } else {
                        echo '                     -- Monto Impuesto < 0 ----> '.$persona.' '.$rango.PHP_EOL;
                    }
                } else {

                    echo '                     -- Rango <> 1 ----> '.$persona.' '.$rango.PHP_EOL;

                }
            }
        }

        echo '-- Termino -------------------------------------------------'.PHP_EOL;

    }

    public function getProrrateoNoHabitual(&$datos,$periodoBusqueda,$totalMes = false) {

        if($periodoBusqueda) {
            $mesBusqueda   = $periodoBusqueda->getDesde()->format('m');
            $anioBusqueda  = $periodoBusqueda->getDesde()->format('Y');
        } else {
            $mesBusqueda   = $datos['mes'];
            $anioBusqueda  = $datos['anio'];
        }

        if ($totalMes) {
            $condicion = " AND MesDesde <= $mesBusqueda AND MesHasta >= $mesBusqueda";
        } else {
            $condicion = " AND MesDesde = $mesBusqueda ";
        }

        $sql = "    SELECT  sum(PGP.Monto)
                    FROM    PersonasGananciasProrrateos PGP
                    WHERE   AnioGanancia    =   $anioBusqueda
                    $condicion
                    AND     Persona         =   {$datos['idPersona']}

        ";

        $MontoProrrateado = $this->_db->fetchOne($sql);
        $MontoProrrateado = ($MontoProrrateado) ? $MontoProrrateado : 0;

        return $MontoProrrateado;
    }


    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setProrrateoNoHabitual(&$datos) {

        // inicializo
        $ahora          = date('Y-m-d H:i:s');

        // 1. Veo la cantidad de meses que faltan contando este (el +1 es para incluir este mes)
        $mes = (int)$datos['mes'];
        $MesesPendientes    = 12 - $mes + 1;
        // monto no habitual ya tiene a esta altura agregado el plus de vacaciones
        $MontoProrrateado   = $datos['MontoNoHabitual'] / $MesesPendientes;

        echo '== setProrrateoNoHabitual =========================================================='.PHP_EOL;
        echo '$datos_mes '.              $datos['mes'].PHP_EOL;
        echo '$mes '.                    $mes.PHP_EOL;
        echo '$MesesPendientes '.        $MesesPendientes.PHP_EOL;
        echo '$MontoProrrateado '.       $MontoProrrateado.PHP_EOL;
        echo 'MontoNoHabitual '.         $datos['MontoNoHabitual'].PHP_EOL;
        echo 'MontoPlusVacaciones '.     $datos['MontoPlusVacaciones'].PHP_EOL;
        echo '========================================================================'.PHP_EOL;

        // 2. Almaceno el dato para los meses siguientes
        $M = new Rrhh_Model_DbTable_PersonasGananciasProrrateos;

        $d = array(     'LiquidacionRecibo'   => $datos['idRecibo'],
                        'Monto'               => $MontoProrrateado,
                        'AnioGanancia'        => $datos['anio'],
                        'MesDesde'            => $datos['mes'],
                        'MesHasta'            => 12,
                        'FechaCarga'          => $ahora,
                        'Persona'             => $datos['idPersona'],
                        'GananciaConcepto'    => 44
            );
        $M->insert($d);

        // 3. Devuelvo el Monto prorrateado
        return $MontoProrrateado;
    }

























    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setProrrateoDeducciones(&$datos) {

        // inicializo
        $ahora          = date('Y-m-d H:i:s');

        // Recupero las deducciones del recibo

        $MontoProrrateado   = $datos['MontoNoHabitual'] / $datos['MesesPendientes'];

        echo '== setProrrateoNoHabitual =========================================================='.PHP_EOL;
        echo '$datos_mes '.              $datos['mes'].PHP_EOL;
        echo '$mes '.                    $mes.PHP_EOL;
        echo '$MesesPendientes '.        $datos['MesesPendientes'].PHP_EOL;
        echo '$MontoProrrateado '.       $MontoProrrateado.PHP_EOL;
        echo 'MontoNoHabitual '.         $datos['MontoNoHabitual'].PHP_EOL;
        echo 'MontoPlusVacaciones '.     $datos['MontoPlusVacaciones'].PHP_EOL;
        echo '========================================================================'.PHP_EOL;

        // 2. Almaceno el dato para los meses siguientes
        $M = new Rrhh_Model_DbTable_PersonasGananciasProrrateos;

        $d = array(     'LiquidacionRecibo'   => $datos['idRecibo'],
                        'Monto'               => $MontoProrrateado,
                        'AnioGanancia'        => $datos['anio'],
                        'MesDesde'            => $datos['mes'],
                        'MesHasta'            => 12,
                        'FechaCarga'          => $ahora,
                        'Persona'             => $datos['idPersona'],
                        'TipoProrrateo'       => 'DNH'
            );
        $M->insert($d);

        // 3. Devuelvo el Monto prorrateado
        return $MontoProrrateado;
    }

    /**
     * Busca el plus de las licencias y vacaciones.... por ahora solo de las vacaciones
     * este monto hay que descontarlo de lo habitual y sumarlo a lo no habitual
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getPlusVacaciones(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     L.Empresa   = {$datos['idEmpresa']}
                    AND     V.TipoDeConcepto in (16,18,17,19)
                    -- 16: descuentos dias licencias, 18: pago dias licencias
                    -- 17: descuentos dias vacaciones, 19: pago dias vacaciones
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     LR.Persona  = {$datos['idPersona']}
        ";


        echo '--Plus Vacaciones-------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve el monto del sac para el seg semestre del 2014
     *
     * @param   array       $idPersona   id de Persona
     * @return  integer
     */
    public function getSac2sem2014($idRecibo) {

        $sql = "    SELECT  ifnull(LRD.Monto,0)
                    FROM    LiquidacionesRecibosDetalles LRD
                    INNER JOIN VariablesDetalles VD on VD.Id = LRD.VariableDetalle
                    WHERE   LRD.LiquidacionRecibo = $idRecibo
                    AND     VD.Variable in (249,252)
        ";

        $r = $this->_db->fetchOne($sql);
        $r = ($r) ? $r : 0;
        return $r;
    }


    /**
     * Devuelve verdadero o falso dependiendo si tiene una remuneracion bruta mayora 35k entre
     * Julio y dic de 2014
     *
     * @param   array       $idPersona   id de Persona
     * @return  integer
     */
    public function getCobroMasDe35kEntreJulioDiciembre2014($idPersona) {

        $sql = "    SELECT count(*)
                    FROM (
                        SELECT      sum(LRD.Monto) as Monto, LP.Valor
                                    FROM        LiquidacionesRecibosDetalles    LRD
                                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                                    INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.ID   = LR.Periodo
                                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                                    WHERE   LR.Ajuste   = 0
                                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5)
                                    AND     LRD.Monto   <> 0
                                    AND     LP.Anio     = 2014
                                    AND     LP.Valor    >= 7
                                    AND     LP.Valor    <= 12
                        --            AND     LR.Periodo  = $idPeriodo
                                    AND     LR.Persona  = $idPersona
                                    -- Parche para qeu no tome los adelantos
                                    AND     L.TipoDeLiquidacion in (1,2,3)
                                    -- Saco el SAC que no lo tome...ojo el 2014 ahora
                                    AND     V.Id not in (249,252,108,262)
                                    group by LP.Valor
                        ) as L
                    WHERE L.Monto > 35000
        ";

        echo '-- getCobroMasDe35kEntreJulioDiciembre2014 -----------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $r = $this->_db->fetchOne($sql);
        $r = ($r) ? $r : 0;
        return $r;
    }

    /**
     * Devuelve los descuentos de un recibo
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getSumDescuentos(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (4) -- Descuentos
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}
                    AND     LR.Persona  = {$datos['idPersona']}
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
        ";

        echo '-- getSumDescuetos -------------------------------------------------'.PHP_EOL;
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
    public function getDescuentosQueNoSuman(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (4) -- Descuentos
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}                    
                    AND     LR.Persona  = {$datos['idPersona']}
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
                    AND     V.Id in (227, 105,262,263) -- 227: Amutcaer, 105 Descuentos de anticipos, 262 y 364 imp ganancia sujeta a benesficio y beneficio 12/2014

        ";

        echo '-- getAmutcaer -------------------------------------------------'.PHP_EOL;
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
    public function getSumRemunerativos(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2)
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}                    
                    AND     LR.Persona  = {$datos['idPersona']}
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
        ";

        echo '-- getSumRemunerativos -------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve el ingreso bruto mensual sin descontar el plus que viene metido dentro de lo habitual
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getIngresoBrutoHabitual(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5)
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}
                    AND     LR.Persona  = {$datos['idPersona']}
                    AND     ifnull(V.NoHabitual,0) = 0
                    -- Parche para qeu no tome los adelantos
                    AND     L.TipoDeLiquidacion in (1,2,3)
                    -- Parche para que no sume el concepto beneficio de ganancia o el redondeo
                    AND     V.Id not in (108) -- ,118)
                    -- No cuanta para Ganacia
                    AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
        ";


        echo '-- getIngresoBrutoHabitual ---------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve el plus de las horas extra de dias feriados,inhabiles y Fines de semana que no suman al ingreso bruto mensual
     *
     * CaracteristicaGanancia = 5 -> HS. Extra normales
     * CaracteristicaGanancia = 6 -> HS. Extra Feriados, Fines de Semana e Inhabiles
     *
     * Necesito sacar solo el plus es decir el 50% o 100% extra pagada en las Hs Extra de tipo 6 ( Feriados, Fines de Semana e Inhabiles )
     * Variable->Id = 370 -> 100%
     * Variable->Id = 371 -> 50% 
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getPlusHorasExtrasQueNoSuman(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT SUM(Monto) FROM (
                        SELECT  CASE V.Id 
                                WHEN 370 THEN LRD.Monto / 2
                                WHEN 371 THEN LRD.Monto / 3
                                ELSE 0
                        END  as Monto
                        FROM        LiquidacionesRecibosDetalles    LRD
                        INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                        INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion 
                        INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                        INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                        WHERE   LR.Ajuste   = 0
                        AND     V.TipoDeConceptoLiquidacion in (1,2) /* Remunerativos */
                        AND     V.CaracteristicaGanancia in (6)
                        AND     LRD.Monto   <> 0
                        AND     LR.Periodo  = $idPeriodo
                        AND     L.Empresa   = {$datos['idEmpresa']}                    
                        AND     LR.Persona  = {$datos['idPersona']}
                        -- No cuanta para Ganacia
                        AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
                    ) as x
        ";

        echo '--No getPlusHorasExtrasQueNoSuman ----------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve el Monto Total de las Horas extra cobradas por cualquier causal
     *
     * CaracteristicaGanancia = 5 -> HS. Extra normales
     * CaracteristicaGanancia = 6 -> HS. Extra Feriados, Fines de Semana e Inhabiles
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getMontoHorasExtras(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT SUM(LRD.Monto)
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion 
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2) /* Remunerativos */
                    AND     V.CaracteristicaGanancia in (5,6)
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}                    
                    AND     LR.Persona  = {$datos['idPersona']}
                    -- No cuanta para Ganacia
                    AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
        ";

        echo '--No getMontoHorasExtras ----------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Devuelve el ingreso bruto mensual sin descontar el plus que viene metido dentro de lo habitual
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getIngresoBrutoNoHabitual(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion 
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5)
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}                    
                    AND     LR.Persona  = {$datos['idPersona']}
                    AND     ifnull(V.NoHabitual,0) <> 0
                    -- No cuanta para Ganacia
                    AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
        ";

        echo '--No Habitual----------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        
        /* agregado 2017-01 nuevas resoluciones afip */
        $PlusHorasExtrasQueNoSuman = 0;
        if($Monto) { 
            $PlusHorasExtrasQueNoSuman = $this->PlusHorasExtrasQueNoSuman($datos,$periodoBusqueda);
            if($PlusHorasExtrasQueNoSuman) $Monto = $monto - $PlusHorasExtrasQueNoSuman;
        }
        /* fin agregado */
        return $Monto;
    }

    /**
     * Devuelve el ingreso bruto No habitual NO REMUNERATIVO
     *
     * @param   array       $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  decimal
     */
    public function getIngresoBrutoNoHabitualNoRemunerativo(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion                     
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (3,5)
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = $idPeriodo
                    AND     L.Empresa   = {$datos['idEmpresa']}                    
                    AND     LR.Persona  = {$datos['idPersona']}
                    AND     ifnull(V.NoHabitual,0) <> 0
                    -- No cuanta para Ganacia
                    AND     ifnull(V.NoCuentaParaGanancia,0) <> 1

        ";

        echo '--No Habitual No Remunerativo ------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }


    /**
     * Devuelve la suma de los Pagos de Ganancia anteriores al mes actual del año en curso
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getPagosAnteriores($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo,$idEmpresa) {

        // *******************************************************************************************
        // Pagos Anteriores
        // *******************************************************************************************
        
        /*
        $sql = "SELECT      sum(LRD2.Monto) as MontoAcumulado
                FROM        LiquidacionesRecibosDetalles    LRD2
                INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                INNER JOIN  Liquidaciones                   L    ON  L.Id     = LR2.Liquidacion                 
                INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                WHERE   GC2.GananciaConceptoTipo in (6)
                AND     LR2.Ajuste       =  0
                AND     LRD2.Monto       <> 0
                AND     LP2.FechaDesde   >=  '$periodoGananciaFD'
                AND     LP2.FechaDesde   <   '$periodoGananciaFH'
                AND     LR2.Persona      =   $idPersona
                -- AND     LR2.Id           <>  $idRecibo --->  Para que no lo tome cuando reliquida 
                -- GROUP BY GC2.Id ---> ojo esta linea que es solo para depuracion
                AND     L.Empresa   = $idEmpresa                 
                ";
        */
       
        $sql = "SELECT ifnull(SUM(MontoAcumulado),0) as MontoAcumulado 
                From (
                        SELECT      ifnull(sum(LRD2.Monto),0) as MontoAcumulado
                        FROM        LiquidacionesRecibosDetalles    LRD2
                        INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                        INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                        INNER JOIN  Liquidaciones                   L    ON  L.Id     = LR2.Liquidacion                 
                        INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                        INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                        INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                        INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                        WHERE   GC2.GananciaConceptoTipo in (6)
                        AND     LR2.Ajuste       =  0
                        AND     LRD2.Monto       <> 0
                        AND     LP2.FechaDesde   >= '$periodoGananciaFD'
                        AND     LP2.FechaDesde   <  '$periodoGananciaFH'
                        AND     LR2.Persona      =  $idPersona
                        AND     L.Empresa        =  $idEmpresa  

                        UNION 

                        SELECT      ifnull(sum(PGPD.RetencionGanancias),0) as MontoAcumulado
                                    -- ifnull(sum(PGPD.DevolucionGanancia),0) as MontoAcumulado
                        FROM        PersonasGananciasPluriempleoDetalle PGPD
                        INNER JOIN  PersonasGananciasPluriempleo PGP on PGP.Id = PGPD.PersonaGananciaPluriempleo
                        WHERE   Persona = $idPersona
                        AND     PGPD.FechaDeLiquidacion   >=  '$periodoGananciaFD'
                        AND     PGPD.FechaDeLiquidacion   <   '$periodoGananciaFH'
                ) X ";

        echo "--Pagos Anteriores -----------------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $pagosAnteriores = $this->_db->fetchOne($sql);
        if (!$pagosAnteriores) $pagosAnteriores = 0;

        return $pagosAnteriores;
    }

    /**
     * Devuelve la suma de las Devoluciones de Ganancia anteriores al mes actual del año en curso
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getDevolucionesAnteriores($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo,$idEmpresa) {

        // *******************************************************************************************
        // Devoluciones Anteriores incluye los beneficios a partir del 2016
        // *******************************************************************************************
        
        /*
        $sql = "SELECT      sum(LRD2.Monto) as MontoAcumulado
                FROM        LiquidacionesRecibosDetalles    LRD2
                INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                INNER JOIN  Liquidaciones                   L    ON  L.Id     = LR2.Liquidacion                 
                INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                WHERE   GC2.GananciaConceptoTipo in (7,9)
                AND     LR2.Ajuste       = 0
                AND     LRD2.Monto       <> 0
                AND     LP2.FechaDesde   >=  '$periodoGananciaFD'
                AND     LP2.FechaDesde   <   '$periodoGananciaFH'
                AND     LR2.Persona      =   $idPersona
                -- AND     LR2.Id           <>  $idRecibo ---> Para que no lo tome cuando reliquida 
                -- GROUP BY GC2.Id ---> ojo esta linea que es solo para depuracion
                AND     L.Empresa   = $idEmpresa";
        */
       
        $sql = "SELECT ifnull(SUM(MontoAcumulado),0) as MontoAcumulado 
                From (
                        SELECT      ifnull(sum(LRD2.Monto),0) as MontoAcumulado
                        FROM        LiquidacionesRecibosDetalles    LRD2
                        INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                        INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                        INNER JOIN  Liquidaciones                   L    ON  L.Id     = LR2.Liquidacion                 
                        INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                        INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                        INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                        INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                        WHERE   GC2.GananciaConceptoTipo in (7,9)
                        AND     LR2.Ajuste       =  0
                        AND     LRD2.Monto       <> 0
                        AND     LP2.FechaDesde   >= '$periodoGananciaFD'
                        AND     LP2.FechaDesde   <  '$periodoGananciaFH'
                        AND     LR2.Persona      =  $idPersona
                        AND     L.Empresa        =  $idEmpresa

                        UNION 

                        SELECT      ifnull(sum(PGPD.DevolucionGanancia),0) as MontoAcumulado
                        FROM        PersonasGananciasPluriempleoDetalle PGPD
                        INNER JOIN  PersonasGananciasPluriempleo PGP on PGP.Id = PGPD.PersonaGananciaPluriempleo
                        WHERE   Persona = $idPersona
                        AND     PGPD.FechaDeLiquidacion   >=  '$periodoGananciaFD'
                        AND     PGPD.FechaDeLiquidacion   <   '$periodoGananciaFH'
                ) X ";

        echo "--Devoluciones Anteriores ----------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $devolucionesAnteriores = $this->_db->fetchOne($sql);
        if (!$devolucionesAnteriores) $devolucionesAnteriores = 0;

        return $devolucionesAnteriores;
    }


    /**
     * Devuelve el monto imponible para el calculo de ganancias
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getMontoImponible($idPersona,$mes,$anio,$idRecibo) {

        // *******************************************************************************************
        // Monto imponible (ojo 41 es el beneficio en las deducciones)
        // OJO !!!! el beneficio YA esta incluida en las deduccions por lo tanto no debe ser
        // sumado NUNCA con las deducciones por lo tanto SIEMPRE el 41 Debe esta en el not del where
        // *******************************************************************************************
        $sql = "SELECT  sum(MontoAcumulado)
                FROM    PersonasGananciasLiquidaciones PGL
                WHERE   PGL.Persona             = $idPersona
                AND     PGL.GananciaMesPeriodo  = $mes
                AND     PGL.GananciaAnioPeriodo = $anio
                AND     PGL.GananciaConcepto not in (34,35,41,42,43)
                -- Parche para mas de un recibo por mes 2015-12-15
                AND     PGL.Recibo              = $idRecibo
                ";


        echo "--Monto imponible ------------------------------------------------------".PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;


        $MontoImponible = $this->_db->fetchOne($sql);
        if (!$MontoImponible) $MontoImponible = 0;

        return $MontoImponible;
    }

    public function updateAcumuladoGanancia($idRecibo,$MontoNuevo = 0) {

        $sql = "    SELECT      LP.Anio,
                                LP.FechaDesde   as PeriodoFechaDesde,
                                LP.FechaHasta   as PeriodoFechaHasta,
                                LR.Persona,
                                L.TipoDeLiquidacion,
                                LP.Valor        as Valor,
                                L.Empresa       as Empresa
                    FROM        LiquidacionesRecibos LR
                    INNER JOIN  LiquidacionesPeriodos LP    on LP.Id    = LR.Periodo
                    INNER JOIN  Liquidaciones L             on L.Id     = LR.Liquidacion
                    WHERE       LR.Id = $idRecibo
        ";
        $r = $this->_db->fetchRow($sql);

        if ($r) {
            $periodoGananciaFD      = $r['Anio']."-01-01";
            $periodoGananciaFH      = $r['PeriodoFechaHasta'];
            $idPersona              = $r['Persona'];
            $idEmpresa              = $r['Empresa'];

            $pagosAnteriores        = $this->getDevolucionesAnteriores($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo,$idEmpresa);
            $devolucionesAnteriores = $this->getPagosAnteriores($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo,$idEmpresa);

            if (!$MontoNuevo) $MontoNuevo = $this->_db->fetchOne("SELECT ifnull(Monto,0) FROM PersonasGananciasLiquidaciones WHERE Recibo = $idRecibo AND GananciaConcepto = 43");
            $acumulado  = $MontoNuevo + $devolucionesAnteriores + $pagosAnteriores;
            // busco y updateo el acumulado... veamos...
            // 1. Ganancia concepto 43 tiene el total

            $d = array( "Monto" => $MontoNuevo, "MontoAcumulado" => $acumulado, "GananciaConcepto" => 43);

            $w = " Recibo = $idRecibo and GananciaConcepto = 43 and Persona = ". $r['Persona'];
            $this->update($d,$w);

            // 2. Ganancia concepto 34 retencion -- Ganancia concepto 35 devolucion

            /*
            //Recupero la retencion y devolucion (ojo ... hacer por separado las dos busquedas)
            $MontoRetencion     = $this->_db->fetchOne("SELECT ifnull(Monto,0) FROM LiquidacionesRecibosDetalles WHERE LiquidacionRecibo = $idRecibo AND VariableDetalle = 500");
            $MontoDevolucion    = $this->_db->fetchOne("SELECT ifnull(Monto,0) FROM LiquidacionesRecibosDetalles WHERE LiquidacionRecibo = $idRecibo AND VariableDetalle = 484");

            if ($MontoRetencion || $MontoDevolucion) {
                if ($MontoDevolucion) {
                    //Devolucion
                    $Monto              = $MontoDevolucion;
                    $GananciaConcepto   = 35;
                } else {
                    //Retencion
                    $Monto              = $MontoRetencion;
                    $GananciaConcepto   = 34;
                }

                $d = array( "Monto" => $Monto, "GananciaConcepto" => $GananciaConcepto);
                $w = " Recibo = $idRecibo and GananciaConcepto = $GananciaConcepto and Persona = ". $r['Persona'];
                $this->update($d,$w);
            }
            */
            // TODO: logear estos cambio

            // Si es el mes doce no tengo qeu acumular mas
            if ($r['Valor'] < 12) {
                // veo si hay recibos posteriores a los que arreglar el acumulado
                $M_LR           = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesRecibos');
                $proximoRecibo  = $M_LR->proximoRecibo($idRecibo);

                /*    echo "-- Proximo Recibo-------------------------------------------------------".PHP_EOL;
                    echo $proximoRecibo.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;              */

       // throw new Rad_Db_Table_Exception($proximoRecibo);

                // si existe arreglo acumulado
                if ($proximoRecibo != 0) $this->updateAcumuladoGanancia($proximoRecibo);
            }
        }
    }


    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * y agrega el concepto al recibo de sueldo
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setPagosyDevoluciones(&$datos) {

        if ($datos['idRecibo']) {

            $periodoGananciaFD  = $datos['anio']."-01-01";
            $periodoGananciaFH  = $datos['periodoFH'];
            $idPersona          = $datos['idPersona'];
            $idRecibo           = $datos['idRecibo'];
            $mes                = $datos['mes'];
            $anio               = $datos['anio'];
            $idEmpresa          = $datos['idEmpresa'];

            // *******************************************************************************************
            // Devoluciones Anteriores
            // *******************************************************************************************
            $devolucionesAnteriores = $this->getDevolucionesAnteriores($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo,$idEmpresa);
            echo PHP_EOL."-----------> devolucionesAnteriores :  $devolucionesAnteriores ($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo) ---".PHP_EOL.PHP_EOL;
            // $devolucionesAnteriores = 0;

            // *******************************************************************************************
            // Pagos Anteriores
            // *******************************************************************************************
            $pagosAnteriores        = $this->getPagosAnteriores($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo,$idEmpresa);
            echo PHP_EOL."-----------> pagosAnteriores :  $pagosAnteriores ($periodoGananciaFD,$periodoGananciaFH,$idPersona,$idRecibo) ---".PHP_EOL.PHP_EOL;
            //$pagosAnteriores = 0;

            // *******************************************************************************************
            // Monto imponible (ojo 41 es el beneficio en las deducciones)
            // OJO !!!! el beneficio YA esta incluida en las deduccions por lo tanto no debe ser
            // sumado NUNCA con las deducciones por lo tanto SIEMPRE el 41 Debe esta en el not del where
            // *******************************************************************************************
            $MontoImponible         = $this->getMontoImponible($idPersona,$mes,$anio,$idRecibo);
            echo PHP_EOL."-----------> MontoImponible :  $MontoImponible ($idPersona,$mes,$anio,$idRecibo,$idEmpresa) ---".PHP_EOL.PHP_EOL;

            // **************************************************************************************************************
            // ******** PARCHE RES 3770 de GANANCIA ******** PARCHE RES 3770 de GANANCIA ******** PARCHE RES 3770 de GANANCIA
            // **************************************************************************************************************
            $devolucion3770 = 0;
            switch ($idPersona) {
                case 5:
                    $devolucion3770 = 631.41;
                    break;
                case 6:
                    $devolucion3770 = 3190.93;
                    break;
                case 7:
                    $devolucion3770 = 1504.74;
                    break;
                case 8:
                    $devolucion3770 = 1625.7;
                    break;
                case 9:
                    $devolucion3770 = 1867.62;
                    break;
                case 12:
                    $devolucion3770 = 3256.54;
                    break;
                case 14:
                    // OJO este se cargo el 26/08/2015
                    // en el mes 7 se liquido si este dato y Ayelen acomodo a mano
                    $devolucion3770 = 789.27;
                    break;
                case 16:
                    $devolucion3770 = 3009.48;
                    break;
                case 17:
                    $devolucion3770 = 2525.64;
                    break;
                case 18:
                    $devolucion3770 = 3179.35;
                    break;
                case 19:
                    $devolucion3770 = 3266.79;
                    break;
                case 25:
                    $devolucion3770 = 3332.59;
                    break;
                case 35:
                    $devolucion3770 = 3157.06;
                    break;
                case 62:
                    $devolucion3770 = 2965.43;
                    break;
            }

            if ($periodoGananciaFD == '2015-05-01' && $periodoGananciaFD == '2015-05-31') {
                $devolucionesAnteriores = $devolucionesAnteriores + $devolucion3770;
            }

            // *******************************************************************************************
            // Datos Tabla rangos Afip (Con Beneficios)
            // *******************************************************************************************

                    $sql = "SELECT  E.*
                            FROM    AfipGananciasEscalas E
                            INNER JOIN AfipGananciasEscalasPeriodos P ON E.AfipEscalaPeriodo = P.Id
                            WHERE   E.Desde/12*{$datos['mes']} < $MontoImponible
                            AND     E.Hasta/12*{$datos['mes']} >= $MontoImponible
                            AND     P.FechaDesde >= '".$datos['periodoFD']."'";

                    echo "--Datos Tabla rangos Afip (Con Beneficios) -----------------------------".PHP_EOL;
                    echo $sql.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;


                    $tablaAfip = $this->_db->fetchRow($sql);

                    //if (!$tablaAfip) throw new Rad_Db_Table_Exception("Falta ingresar la tabla de datos de ganancia para el periodo seleccionado.");

                    $limiteInferior     = $tablaAfip['Desde'] / 12 * $datos['mes'];
                    $Alicuota           = $tablaAfip['Alicuota'];
                    $CanonFijo          = $tablaAfip['CanonFijo'];

                    $montoIncremento    = (($MontoImponible - $limiteInferior) * ($Alicuota / 100))  + ($CanonFijo / 12 * $datos['mes']);
                    $montoAPAgar        = $montoIncremento + $pagosAnteriores + $devolucionesAnteriores;

            // *******************************************************************************************
            // Beneficio
            // *******************************************************************************************
/*
                    $sql = "SELECT  sum(MontoAcumulado)
                            FROM    PersonasGananciasLiquidaciones PGL
                            WHERE   PGL.Persona             = {$datos['idPersona']}
                            AND     PGL.GananciaMesPeriodo  = {$datos['mes']}
                            AND     PGL.GananciaAnioPeriodo = {$datos['anio']}
                            AND     PGL.GananciaConcepto in (41)
                            ";

                    echo "-- Beneficio MES -------------------------------------------------------".PHP_EOL;
                    echo $sql.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;

                    $beneficioM = $this->_db->fetchOne($sql);
                    if (!$beneficioM) $beneficioM = 0;
*/
            // *******************************************************************************************
            // Beneficio Acumulado a este mes
            // *******************************************************************************************
/*
                    $sql = "SELECT  sum(MontoAcumulado)
                            FROM    PersonasGananciasLiquidaciones PGL
                            WHERE   PGL.Persona             = {$datos['idPersona']}
                            AND     PGL.GananciaMesPeriodo  <= {$datos['mes']}
                            AND     PGL.GananciaAnioPeriodo = {$datos['anio']}
                            AND     PGL.GananciaConcepto in (41)
                            ";

                    echo "-- Beneficio Acumulado (incluye el mes actual) -------------------------".PHP_EOL;
                    echo $sql.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;

                    $beneficioAcumulado = $this->_db->fetchOne($sql);
                    if (!$beneficioAcumulado) $beneficioAcumulado = 0;
*/

            // *******************************************************************************************
            // Monto imponible sin beneficios (ojo 41 es el beneficio en las deducciones)
            // *******************************************************************************************

//                    $MontoImponibleSinBeneficios = $MontoImponible - abs($beneficioM);

                    // $MontoImponibleSinBeneficios = $MontoImponible + abs($beneficioAcumulado);
                    /*
                    $sql = "SELECT  sum(MontoAcumulado)
                            FROM    PersonasGananciasLiquidaciones PGL
                            WHERE   PGL.Persona             = {$datos['idPersona']}
                            AND     PGL.GananciaMesPeriodo  = {$datos['mes']}
                            AND     PGL.GananciaAnioPeriodo = {$datos['anio']}
                            AND     PGL.GananciaConcepto not in (34,42,43)
                            ";

                    echo "--Monto imponible ------------------------------------------------------".PHP_EOL;
                    echo $sql.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;

                    $MontoImponible = $this->_db->fetchOne($sql);
                    if (!$MontoImponible) $MontoImponible = 0;
                    */

            // *******************************************************************************************
            // Datos Tabla rangos Afip (Sin Beneficios)
            // *******************************************************************************************
/*
                    $sql = "SELECT  *
                            FROM    AfipGananciasEscalas
                            WHERE   Desde/12*{$datos['mes']} < $MontoImponibleSinBeneficios
                            AND     Hasta/12*{$datos['mes']} >= $MontoImponibleSinBeneficios";


                    echo "--Datos Tabla rangos Afip sin Beneficios -------------------------------".PHP_EOL;
                    echo $sql.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;


                    $tablaAfipSB = $this->_db->fetchRow($sql);

                    //if (!$tablaAfip) throw new Rad_Db_Table_Exception("Falta ingresar la tabla de datos de ganancia para el periodo seleccionado.");

                    $limiteInferiorSB   = $tablaAfipSB['Desde'] / 12 * $datos['mes'];
                    $AlicuotaSB         = $tablaAfipSB['Alicuota'];
                    $CanonFijoSB        = $tablaAfipSB['CanonFijo'];

                    $montoIncrementoSB  = (($MontoImponibleSinBeneficios - $limiteInferiorSB) * ($AlicuotaSB / 100)) + ($CanonFijoSB / 12 * $datos['mes']);
                    $montoAPagarSB      = $montoIncrementoSB + $pagosAnteriores - $devolucionesAnteriores;

                    $BeneficioEnSueldoPeriodo    = $montoAPagarSB - $montoAPagar;
                    $BeneficioEnSueldoAcumulado  = $montoIncremento - $montoIncrementoSB;
*/
            // *******************************************************************************************
            // inserto cuanto deberia pagar
            // *******************************************************************************************

                    $d = array(     'Persona'             => $datos['idPersona'],
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio']
                        );

                    // inserto el beneficio reflejado en el sueldo
                    /*
                    $d['GananciaConcepto']  = 42;
                    $d['Monto']             = - (abs($montoAPagarSB) - abs($montoAPAgar) - abs($this->getMontoAjusteDeduccionesAcumuladoMesAnterior($datos,$datos['mes'])));
                    $d['MontoAcumulado']    = - (abs($montoAPagarSB) - abs($montoAPAgar));
                    $this->insert($d);
                    */
                    // que no le descuente nada a los de rango 1
//                  $montoAPAgar = ($datos['rangoGanancias'] <> 1) ? $montoAPAgar : 0;

                    // inserto lo que deberia pagar este periodo
                    $d['GananciaConcepto']  = 43;
                    $d['Monto']             = -$montoAPAgar;

                    // pagosAnteriores          viene en negativo
                    // devolucionesAnteriores   viene en positivo
                    // montoAPAgar              esta en positivo
                    // asi que debe ser -$montoAPAgar + $devolucionesAnteriores + $pagosAnteriores para sacar el acumulado

                    $d['MontoAcumulado']    = -$montoAPAgar + $devolucionesAnteriores + $pagosAnteriores;
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
                    echo "devolucionesAnteriores:       ".$devolucionesAnteriores .PHP_EOL;
                    echo "pagosAnteriores:              ".$pagosAnteriores .PHP_EOL;
                    echo "montoAPAgar:                  ".$montoAPAgar .PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;

                    // que no le descuente nada a los de rango 1
                    // $montoAPAgar = ($datos['rangoGanancias'] <> 1) ? $montoAPAgar : 0;

                    if ($montoAPAgar) {

                        $M_LRD       = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles);
                        $M_Concepto  = Service_TableManager::get(Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles);

                        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                        // Ojo la ganancia viene con el signo cambiado
                        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                        if ($montoAPAgar > 0) {
                            // Se le debe retener el impuesto
                            $VarDetalle         = 500;
                            $conceptoGanancia   = 34;

                            // veo que el monto a pagar no supere el 35% del bruto del recibo.
                            if (!($datos['anio'] == 2016 && $datos['mes'] == 3))
                            {
                                $bruto          = $M_LRD->getSumBruto($datos['idRecibo']);
                                $topeBruto      = abs($bruto)*0.35;
                                $montoAPAgar    = (abs($montoAPAgar) > abs($topeBruto)) ? $topeBruto : $montoAPAgar;
                            }
                            /*                             
                            $bruto          = $M_LRD->getSumBruto($datos['idRecibo']);
                            $topeBruto      = -$bruto*0.35;
                            $montoAPAgar    = (abs($montoAPAgar) > abs($topeBruto)) ? $topeBruto : $montoAPAgar;
                            */
                        } else {
                            // debemos devolverle plata
                            $VarDetalle         = 484;
                            $conceptoGanancia   = 35;
                        }

                        $c = array(     'LiquidacionRecibo'   => $datos['idRecibo'],
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

                        // inserto el monto que realmente pago ... mas arriba se inserto el que DEBERIA pagar
                        $d['GananciaConcepto']  = $conceptoGanancia;
                        $d['Monto']             = -$montoAPAgar;

                        // pagosAnteriores          viene en negativo
                        // devolucionesAnteriores   viene en positivo
                        // montoAPAgar              esta en positivo
                        // asi que debe ser -$montoAPAgar + $devolucionesAnteriores + $pagosAnteriores para sacar el acumulado

                        $d['MontoAcumulado']    = -$montoAPAgar + $devolucionesAnteriores + $pagosAnteriores;
                        $this->insert($d);
                    }


        return 1;

        }
        else
        return 0;
    }

    /**
     * Ajusta las deducciones segun los rangos salariales tomados en 2013
     * Sueldo Bruto menor de 15000, entre 15000 y 25000 y mayores a 25000
     * Ojo solo ajusta las de los que tienen menos de 15000 para que no
     * tengan que pagar ganancias. Para eso hay que incrementar las deducciones
     * de forma tal que hagan que este mes se deba pagar 0 de ganancia, es decir
     * que no incremente el acumulado en ese mes.
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function getMontoAjusteDeducciones(&$datos,$mes) {

        $ajuste = 0;

        $sql = "SELECT  sum(Monto)
                FROM    PersonasGananciasLiquidaciones
                WHERE   Persona             = {$datos['idPersona']}
                AND     GananciaMesPeriodo  = $mes
                AND     GananciaAnioPeriodo = {$datos['anio']}
                AND     GananciaConcepto not in (41)
                ";

        $ajuste = $this->_db->fetchOne($sql);
        if ($ajuste < 0) $ajuste = 0;

        return $ajuste * (-1);
    }

    /**
     * Ajusta las deducciones segun los rangos salariales tomados en 2013
     * Sueldo Bruto menor de 15000, entre 15000 y 25000 y mayores a 25000
     * Ojo solo ajusta las de los que tienen menos de 15000 para que no
     * tengan que pagar ganancias. Para eso hay que incrementar las deducciones
     * de forma tal que hagan que este mes se deba pagar 0 de ganancia, es decir
     * que no incremente el acumulado en ese mes.
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function getMontoAjusteDeduccionesAcumuladoMesAnterior(&$datos,$mes = null) {

        $ajuste = 0;

        if ((int)$datos['mes'] > 1) {

            $sql = "SELECT  sum(Monto)
                    FROM    PersonasGananciasLiquidaciones
                    WHERE   Persona             = {$datos['idPersona']}
                    AND     GananciaMesPeriodo  < ".(int)$datos['mes'].
                "   AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaConcepto in (42)
                    ";

            $ajuste = $this->_db->fetchOne($sql);
            if ($ajuste < 0) $ajuste = 0;
        } else {
            $ajuste = 0;

        }

        return $ajuste * (-1);
    }

    /**
     * Borra los registros de una liquidacion de Ganancias
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function delGananciaLiquidada(&$datos) {

        $where = "          Persona             = {$datos['idPersona']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaMesPeriodo  = {$datos['mes']}
                    AND     Recibo              = {$datos['idRecibo']}
                ";
        $existe = $this->fetchRow($where);
        if ($existe) $this->delete($where);
    }

    /**
     * Recupero el rango de ganancias que tiene a partir del mes Septiembre de 2013
     * Ajusta las deducciones segun los rangos salariales tomados en 2013
     * Sueldo Bruto menor de 15000, entre 15000 y 25000 y mayores a 25000
     * Tomar el mas alto devengado entre los meses Enero y Agosto del 2013
     * OJO ... Devengado es decir que se ubieran cobrado en esos meses.
     *
     * @param   int     $idPersona   identificador de la tabla persona
     * @return  int
     */
    public function getRangoDeducciones($idPersona) {

        $modelo         = 57; // Base_Model_DbTable_Empleados
        $caracteristica = 15; // TramosGanancias
        $empleado       = $idPersona;

        $M_CV   = new Model_DbTable_CaracteristicasValores;
        $rango  = $M_CV->getValor($empleado, $caracteristica, $modelo);

        if (!$rango) $rango = 1;

        return $rango;
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

    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setIngresoBruto(&$datos) {

        $clausulaEsNoHabitual = ($datos['NoHabitual']) ? " AND V.NoHabitual is not null " : " AND V.NoHabitual is null ";

        // ---------------------------------------
        // Busco lo del periodo
        // ---------------------------------------

        $Monto              = 0;
        $MontoAcumulado     = 0;

        $sql = "    SELECT      sum(LRD.Monto) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    WHERE   LR.Ajuste   = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5)
                    AND     LRD.Monto   <> 0
                    AND     LR.Periodo  = {$datos['idPeriodo']}
                    AND     LR.Persona  = {$datos['idPersona']}
                    $clausulaEsNoHabitual
        ";

        /*
        echo '--Normal----------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;
        */

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;

        // ---------------------------------------
        // Si es no habitual reviso si supera el 20% de lo habitual para fraccionarlo
        // ---------------------------------------

        if ($datos['NoHabitual']) {

            $datos['MontoNoHabitual']   = $Monto;
            $VeintePorcientoHabitual    = $datos['MontoHabitual'] * 0.2;

            if ($Monto >= $VeintePorcientoHabitual && $datos['mes'] != 12 ) {
                // en este caso debo prorratear lo no habitual por los meses que quedan
                // he informar el monto ese solamente
                $Monto = $this->setProrrateoNoHabitual($datos);
            }
        }

        // ---------------------------------------
        // Sumo los montos prorrateados de meses anteriores al monto
        // Esto es generico y debo hacerlo siempre
        // ---------------------------------------

        $sql = "    SELECT  sum(PGP.Monto)
                    FROM    PersonasGananciasProrrateos PGP
                    WHERE   AnioGanancia        =   {$datos['anio']}
                    AND     MesDesde            <=  {$datos['mes']}
                    AND     MesHasta            >=  {$datos['mes']}
                    AND     LiquidacionRecibo   <>  {$datos['idRecibo']}
        ";

        $MontoProrrateado = $this->_db->fetchOne($sql);
        $MontoProrrateado = ($MontoProrrateado) ? $MontoProrrateado : 0;

        // Sumo los dos.... lo de este mes y lo prorrateado de otros meses que se debe abonar este mes
        $Monto = $Monto + $MontoProrrateado;

        // ---------------------------------------
        // Busco lo acumulado
        // ---------------------------------------

        // completo el filtro para que no me traiga los conceptos variables cuando se prorratearon en el caso que este sumando no habituales
        $clausulaEsNoHabitual .= ($datos['NoHabitual']) ? " AND LR.Id not in (Select LiquidacionRecibo from PersonasGananciasProrrateos) " : "";

        $periodoGananciaFD  = $datos['anio']."-01-01";
        $periodoGananciaFH  = $datos['anio']."-31-12";

        $sql = "    SELECT      sum(LRD.Monto) as MontoAcumulado
                    FROM        LiquidacionesRecibosDetalles    LRD
                    INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                    INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                    INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                    INNER JOIN  LiquidacionesPeriodos           LP  ON  LP.Id   = LR.Periodo
                    WHERE   LR.Ajuste       = 0
                    AND     LP.FechaDesde   >=  '$periodoGananciaFD'
                    AND     LP.FechaDesde   <   '{$datos['periodoFH']}'
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5)
                    AND     LRD.Monto       <> 0
                    AND     LR.Persona      = {$datos['idPersona']}
                    $clausulaEsNoHabitual
        ";

        echo '--Acumulado-------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

        $MontoAcumulado = $this->_db->fetchOne($sql);
        $MontoAcumulado = ($MontoAcumulado) ? $MontoAcumulado : 0;

        // ---------------------------------------
        // Agrego al acumulado los montos prorrateados de meses anteriores
        // Ojo si estoy en el mes 1 tengo acumulado
        // ---------------------------------------

        $MontoProrrateadoAcumulado = 0;


            for ($mes = 1; $mes <= $datos['mes']; $mes++) {

                $sql = "    SELECT  sum(PGP.Monto)
                            FROM    PersonasGananciasProrrateos PGP
                            INNER JOIN LiquidacionesRecibos LR on LR.Id = PGP.LiquidacionRecibo
                            WHERE   PGP.AnioGanancia        =   {$datos['anio']}
                            AND     PGP.MesDesde            <=  $mes
                            AND     PGP.MesHasta            >=  $mes
                            AND     LR.Persona              =   {$datos['idPersona']}
                ";

                $MPA = $this->_db->fetchOne($sql);
                $MontoProrrateadoAcumulado += ($MPA) ? $MPA : 0;
            }


        // Sumo los dos
        $MontoAcumulado = $MontoAcumulado + $MontoProrrateadoAcumulado;

        // Inserto el registro
        $d = array(     'Persona'             => $datos['idPersona'],
                        'Recibo'              => $datos['idRecibo'],
                        'Liquidacion'         => $datos['idLiquidacion'],
                        'GananciaConcepto'    => $datos['Concepto'],
                        'Monto'               => $Monto,
                        'MontoAcumulado'      => $MontoAcumulado,
                        'GananciaMesPeriodo'  => $datos['mes'],
                        'GananciaAnioPeriodo' => $datos['anio']

            );
        $this->insert($d);

        return $Monto;

    }





    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesPersonales(&$datos) {

        $deducciones = array();

        // Si el rango es 2 aumento un 20% las deducciones. (Ojo los rango 1 aunque no paguen son beneficiados con el 20% tambien)
        // Solo los casos 1 y 2 que provienen de las tablas de AFIP
        // No lo hago para los montos fijos pagados como ser seguros

        $incremento = ($datos['rangoGanancias'] == 2 || $datos['rangoGanancias'] == 1) ? 1.2 : 1;

        switch ($datos['rangoGanancias2015']) {
            case 1:
                $incremento2015 = 1.5;
                break;
            case 2:
                $incremento2015 = 1.44;
                break;
            case 3:
                $incremento2015 = 1.38;
                break;
            case 4:
                $incremento2015 = 1.32;
                break;
            case 5:
                $incremento2015 = 1.29;
                break;
            case 6:
                $incremento2015 = 1.26;
                break;
            case 7:
                // Rango 1
                $incremento2015 = 1.2;
                break;
            case 8:
                // Rango 3
                $incremento2015 = 1;
                break;
        }

        // Parche 2016
        $incremento     = 1;
        $incremento2015 = 1;

        // $incrementoAplicar Se setea mas adelante con el valor de incremento para los meses posteriores a Agosto 2013
        $incrementoAplicar = 1;

        // Debo recorre mes a mes cuanto es el monto que le corresponde
        for ($mes = 1; $mes <= $datos['mes']; $mes++) {

            // Debo completar el array ya que existen deducciones que aparecen un solo mes y despues no las sume mas tarde
            if ($mes > 1) {
                /*
                $arr = $deducciones[$mes-1];
                foreach ($arr as $C_Concepto => $M_Monto) {
                    $deducciones[$mes][$C_Concepto] = $M_Monto;
                }
                */

                $deducciones[$mes] = $deducciones[$mes-1];
                //echo '## DEDUCCIONES #########################################################'.PHP_EOL;
                //// echo print_r($deducciones[$mes], true).PHP_EOL;
                //echo '########################################################################'.PHP_EOL;

            }

            $periodoFD = $datos['anio']."-".str_pad($mes, 2,"0",STR_PAD_LEFT)."-01";

            if ($periodoFD > '2013-08-31') $incrementoAplicar = $incremento;
            if ($periodoFD > '2014-12-31') $incrementoAplicar = $incremento2015;

            $R  = array();

            $sql = "    SELECT      GC.Id               as Concepto,
                                    (0 - AGDD.Monto)    as Monto,
                                    (0 - PGD.Monto)     as MontoParticular,
                                    AGD.TipoDeduccion   as Tipo,
                                    PGD.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesInicioImputacion,
                                    null                as TopeAnual,
                                    1                   as Control
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesDetalles    AGDD    ON AGD.Id   =   AGDD.Deduccion
                        INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP    ON AGDP.Id  =   AGDD.Periodo
                        WHERE       AGDP.FechaDesde                                 <=  '$periodoFD'
                        AND         ifnull(AGDP.FechaHasta,'2199-01-01')            >   '$periodoFD'
                        AND         PGD.Persona                                     =   {$datos['idPersona']}
                        AND         PGD.AnioGanancia                                =   {$datos['anio']}
                        AND         PGD.MesDesde                                    <=  $mes
                        AND         PGD.MesHasta                                    >=  $mes
                        /* Agregado por pedido de Ayelen para que tome solo lo prestnado al empleador en el 572 */
                        AND         PGD.Empresa                                     =   {$datos['idEmpresa']}
                        /* Agregado por pedido de Ayelen para que indiquen desde cuando imputar */
                        AND         ifnull(PGD.MesInicioImputacion,1)               <   $mes
                        /* Quito los que se manejan por topes 2015-07-13*/
                        AND AGD.Id NOT IN (SELECT  T1.AfipGananciaDeduccion FROM   AfipGananciasDeduccionesTopes T1)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;

            $sql = "    SELECT      GC.Id                       as Concepto,
                                    if ($mes <= PGD.MesHasta,
                                        0-(($mes-PGD.MesDesde+1)*AGDD.Monto),
                                        0-((PGD.MesHasta-PGD.MesDesde+1)*AGDD.Monto)
                                        )                       as Monto,
                                    --(0 - AGDD.Monto)            as Monto,
                                    if ($mes <= PGD.MesHasta,
                                        0-(($mes-PGD.MesDesde+1)*PGD.Monto),
                                        0-((PGD.MesHasta-PGD.MesDesde+1)*PGD.Monto)
                                        )                       as MontoParticular,
                                    --(0 - PGD.Monto)             as MontoParticular,
                                    AGD.TipoDeduccion           as Tipo,
                                    PGD.Id                      as BasuraParaQueNoFalleSQL,
                                    PGD.MesDesde                as MesInicio,
                                    PGD.MesHasta                as MesFinal,
                                    PGD.MesInicioImputacion     as MesInicioImputacion,
                                    null                as TopeAnual,
                                    2                   as Control
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesDetalles    AGDD    ON AGD.Id   =   AGDD.Deduccion
                        INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP    ON AGDP.Id  =   AGDD.Periodo
                        WHERE       AGDP.FechaDesde                                 <=  '$periodoFD'
                        AND         ifnull(AGDP.FechaHasta,'2199-01-01')            >   '$periodoFD'
                        AND         PGD.Persona                                     =   {$datos['idPersona']}
                        AND         PGD.AnioGanancia                                =   {$datos['anio']}
                        AND         PGD.MesDesde                                    <=  $mes
                        /* Agregado por pedido de Ayelen para que tome solo lo prestnado al empleador en el 572 */
                        AND         PGD.Empresa                                     =   {$datos['idEmpresa']}                        
                        -- AND         PGD.MesHasta                                 >=  $mes
                        /* Agregado por pedido de Ayelen para que indiquen desde cuando imputar */
                        AND         ifnull(PGD.MesInicioImputacion,1)               =  $mes
                        -- AND         $mes <> 1
                        /* Quito los que se manejan por topes 2015-07-13*/
                        AND AGD.Id NOT IN (SELECT  T1.AfipGananciaDeduccion FROM   AfipGananciasDeduccionesTopes T1)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;

            $sql = "    SELECT      GC.Id               as Concepto,
                                    0                   as Monto,
                                    0 - if (sum(PGD.Monto) < ifnull(AGDT.TopeMensual,999999),
                                            sum(PGD.Monto),
                                            AGDT.TopeMensual
                                            )           as MontoParticular,
                                    AGD.TipoDeduccion   as Tipo,
                                    PGD.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesInicioImputacion,
                                    0-AGDT.TopeAnual    as TopeAnual,
                                    3                   as Control
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesTopes       AGDT    ON AGD.Id   =   AGDT.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP    ON AGDP.Id  =   AGDT.Periodo
                        WHERE       AGDP.FechaDesde                                 <=  '$periodoFD'
                        AND         ifnull(AGDP.FechaHasta,'2199-01-01')            >   '$periodoFD'
                        AND         PGD.Persona                                     =   {$datos['idPersona']}
                        AND         PGD.AnioGanancia                                =   {$datos['anio']}
                        AND         PGD.MesDesde                                    <=  $mes
                        AND         PGD.MesHasta                                    >=  $mes
                        /* Agregado por pedido de Ayelen para que tome solo lo prestnado al empleador en el 572 */
                        AND         PGD.Empresa                                     =   {$datos['idEmpresa']}
                        /* Agregado por pedido de Ayelen para que indiquen desde cuando imputar */
                        AND         ifnull(PGD.MesInicioImputacion,1)               <   $mes
                        /* Filtro para que no duplique cuando hay varios periodos de topes */
                        -- AND         AGDT.Id in ( SELECT AGDT1.Id FROM AfipGananciasDeduccionesTopes AGDT1 WHERE AGDT1.AnioDesde <= {$datos['anio']} and AGDT1.MesDesde <= $mes ORDER BY AGDT1.AnioDesde desc, AGDT1.MesDesde desc LIMIT 1)
                        AND         AGDT.AnioDesde = {$datos['anio']}
                        /* Filtro solo los que se manejan por topes 2015-07-13*/
                        AND AGD.Id IN (SELECT  T1.AfipGananciaDeduccion FROM   AfipGananciasDeduccionesTopes T1)
                        group by GC.Id
            ";

            echo "=-= 1 =-=".PHP_EOL;
            echo PHP_EOL.$sql.PHP_EOL;
            echo "=-= 1 =-=".PHP_EOL;


            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;

            $sql = "    SELECT  Concepto, Monto, SUM(MontoParticular) as MontoParticular, Tipo, 
                                BasuraParaQueNoFalleSQL, MesInicio, MesFinal, MesInicioImputacion,
                                TopeAnual, 
                                Control 
                        FROM (
                                SELECT      GC.Id                       as Concepto,
                                            0                           as Monto,
                                            if ($mes <= PGD.MesHasta,
                                                if (($mes-PGD.MesDesde+1)* (PGD.Monto) < ($mes-PGD.MesDesde+1)*ifnull(AGDT.TopeMensual,999999),
                                                    0-($mes-PGD.MesDesde+1)* (PGD.Monto),
                                                    0-($mes-PGD.MesDesde+1)* AGDT.TopeMensual
                                                ),
                                                if((PGD.MesHasta-PGD.MesDesde+1)* (PGD.Monto) < (PGD.MesHasta-PGD.MesDesde+1)*ifnull(AGDT.TopeMensual,999999),
                                                    0-(PGD.MesHasta-PGD.MesDesde+1)* (PGD.Monto),
                                                    0-(PGD.MesHasta-PGD.MesDesde+1)* AGDT.TopeMensual
                                                )
                                            )                           as MontoParticular,
                                            AGD.TipoDeduccion           as Tipo,
                                            PGD.Id                      as BasuraParaQueNoFalleSQL,
                                            PGD.MesDesde                as MesInicio,
                                            PGD.MesHasta                as MesFinal,
                                            PGD.MesInicioImputacion     as MesInicioImputacion,
                                            0-AGDT.TopeAnual            as TopeAnual,
                                            4                   as Control
                                FROM        PersonasGananciasDeducciones        PGD
                                INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                                INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                                INNER JOIN  AfipGananciasDeduccionesTopes       AGDT    ON AGD.Id   =   AGDT.AfipGananciaDeduccion
                                INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP    ON AGDP.Id  =   AGDT.Periodo
                                WHERE       AGDP.FechaDesde                                 <=  '$periodoFD'
                                AND         ifnull(AGDP.FechaHasta,'2199-01-01')            >   '$periodoFD'
                                AND         PGD.Persona                                     =   {$datos['idPersona']}
                                AND         PGD.AnioGanancia                                =   {$datos['anio']}
                                AND         PGD.MesDesde                                    <=  $mes
                                /* Agregado por pedido de Ayelen para que tome solo lo prestnado al empleador en el 572 */
                                AND         PGD.Empresa                                     =   {$datos['idEmpresa']}
                                -- AND         PGD.MesHasta                                 >=  $mes
                                /* Agregado por pedido de Ayelen para que indiquen desde cuando imputar */
                                AND         ifnull(PGD.MesInicioImputacion,1)               =  $mes
                                -- AND         $mes <> 1
                                /* Filtro para que no duplique cuando hay varios periodos de topes */
                                -- AND         AGDT.Id in ( SELECT AGDT1.Id FROM AfipGananciasDeduccionesTopes AGDT1 WHERE AGDT1.AnioDesde <= {$datos['anio']} and AGDT1.MesDesde <= $mes ORDER BY AnioDesde desc, MesDesde desc LIMIT 1)
                                AND         AGDT.AnioDesde = {$datos['anio']}
                                /* Quito los que se manejan por topes 2015-07-13*/
                                AND AGD.Id IN (SELECT  T1.AfipGananciaDeduccion FROM   AfipGananciasDeduccionesTopes T1)
                                -- group by GC.Id
                        ) AS X
            ";

            echo "=-= 2 =-=".PHP_EOL;
            echo PHP_EOL.$sql.PHP_EOL;
            echo "=-= 2 =-=".PHP_EOL;


            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;



            $sql = "    SELECT      GC.Id               as Concepto,
                                    (0)                 as Monto,
                                    (0 - PGD.Monto)     as MontoParticular,
                                    AGD.TipoDeduccion   as Tipo,
                                    PGD.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesInicioImputacion,
                                    null                as TopeAnual,
                                    5                   as Control
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        WHERE       PGD.Persona                                     =   {$datos['idPersona']}
                        AND         PGD.AnioGanancia                                =   {$datos['anio']}
                        AND         PGD.MesDesde                                    <=  $mes
                        AND         PGD.MesHasta                                    >=  $mes
                        /* Agregado por pedido de Ayelen para que tome solo lo prestnado al empleador en el 572 */
                        AND         PGD.Empresa                                     =   {$datos['idEmpresa']}
                        /* Agregado por paedido de Ayelen para que indiquen desde cuando imputar */
                        AND         ifnull(PGD.MesInicioImputacion,1)               <   $mes
                        -- AND         ifnull(PGD.FechaInicioImputacion,'2013-01-01')  <  '$periodoFD'
                        AND     AGD.Id NOT IN (Select AGDD1.Deduccion From AfipGananciasDeduccionesDetalles    AGDD1)
                        AND     AGD.Id NOT IN (SELECT T1.AfipGananciaDeduccion FROM AfipGananciasDeduccionesTopes T1)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;

            $sql = "    SELECT      GC.Id                       as Concepto,
                                    (0)                         as Monto,
                                    if ($mes <= PGD.MesHasta,
                                        0-(($mes-PGD.MesDesde+1)*PGD.Monto),
                                        0-((PGD.MesHasta-PGD.MesDesde+1)*PGD.Monto)
                                        )                       as MontoParticular,
                                    -- (0 - PGD.Monto)     as MontoParticular,
                                    AGD.TipoDeduccion           as Tipo,
                                    PGD.Id                      as BasuraParaQueNoFalleSQL,
                                    PGD.MesDesde                as MesInicio,
                                    PGD.MesHasta                as MesFinal,
                                    PGD.MesInicioImputacion     as MesInicioImputacion,
                                    null                        as TopeAnual,
                                    6                           as Control
                        FROM        PersonasGananciasDeducciones        PGD
                        INNER JOIN  GananciasConceptos                  GC      ON GC.Id    =   PGD.GananciaDeduccion
                        INNER JOIN  AfipGananciasDeducciones            AGD     ON AGD.Id   =   GC.AfipGananciaDeduccion
                        WHERE       PGD.Persona                                     =   {$datos['idPersona']}
                        AND         PGD.AnioGanancia                                =   {$datos['anio']}
                        AND         PGD.MesDesde                                    <=  $mes
                        /* Agregado por pedido de Ayelen para que tome solo lo prestnado al empleador en el 572 */
                        AND         PGD.Empresa                                     =   {$datos['idEmpresa']}
                        -- AND         PGD.MesHasta                                    >=  $mes
                        /* Agregado por paedido de Ayelen para que indiquen desde cuando imputar */
                        AND         ifnull(PGD.MesInicioImputacion,1)               =  $mes
                        -- AND         ifnull(PGD.FechaInicioImputacion,'2013-01-01')  <  '$periodoFD'
                        AND         AGD.Id NOT in (Select AGDD1.Deduccion From AfipGananciasDeduccionesDetalles    AGDD1)
                        AND         AGD.Id NOT IN (SELECT T1.AfipGananciaDeduccion FROM AfipGananciasDeduccionesTopes T1)
                        AND         $mes <> 1
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;

            $sql = "    SELECT      GC1.Id               as Concepto,
                                    (0 - AGDD1.Monto)    as Monto,
                                    0                    as MontoParticular,
                                    AGD1.TipoDeduccion   as Tipo,
                                    AGD1.Id              as BasuraParaQueNoFalleSQL,
                                    0                   as MesInicio,
                                    0                   as MesFinal,
                                    0                   as MesPresentacion,
                                    null                as TopeAnual,
                                    7                   as Control
                        FROM        GananciasConceptos                  GC1
                        INNER JOIN  AfipGananciasDeducciones            AGD1     ON AGD1.Id   =   GC1.AfipGananciaDeduccion
                        INNER JOIN  AfipGananciasDeduccionesDetalles    AGDD1    ON AGD1.Id   =   AGDD1.Deduccion
                        INNER JOIN  AfipGananciasDeduccionesPeriodos    AGDP1    ON AGDP1.Id  =   AGDD1.Periodo
                        WHERE       AGDP1.FechaDesde                         <=  '$periodoFD'
                        AND         ifnull(AGDP1.FechaHasta,'2199-01-01')    >   '$periodoFD'
                        AND         AGD1.Id in (1,7)
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;

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
                            if ($row['TopeAnual'] && abs($row['TopeAnual']) < abs($row['MontoParticular'])) $row['MontoParticular'] = $row['TopeAnual'];
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
                    if ($row['Concepto']) {
                        if ($mes == $datos['mes']) $ValorMes[$row['Concepto']] = $MontoDeduccion;
                   
                        // Acumulo
                        // $deducciones[$mes][$row['Concepto']]    = $deducciones[$mes-1][$row['Concepto']] + $MontoDeduccion;
                        // Ahora sumo con el mismo mes por que mas arriba complete el mes con los valores del mes anterior
                        $deducciones[$mes][$row['Concepto']]    = $deducciones[$mes][$row['Concepto']] + $MontoDeduccion;
                    }
                    //echo "--Mes $mes--------------------------------------------------------------".PHP_EOL;
                    //echo $mes.PHP_EOL.$row['Concepto'].PHP_EOL.'deducciones'.$deducciones[$mes][$row['Concepto']].PHP_EOL;
                    //echo '------------------------------------------------------------------------'.PHP_EOL;

                    /* Pasado mas abajo
                    if ($datos['rangoGanancias'] == 1 && $row['Concepto'] == 40 && ($datos['anio']  >= 2014 || ($datos['anio'] = 2013 && $mes > 8))) {
                        $m = 0;
                        $m = $this->getMontoAjusteDeducciones($datos,$mes);
                        $deducciones[$mes][41]    = $deducciones[$mes-1][41] + $m;
                    }
                    */
                    //if ($mes == (int)$datos['mes']) {

                        // inserto
                        /*
                        $d = array(     'Persona'             => $datos['idPersona'],
                                        'Liquidacion'         => $datos['idLiquidacion'],
                                        'GananciaConcepto'    => $row['Concepto'],
                                        'Monto'               => $MontoDeduccion,
                                        'GananciaMesPeriodo'  => $mes,
                                        'GananciaAnioPeriodo' => $datos['anio'],
                                        'MontoAcumulado'      => $deducciones[$mes][$row['Concepto']]
                            );
                        $this->insert($d);
                        */
                        // Ajusto a aquellos que no tienen que pagar (Rango 1) ... Como 40 es el ultimo concepto que se deduce
                        // pregunto por el en el if para saber que no sigue.

                        // if ($datos['rangoGanancias'] == 1 && $row['Concepto'] == 40 && $datos['anio']  >= 2013 && $mes > 8){

                        /*
                        if ( $row['Concepto'] == 40 && ($datos['anio']  >= 2014 || ($datos['anio'] == 2013 && $mes > 8))) {

                            switch ($datos['rangoGanancias']) {
                                 case 1:
                                    $m = $this->getMontoAjusteDeducciones($datos,$mes);
                                    //$m = $MontoDeduccionSinBeneficios - $MontoDeduccion;
                                    break;
                                 case 2:
                                    $m = $MontoDeduccionSinBeneficios - $MontoDeduccion;
                                    break;
                                 case 2:
                                    $m = 0;
                                    break;
                            }
                            $deducciones[$mes][41]    = $deducciones[$mes][41] + $m;
                            // Inserto el ajuste para el beneficio... ojo siempre es negativo el beneficio
                            $d['GananciaConcepto']  = 41;
                            $d['Monto']             = -$m;
                            $d['MontoAcumulado']    = -$deducciones[$mes][41];
                            $this->insert($d);

                            $m = 0;
                        }
                        */

                    //}
                    // Blanqueo
                    $MontoDeduccion = 0;
                }
            }
        }

        // grabo

        if ($deducciones) {

            echo '===---===deducciones'.PHP_EOL;
            echo print_r($deducciones, true).PHP_EOL;

            foreach ($deducciones as $Dmes => $arrValores){

                if ($Dmes == $datos['mes']) {
                    foreach ($arrValores as $Dconcepto => $Dmonto) {

                        $M  = $ValorMes[$Dconcepto];
                        $MA = $Dmonto;
                        $d = array( 'Persona'             => $datos['idPersona'],
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaConcepto'    => $Dconcepto,
                                    'Monto'               => $M,
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio'],
                                    'MontoAcumulado'      => $MA
                        );
                        $this->insert($d);
                    }
                }
            }
        }

        // parche 2016/12 para SAC (Parche UNO para el calculo del SAC)
        if ($datos['tipoDeLiquidacion'] == 2 && $datos['mes'] == 12 && $datos['anio'] == 2016) {
            // veo el maximo sueldo bruto para ver si hay que incrementar la deduccion
            $incrementaRetencion = true;
            
            for ($mesX = 7; $mesX <= 12; $mesX++) {
                
                $SueldoBrutoTotal = $SueldoBruto = $SueldoBrutoPluri = 0;

                $sql = "SELECT  ifnull(SUM(LRD.Monto),0) as Monto
                        FROM    LiquidacionesRecibosDetalles LRD
                        INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                        INNER JOIN Variables V              on V.Id  = VD.Variable
                        INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                        INNER JOIN Liquidaciones L          on L.Id = LR.Liquidacion
                        INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                        INNER JOIN Servicios S              on S.Id  = LR.Servicio
                        WHERE   V.TipoDeConceptoLiquidacion    in (1,2,3,5) -- 1:Remunerativos 2:Rem.Agrupados 3:No Remunerativos 5:No Rem.Agrupados
                        AND     LR.Ajuste = (
                                SELECT   MAX(LR1.Ajuste)
                                FROM    LiquidacionesRecibos LR1
                                WHERE   LR1.Periodo  = (SELECT  LP1.Id
                                                        FROM    LiquidacionesPeriodos LP1
                                                        WHERE   LP1.Anio = 2016
                                                        AND     LP1.Valor = $mesX)
                                AND     LR1.Persona  = {$datos['idPersona']}
                                AND     LR1.Servicio = LR.Servicio
                        )
                        AND     ifnull(V.EsSAC,99)  <> 1
                        AND     LR.Persona  = {$datos['idPersona']}
                        -- AND     LR.Servicio = {$servicio->Id} /* no poner para que tome cuando cambia de servicio */
                        AND     LP.Anio     = 2016
                        AND     LP.Valor    = $mesX
                        AND     L.Empresa   = {$datos['idEmpresa']}
                        -- AND     L.Empresa   = 1
                        AND     L.TipoDeLiquidacion = 1
                ";

                $sqlPluri = "   SELECT  ifnull(RemuneracionBrutaTotal,0)            as RemuneracionBrutaTotal
                                FROM    PersonasGananciasPluriempleoDetalle PPD
                                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                                WHERE   DATE_FORMAT(PPD.FechaDeLiquidacion, '%c')   = $mesX
                                AND     PGP.Persona                                 = {$datos['idPersona']}
                ";

                $SueldoBruto        = $this->_db->fetchOne($sql);
                $SueldoBrutoPluri   = $this->_db->fetchOne($sqlPluri);

                $SueldoBrutoTotal   =  $SueldoBruto + $SueldoBrutoPluri;

                if ($SueldoBrutoTotal >= 55000) {
                    $incrementaRetencion = false;
                }
            }

            if ($incrementaRetencion) {
                $d = array( 'Persona'             => $datos['idPersona'],
                            'Recibo'              => $datos['idRecibo'],
                            'Liquidacion'         => $datos['idLiquidacion'],
                            'GananciaConcepto'    => 52,
                            'Monto'               => -15000,
                            'GananciaMesPeriodo'  => $datos['mes'],
                            'GananciaAnioPeriodo' => $datos['anio'],
                            'MontoAcumulado'      => -15000
                );
                $this->insert($d);                
            }
        }
        // fin parche 2016/12 para SAC


        // parche 2016/12 para SAC -- Parche DOS para el calculo posterior del recibo comun con la modificacion de la nueva resolucion
        if ($datos['tipoDeLiquidacion'] == 1 && $datos['mes'] == 12 && $datos['anio'] == 2016) {
            // veo el maximo sueldo bruto para ver si hay que incrementar la deduccion
            $incrementaRetencion = true;
            $NetoSAC = 0;
            
            for ($mesX = 7; $mesX <= 12; $mesX++) {
                
                $SueldoBrutoTotal = $SueldoBruto = $SueldoBrutoPluri = 0;

                $sql = "SELECT  ifnull(SUM(LRD.Monto),0) as Monto
                        FROM    LiquidacionesRecibosDetalles LRD
                        INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                        INNER JOIN Variables V              on V.Id  = VD.Variable
                        INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                        INNER JOIN Liquidaciones L          on L.Id = LR.Liquidacion
                        INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                        INNER JOIN Servicios S              on S.Id  = LR.Servicio
                        WHERE   V.TipoDeConceptoLiquidacion    in (1,2,3,5) -- 1:Remunerativos 2:Rem.Agrupados 3:No Remunerativos 5:No Rem.Agrupados
                        AND     LR.Ajuste = (
                                SELECT   MAX(LR1.Ajuste)
                                FROM    LiquidacionesRecibos LR1
                                WHERE   LR1.Periodo  = (SELECT  LP1.Id
                                                        FROM    LiquidacionesPeriodos LP1
                                                        WHERE   LP1.Anio = 2016
                                                        AND     LP1.Valor = $mesX)
                                AND     LR1.Persona  = {$datos['idPersona']}
                                AND     LR1.Servicio = LR.Servicio
                        )
                        AND     ifnull(V.EsSAC,99)  <> 1
                        AND     LR.Persona  = {$datos['idPersona']}
                        -- AND     LR.Servicio = {$servicio->Id} /* no poner para que tome cuando cambia de servicio */
                        AND     LP.Anio     = 2016
                        AND     LP.Valor    = $mesX
                        AND     L.Empresa   = {$datos['idEmpresa']}
                        -- AND     L.Empresa   = 1
                        AND     L.TipoDeLiquidacion = 1
                ";

                $sqlPluri = "   SELECT  ifnull(RemuneracionBrutaTotal,0)            as RemuneracionBrutaTotal
                                FROM    PersonasGananciasPluriempleoDetalle PPD
                                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                                WHERE   DATE_FORMAT(PPD.FechaDeLiquidacion, '%c')   = $mesX
                                AND     PGP.Persona                                 = {$datos['idPersona']}
                ";

                $SueldoBruto        = $this->_db->fetchOne($sql);
                $SueldoBrutoPluri   = $this->_db->fetchOne($sqlPluri);

                $SueldoBrutoTotal   =  $SueldoBruto + $SueldoBrutoPluri;

                if ($SueldoBrutoTotal >= 55000) {
                    $incrementaRetencion = false;
                }
            }

            if ($incrementaRetencion) {

                $sqlNetoSAC = " SELECT  ifnull(SUM(LRD.Monto),0) as Monto
                            FROM    LiquidacionesRecibosDetalles LRD
                            INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                            INNER JOIN Variables V              on V.Id  = VD.Variable
                            INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                            INNER JOIN Liquidaciones L          on L.Id = LR.Liquidacion
                            INNER JOIN LiquidacionesPeriodos LP on LP.Id = LR.Periodo
                            INNER JOIN Servicios S              on S.Id  = LR.Servicio
                            WHERE   V.Id not in (95,97,118) -- 95 pago ganancia, 97 devolucion ganancia y 118 redondeo
                            AND     LR.Persona  = {$datos['idPersona']}
                            -- AND     LR.Servicio = {$servicio->Id} /* no poner para que tome cuando cambia de servicio */
                            AND     LP.Anio     = 2016
                            AND     LP.Valor    = 12
                            AND     L.Empresa   = {$datos['idEmpresa']}
                            AND     L.TipoDeLiquidacion = 2
                ";
                $NetoSAC = $this->_db->fetchOne($sqlNetoSAC);
                if ($NetoSAC > 15000) $NetoSAC = 15000;

                $d = array( 'Persona'             => $datos['idPersona'],
                            'Recibo'              => $datos['idRecibo'],
                            'Liquidacion'         => $datos['idLiquidacion'],
                            'GananciaConcepto'    => 52,
                            'Monto'               => -$NetoSAC,
                            'GananciaMesPeriodo'  => $datos['mes'],
                            'GananciaAnioPeriodo' => $datos['anio'],
                            'MontoAcumulado'      => -$NetoSAC
                );
                $this->insert($d);                
            }
        }


        // fin parche 2016/12 para recibo normal
    


    }


    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesRecibosPropios(&$datos) {

        if ($datos['idRecibo']) {

			if ( $this->gananciaNormal($datos)) {
	            // Escribo el acumulado de meses anteriores ... paso a 47 la suma de los 46 anteriores
	            $sql = "    SELECT  0 - sum(PGL.Monto)
	                        FROM    PersonasGananciasLiquidaciones PGL
                            INNER JOIN Liquidaciones L on L.Id = PGL.Liquidacion
	                        WHERE   PGL.GananciaConcepto    = 46
	                        AND     PGL.GananciaMesPeriodo  < {$datos['mes']}
	                        AND     PGL.GananciaAnioPeriodo = {$datos['anio']}
	                        AND     PGL.Persona             = {$datos['idPersona']}
                            AND     L.Empresa               = {$datos['idEmpresa']}
	            ";
	            $M = $this->_db->fetchOne($sql);

	            if ($M) {
	                // Escribo el registro en Ganancias
	                $d = array(     'Persona'             => $datos['idPersona'],
	                                'Recibo'              => $datos['idRecibo'],
	                                'Liquidacion'         => $datos['idLiquidacion'],
	                                'GananciaConcepto'    => 47,
	                                'Monto'               => 0,
	                                'GananciaMesPeriodo'  => $datos['mes'],
	                                'GananciaAnioPeriodo' => $datos['anio'],
	                                'MontoAcumulado'      => $M
	                    );
	                $this->insert($d);
	            }
	        }
            // Sigo con el calculo del mes en curso
            $periodoGananciaFD  = $datos['anio']."-01-01";

            $sql = "SELECT      GC2.Id as Concepto,
                                ifnull(sum(LRD2.Monto),0) as MontoAcumulado,
                                (
                                SELECT      ifnull(sum(LRD.Monto),0) as Monto
                                FROM        LiquidacionesRecibosDetalles    LRD
                                INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                                INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                                INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                                INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                                INNER JOIN  VariablesTiposDeConceptos       VTC ON  VTC.Id  = V.TipoDeConcepto
                                INNER JOIN  GananciasConceptos              GC  ON  GC.Id   = VTC.GananciaConcepto
                                WHERE   GC.GananciaConceptoTipo not in (6,7,8,9)
                                AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
                                AND     L.TipoDeLiquidacion in (1,2,3)
                                AND     LR.Ajuste   = 0
                                AND     LRD.Monto   <> 0
                                AND     GC2.Id      = GC.Id
                                AND     GC.Id       not in (46,47)
                                AND     LR.Periodo  = {$datos['idPeriodo']}
                                AND     LR.Persona  = {$datos['idPersona']}
                                AND     L.Empresa   = {$datos['idEmpresa']}
                                GROUP BY GC.Id
                                ) as Monto
                    FROM        LiquidacionesRecibosDetalles    LRD2
                    INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                    INNER JOIN  Liquidaciones                   L2   ON  L2.Id    = LR2.Liquidacion
                    INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                    INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                    INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                    INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                    INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                    WHERE   GC2.GananciaConceptoTipo not in (6,7,8,9)
                    AND     ifnull(V2.NoCuentaParaGanancia,0) <> 1
                    AND     L2.TipoDeLiquidacion in (1,2,3)
                    AND     LR2.Ajuste       = 0
                    AND     LRD2.Monto       <> 0
                    AND     GC2.Id           not in (46,47)
                    AND     LP2.FechaDesde   >='$periodoGananciaFD'
                    AND     LP2.FechaDesde   < '{$datos['periodoFH']}'
                    AND     LR2.Persona      = {$datos['idPersona']}
                    AND     L2.Empresa       = {$datos['idEmpresa']}
                    GROUP BY GC2.Id
            ";

        echo PHP_EOL;
        echo '--Tiene Recibo----------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;

            $R = $this->_db->fetchAll($sql);

            // Si tiene deducciones
            if ($R) {

                $ahora = date('Y-m-d H:i:s');
                $MAT   = 0; // Monto acumulado total del mes (solo del mes)

                // Si se prorrateo debo quitar la parte de las deducciones que faltan y pasarlas a otro mes
                if ($datos['MontoNoHabitualProrrateado']) {

                    $M = new Rrhh_Model_DbTable_PersonasGananciasProrrateos;

                    $sumRemunerativos   = $this->getSumRemunerativos($datos);
                    $proporcionNH       = ($datos['MontoNoHabitualOriginal'] - $datos['MontoNoHabitualNoRemunerativo']) / $sumRemunerativos;
                    $cantMeses          = $datos['MesesPendientes'] ;
                    $datos['proporcionNoHabitual'] = $proporcionNH;

                    echo '-- XXXXXXXXXXXXX -------------------------------------------------------'.PHP_EOL;
                    echo 'MontoNoHabitualOriginal'.' -> '.$datos['MontoNoHabitualOriginal'].PHP_EOL;
                    echo 'MontoHabitual'.' -> '.PHP_EOL.$datos['MontoHabitual'].PHP_EOL;
                    echo 'MesesPendientes'.' -> '.$datos['MesesPendientes'].PHP_EOL;
                    echo 'sumRemunerativos'.' -> '.$sumRemunerativos.PHP_EOL;
                    echo 'proporcionNH'.' -> '.$proporcionNH.PHP_EOL;
                    echo '------------------------------------------------------------------------'.PHP_EOL;
                }

                $MontoADescontarAcumulado = 0;

                foreach ($R as $row) {

                    // $Monto          = round($row['Monto'],2);
                    // $MontoAcumulado = round($row['MontoAcumulado'],2);

                    $Monto          = $row['Monto'];
                    $MontoAcumulado = $row['MontoAcumulado'];


                    if (!$MontoAcumulado) $MontoAcumulado = 0;

                    // Se prorrateo ??
                    if ($datos['MontoNoHabitualProrrateado']) {

                        // Calculo el monto de las deducciones de ganancia con el mismo porcentaje
                        // que hay entre Habitual y no Habitual
                        $MontoADescontar = $Monto * $proporcionNH;

                        // Lo acumulo (este es un acumuladao para TODOS los conceptos y sin dividir por los meses)
                        $MontoADescontarAcumulado = $MontoADescontarAcumulado + $MontoADescontar;

                        // Del monto a descontar del mes lo divido por la cantidad de meses que lo prorrateo
                        // $montoAAgregar   = round($MontoADescontar / $cantMeses,2);

                        // cuota del mes actual
                        $montoAAgregar   = $MontoADescontar / $cantMeses;

                        // Resto el monto proporcional y le sumo la cuota de este mes
                        $MontoCalculado  = $Monto - $MontoADescontar + $montoAAgregar;

                        // Acumulo para este concepto nomas
                        $MontoAcumulado  = $MontoAcumulado - $MontoADescontar + $montoAAgregar;

                        echo '-- YYYYYYYYYYYYY -------------------------------------------------------'.PHP_EOL;
                        echo $row['Monto'].PHP_EOL;
                        echo $row['MontoAcumulado'].PHP_EOL;
                        echo $datos['MontoNoHabitualProrrateado'].PHP_EOL;
                        echo $MontoADescontar.PHP_EOL;
                        echo $montoAAgregar.PHP_EOL;
                        echo $Monto.PHP_EOL;
                        echo $MontoX.PHP_EOL;
                        echo $MontoAcumulado.PHP_EOL;
                        echo '------------------------------------------------------------------------'.PHP_EOL;

                        // Agrego en la table de Prorrateos
                        $d = array(     'LiquidacionRecibo'   => $datos['idRecibo'],
                                        'Monto'               => $montoAAgregar,
                                        'AnioGanancia'        => $datos['anio'],
                                        'MesDesde'            => $datos['mes'],
                                        'MesHasta'            => 12,
                                        'FechaCarga'          => $ahora,
                                        'Persona'             => $datos['idPersona'],
                                        'GananciaConcepto'    => $row['Concepto']
                            );
                        $M->insert($d);

                    } else {
                        $MontoCalculado = $Monto;
                    }

                    // Escribo el registro en Ganancias
                    /*
                    $d = array(     'Persona'             => $datos['idPersona'],
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaConcepto'    => $row['Concepto'],
                                    'Monto'               => $MontoX,
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio'],
                                    'MontoAcumulado'      => $MontoAcumulado
                        );
                    $this->insert($d);
                    */

                    // Agregar los tres insert por separado y en el anterior dejarlo con el monto original.
                    
                    // descuento valor puro
                    $d = array(     'Persona'             => $datos['idPersona'],
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaConcepto'    => $row['Concepto'],
                                    'Monto'               => $Monto,
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio'],
                                    'MontoAcumulado'      => $MontoAcumulado
                        );
                    $this->insert($d);

                    // decremento del descuento

                    $d['Monto']             = -$MontoADescontar;
                    $d['MontoAcumulado']    = 0;
                    $this->insert($d);

                    // cuota = $MontoADescontar

                    $d['Monto']             = $montoAAgregar;
                    $this->insert($d);

                    // Acumulo el mensual
                    $MAT = $MAT + $MontoCalculado;

                }

                if ($MAT) {
                    // Escribo el registro en Ganancias
                    $MAT = $MAT / 12;

                    $d = array(     'Persona'             => $datos['idPersona'],
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaConcepto'    => 55,
                                    'Monto'               => $MAT,
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio'],
                                    'MontoAcumulado'      => $MAT
                        );
                    $this->insert($d);                    
                }


                // Escribo el proporcional de los Descuentos en la tabla Ganancias
                if ($MontoADescontarAcumulado) {

                    // Escribo el registro en Ganancias
                    $d = array(     'Persona'             => $datos['idPersona'],
                                    'Recibo'              => $datos['idRecibo'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaConcepto'    => 46,
                                    'Monto'               => $MontoADescontarAcumulado,
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio'],
                                    'MontoAcumulado'      => 0
                        );
                    $this->insert($d);
                }



                /*

                    $where = "          Persona             = {$datos['idPersona']}
                                AND     Recibo              = {$datos['idRecibo']}
                                AND     Liquidacion         = {$datos['idLiquidacion']}
                                AND     GananciaConcepto    = 44
                                AND     GananciaMesPeriodo  = {$datos['mes']}
                                AND     GananciaAnioPeriodo = {$datos['anio']}
                    ";

                    // Recupero la cuota y le descuento el proporcional de las deducciones
                    $reg = $this->fetchRow($where);

                    $valores = array( 'Monto' => $reg->Monto + ($MontoADescontarAcumulado / $cantMeses)
                        );

                    $this->update($valores,$where);



                }
                */

            }

        /*
        } else {

            echo PHP_EOL;
            echo '--NO Tiene Recibo-------------------------------------------------------'.PHP_EOL;
            echo $sql.PHP_EOL;
            echo '------------------------------------------------------------------------'.PHP_EOL;

            // No tiene recibo este mes pero el acumulado hay que calcularlo igual

            $sql = "SELECT      GC.Id as Concepto,
                                0 as Monto,
                                (
                                SELECT      sum(LRD2.Monto) as MontoAcumulado
                                FROM        LiquidacionesRecibosDetalles    LRD2
                                INNER JOIN  LiquidacionesRecibos            LR2  ON  LR2.Id   = LRD2.LiquidacionRecibo
                                INNER JOIN  VariablesDetalles               VD2  ON  VD2.Id   = LRD2.VariableDetalle
                                INNER JOIN  Variables                       V2   ON  V2.Id    = VD2.Variable
                                INNER JOIN  VariablesTiposDeConceptos       VTC2 ON  VTC2.Id  = V2.TipoDeConcepto
                                INNER JOIN  GananciasConceptos              GC2  ON  GC2.Id   = VTC2.GananciaConcepto
                                INNER JOIN  LiquidacionesPeriodos           LP2  ON  LP2.Id   = LR2.Periodo
                                WHERE   GC2.GananciaConceptoTipo not in (6,7)
                                AND     LR.Ajuste   = 0
                                AND     LRD2.Monto <> 0
                                AND     GC2.Id = GC.Id
                                AND     LP2.FechaDesde   <   '{$datos['periodoFH']}'
                                AND     LR2.Persona      =   {$datos['idPersona']}
                                GROUP BY GC2.Id
                                ) as MontoAcumulado
                    FROM    GananciasConceptos              GC
                    WHERE   GC.GananciaConceptoTipo not in (6,7,8)
                    GROUP BY GC.Id
                    ";

            $R = $this->_db->fetchAll($sql);

            // Si tiene deducciones
            if ($R) {

                foreach ($R as $row) {

                    $d = array(     'Persona'             => $datos['idPersona'],
                                    'Liquidacion'         => $datos['idLiquidacion'],
                                    'GananciaConcepto'    => $row['Concepto'],
                                    'Monto'               => $row['Monto'],
                                    'GananciaMesPeriodo'  => $datos['mes'],
                                    'GananciaAnioPeriodo' => $datos['anio'],
                                    'MontoAcumulado'      => $row['MontoAcumulado']
                        );
                    $this->insert($d);
                }
            }
            */
        }
    }

    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo de un Tercero
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesRecibosTerceros(&$datos) {

        $periodoGananciaFD  = $datos['anio']."-01-01";

        // Horrible... armo un array con los id ... muy feo !!!
        $conceptos = array();
        $conceptos['RemuneracionBrutaTotal']            = 36;
        $conceptos['AporteJubilacion']                  = 30;
        $conceptos['AporteObraSocial']                  = 28;
        $conceptos['AporteSindical']                    = 29;
        $conceptos['ImporteRetribucionesNoHabituales']  = 37;
        $conceptos['RetencionGanancias']                = 34;
        $conceptos['DevolucionGanancia']                = 35;
        $conceptos['Ajustes']                           = 38;

        // Esto esta harcodeado en AFIP y por arrastre lo tengo que harcodear
        $sql = "SELECT  ifnull(RemuneracionBrutaTotal,0)            as RemuneracionBrutaTotal,
                        ifnull(AporteJubilacion,0)                  as AporteJubilacion,
                        ifnull(AporteObraSocial,0)                  as AporteObraSocial,
                        ifnull(AporteSindical,0)                    as AporteSindical,
                        ifnull(ImporteRetribucionesNoHabituales,0)  as ImporteRetribucionesNoHabituales,
                        ifnull(RetencionGanancias,0)                as RetencionGanancias,
                        ifnull(DevolucionGanancia,0)                as DevolucionGanancia,
                        ifnull(Ajustes,0)                           as Ajustes
                FROM    PersonasGananciasPluriempleoDetalle PPD
                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                WHERE   PPD.FechaDeLiquidacion <    '{$datos['periodoFH']}'
                AND     PPD.FechaDeLiquidacion >=   '{$datos['periodoFD']}'
                AND     PGP.Persona            =    {$datos['idPersona']}
                ";
        $R = $this->_db->fetchAll($sql);

        // el acumulado
        $sql2 = "SELECT sum(RemuneracionBrutaTotal)             as RemuneracionBrutaTotal,
                        sum(AporteJubilacion)                   as AporteJubilacion,
                        sum(AporteObraSocial)                   as AporteObraSocial,
                        sum(AporteSindical)                     as AporteSindical,
                        sum(ImporteRetribucionesNoHabituales)   as ImporteRetribucionesNoHabituales,
                        sum(RetencionGanancias)                 as RetencionGanancias,
                        sum(DevolucionGanancia)                 as DevolucionGanancia,
                        sum(Ajustes)                            as Ajustes
                FROM    PersonasGananciasPluriempleoDetalle PPD
                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                WHERE   PPD.FechaDeLiquidacion <    '{$datos['periodoFH']}'
                AND     PPD.FechaDeLiquidacion >=   '$periodoGananciaFD'
                AND     PGP.Persona            =    {$datos['idPersona']}
                ";
        $Ra = $this->_db->fetchAll($sql2);

            echo PHP_EOL;
            echo '--SQL-------------------------------------------------------------------'.PHP_EOL;
            echo $sql.PHP_EOL;
            echo '------------------------------------------------------------------------'.PHP_EOL;
            echo PHP_EOL;
            echo '--SQL 2-----------------------------------------------------------------'.PHP_EOL;
            echo $sql2.PHP_EOL;
            echo '------------------------------------------------------------------------'.PHP_EOL;

        // Si tiene deducciones
        if ($Ra) {
            /*
            if ($R) { 
                $row    = $R->current(); 
            } else {
                $row    = array();
            }
            */
            // $row    = array();
            // $rowA   = $Ra->current();

            echo print_r($R, true);

            echo print_r($Ra, true);
            
            foreach ($conceptos as $key => $value){

                $m  = $R[0][$key];
                $ma = $Ra[0][$key];

                echo '--concepto !!!-----------------------------------------------------------------'.PHP_EOL;
                echo $value.'--'.$key.'--'.$m.'--'.$ma.PHP_EOL;
                echo '------------------------------------------------------------------------'.PHP_EOL;

                $d = array(     'Persona'             => $datos['idPersona'],
                                'Recibo'              => $datos['idRecibo'],
                                'Liquidacion'         => $datos['idLiquidacion'],
                                'GananciaConcepto'    => $value,
                                'Monto'               => $m,
                                'GananciaMesPeriodo'  => $datos['mes'],
                                'GananciaAnioPeriodo' => $datos['anio'],
                                'MontoAcumulado'      => $ma
                    );
                $this->insert($d);
            }
        }
    }
}


