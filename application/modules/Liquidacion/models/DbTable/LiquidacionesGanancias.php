<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Liquidacion_Model_DbTable_LiquidacionesGanancias
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_LiquidacionesGanancias extends Rad_Db_Table
{
    protected $_name            = 'PersonasGananciasLiquidaciones';

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
     * [generarGananciasPeriodo description]
     * @param  [type] $servicio   [description]
     * @param  [type] $periodoLiq [description]
     * @return [type]             [description]
     */
    public function generarGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo,$recalculandoReciboYaLiquidado = false) {

        $datos = array( 'idPeriodo'             => $periodo->getId(),
                        'idServicio'            => $servicio->Id,
                        'idRecibo'              => $recibo->Id,
                        'idEmpresa'             => $servicio->Empresa,
                        'idPersona'             => $servicio->Persona,
                        'idLiquidacion'         => $liquidacion->Id,
                        'mes'                   => $periodo->getDesde()->format('m'),
                        'anio'                  => $periodo->getDesde()->format('Y'),
                        'periodoFD'             => $periodo->getDesde()->format('Y-m-d'),
                        'periodoFH'             => $periodo->getHasta()->format('Y-m-d'),
                        'tipoDeLiquidacion'     => $liquidacion->TipoDeLiquidacion
                );

        // echo print_r($datos, true);

        // Si existe ganancias para este recibo los borra y calcula nuevamente
        $this->delGananciaLiquidada($datos);

        // Salgo si la liq no suma para ganacias
        if ($this->liquidacionNoCuentaParaGanancia($liquidacion->TipoDeLiquidacion)) return false;

        // verifoco que no sea otro el ente recaudador
        // if ($this->retieneOtro($datos)) return false;

        // Si no tiene Liquidacion debe ser todo 0 pero el proceso se hace igual
        $this->setDatosRecibos($datos);

        // seteo los valores de Recibos de Terceros
        $this->setDeduccionesRecibosTerceros($datos);

        // seteo las deducciones personales (presentadas en el 572)
        $this->setDeduccionesPersonales($datos);

        $this->setPagosyDevoluciones($datos, $recalculandoReciboYaLiquidado);

        /*
        if (!$this->retieneOtro($datos)) {
            // seteo los pagos o devoluciones
            $this->setPagosyDevoluciones($datos);
        }
        */
    }

    /**
     * Asienta en la tabla todos los datos del recibo lo mas en bruto posible asi simplificar su comparacion con el recibo
     *
     * @param   array   $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  none
     */
    public function setDatosRecibos(&$datos) {

        // Ganancia Bruta
        // 119

        // Descuentos Mes 
        // 120

        /* Inicializo */
        $seDebeProrratear = false;

        $m  = $this->getSumConceptosIngresosHabituales($datos,$periodoBusqueda);
        $gc = 100; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getSumConceptosIngresosHabitualesRemunerativos($datos,$periodoBusqueda);
        $gc = 11100; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getSumConceptosIngresosNoHabituales($datos,$periodoBusqueda);
        $gc = 101; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getSumConceptosIngresosNoHabitualesNoRemunerativos($datos,$periodoBusqueda);
        $gc = 102; $this->setRegistro($datos,$gc,$m);        

        $m  = $this->getPlusVacaciones($datos,$periodoBusqueda);
        $gc = 103; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getPlusLicencias($datos,$periodoBusqueda);
        $gc = 104; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getTotalHabitual($datos,$periodoBusqueda);
        $gc = 105; $this->setRegistro($datos,$gc,$m);

        // Falta el plus de HsExtra Especiales que no suman
        // 
        //  EN QUE MOMENTO SE APLICA EL QUITAR LAS HS EXTRA para el prorrateo
        // 
        $m  = $this->getPlusHorasExtrasQueNoSuman($datos,$periodoBusqueda);
        $gc = 106; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getTotalNoHabitual($datos,$periodoBusqueda);
        $gc = 200+$datos['mes']; $this->setRegistro($datos,$gc,$m);        

        $m  = $this->getTotalNoHabitualNoRemunerativo($datos,$periodoBusqueda);
        $gc = 107; $this->setRegistro($datos,$gc,$m);

        $m  = $datos['getTotalNoHabitual'] - $datos['getTotalNoHabitualNoRemunerativo'];
        $gc = 108; $this->setRegistro($datos,$gc,$m);
        $datos['getTotalNoHabitualRemunerativo'] = $m;

        $m  = $this->getSumRemunerativos($datos,$periodoBusqueda);
        $gc = 109; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getSumNoRemunerativos($datos,$periodoBusqueda);
        $gc = 110; $this->setRegistro($datos,$gc,$m);

        $m  = $this->getSumDescuentos($datos,$periodoBusqueda);
        $gc = 111; $this->setRegistro($datos,$gc,$m);

        //Total de los descuentos prorrateables
        $m  = $this->getSumDescuentosProrrateables($datos,$periodoBusqueda);
        $gc = 112; $this->setRegistro($datos,$gc,$m);
        $datos['getSumDescuentosProrrateables'] = $m;



        if ($datos['mes'] < 12) {
            /* ----- INICIO ANALISIS PRORRATEO ----- */
            
            // Esta proporcion es para ver la relacion entre lo no habitual y la remuneracion total y si es mayor
            // al 20% hacer el prorrateo
            $m  = ($datos['getTotalNoHabitual']) ? $datos['getTotalNoHabitual'] / $datos['getTotalHabitual'] : 0;
            //$m  = ($datos['getTotalNoHabitual']) ? ($datos['getTotalNoHabitual'] - $datos['getPlusHorasExtrasQueNoSuman']) / ($datos['getSumRemunerativos'] - $datos['getPlusHorasExtrasQueNoSuman']): 0;
            $gc = 600+$datos['mes']; /* codigo 6xx donde xx es el mes */
            $this->setRegistro($datos,$gc,$m);        
            $datos['getProporcionNHyR'] = $m;

            if ($datos['getProporcionNHyR'] && $datos['getProporcionNHyR'] > 0.2) {
                /* prorratea */
                $seDebeProrratear = true;
                 
                // Esta proporcion es para usar con los decuentos que afectan ganancia que como se calculan en base
                // a los conceptos Remunerativos solo tengo que tener en cuenta la parte remunerativa de los conceptos
                // no habituales
                $m  = $datos['getTotalNoHabitualRemunerativo']  / $datos['getSumRemunerativos'];
                //$m  = ($datos['getTotalNoHabitualRemunerativo'] - $datos['getPlusHorasExtrasQueNoSuman']) / ($datos['getSumRemunerativos'] - $datos['getPlusHorasExtrasQueNoSuman']);
                $gc = 700+$datos['mes']; /* codigo 7xx donde xx es el mes */
                $this->setRegistro($datos,$gc,$m);        
                $datos['getProporcionNHRyR'] = $m;

                //Veo la cantidad de meses que faltan contando este (el +1 es para incluir este mes)
                $m  = $datos['MesesPendientes'] = 12 - $datos['mes'] + 1;
                $gc = 113; $this->setRegistro($datos,$gc,$m);

                //Todo lo no habitual se prorratea, pero hay que restarle la proporcion de los descuentos
                //es decir los descuentos prorrateables x la segunda proporcion
                $m  = $datos['getSumDescuentosProrrateables'] * $datos['getProporcionNHRyR'];
                $gc = 300+$datos['mes']; $this->setRegistro($datos,$gc,$m);
                $datos['getDescuentosParteProrrateada'] = $m;

                //Calculo el monto total que se prorrateo
                $m  = $datos['getTotalNoHabitual'] - abs($datos['getDescuentosParteProrrateada']);
                $gc = 400+$datos['mes']; $this->setRegistro($datos,$gc,$m);
                $datos['TotalMontoProrrateado'] = $m;

                //Calculo la cuota del prorrateo
                $m  = $datos['TotalMontoProrrateado'] / $datos['MesesPendientes'];
                $gc = 500+$datos['mes']; $this->setRegistro($datos,$gc,$m,$m); /* aca acumula ya el monto para futuros meses */
                $datos['MontoCuotaMesActual'] = $m;

                // Tambien calculo las dos partes de la cuota, la de ingresos y la de descuentos
                // Ingresos
                $m  = $datos['getTotalNoHabitual'] / $datos['MesesPendientes'];
                $gc = 800+$datos['mes']; $this->setRegistro($datos,$gc,$m,$m);
                $datos['CuotaProrrateoParteIngresos'] = $m;
                // Descuentos
                $m  = $datos['getDescuentosParteProrrateada'] / $datos['MesesPendientes'];
                $gc = 900+$datos['mes']; $this->setRegistro($datos,$gc,$m,$m);
                $datos['CuotaProrrateoParteDescuentos'] = $m;

                // Necesario para el calculo de la 12va parte del sac
                $m  = $datos['getTotalNoHabitualRemunerativo'] / $datos['MesesPendientes'];
                $gc = 1000+$datos['mes']; $this->setRegistro($datos,$gc,$m,$m);
                $datos['CuotaProrrateoParteIngresosRemunerativos'] = $m;                
            }
        }

        // Agrego las cuotas de prorrateos de meses anteriores
        if ($datos['mes'] > 1) {
            $this->addCuotasAnteriores($datos,500);
            $this->addCuotasAnteriores($datos,800);
            $this->addCuotasAnteriores($datos,900);
            $this->addCuotasAnteriores($datos,1000);            
        } 

        // Recupero el monto de las cuotas de todos los meses inclusive este (ambas partes: Ingresos y descuentos)
        $m = $this->getMontoTotalCuotasAcumulado($datos,'500');
        $gc = 114; $this->setRegistro($datos,$gc,$m);
        $datos['MontoTotalAcumuladoCuotas'] = $m;

        // Recupero el monto de las cuotas de todos los meses inclusive parte Ingresos 
        $m = $this->getMontoTotalCuotasAcumulado($datos,'800');
        $gc = 115; $this->setRegistro($datos,$gc,$m);
        $datos['MontoTotalAcumuladoCuotasParteIngresos'] = $m;

        // Recupero el monto de las cuotas de todos los meses inclusive este parte descuentos
        $m = $this->getMontoTotalCuotasAcumulado($datos,'900');
        $gc = 116; $this->setRegistro($datos,$gc,$m);
        $datos['MontoTotalAcumuladoCuotasParteDescuentos'] = $m;        

        // Recupero el monto de las cuotas de todos los meses inclusive este parte descuentos
        $m = $this->getMontoTotalCuotasAcumulado($datos,'1000');
        $gc = 138; $this->setRegistro($datos,$gc,$m);
        $datos['MontoTotalAcumuladoCuotasParteIngresosRemunerativos'] = $m;   

        // Hago el subtotal de todo esto antes de agregar lo nuevo
        // MUCHO OJO de no usar la cuota completa ya que arrastra la parte de los descuentos
        if ($seDebeProrratear) {
            $m = $datos['getTotalHabitual'] + $datos['MontoTotalAcumuladoCuotasParteIngresos'];
        } else {
            $m = $datos['getTotalHabitual'] + $datos['getTotalNoHabitual'] - $datos['getPlusHorasExtrasQueNoSuman'] + $datos['MontoTotalAcumuladoCuotasParteIngresos'];
        }
        $gc = 117; $this->setRegistro($datos,$gc,$m);
        $datos['SubtotalIngresosBrutos'] = $m;

        //Total de los descuentos prorrateables
        $m  = $this->getTotalHabitualRemunerativo($datos,$periodoBusqueda);
        $gc = 1112; $this->setRegistro($datos,$gc,$m);
        // $datos['getTotalHabitualRemunerativo'] = $m;

        // Como el sac se paga sobre lo remunerativo, la base de calculo para la 12va parte de los Ingresos es:
        // Lo Remunerativo (H y NH) - el plus de horas extra que no suman + parte remunerativa de las cuotas de prorrateo (de todos los meses inclusive este)
        if ($seDebeProrratear) {
            $m = $datos['getTotalHabitualRemunerativo'] + $datos['MontoTotalAcumuladoCuotasParteIngresosRemunerativos'];
        } else {
            $m = $datos['getTotalHabitualRemunerativo'] + $datos['getTotalNoHabitualRemunerativo'] - $datos['getPlusHorasExtrasQueNoSuman'] + $datos['MontoTotalAcumuladoCuotasParteIngresosRemunerativos'];
        }
        $gc = 139; $this->setRegistro($datos,$gc,$m);
        $datos['SubtotalIngresosBrutosParaCalculoSAC'] = $m;

        $m = $datos['SubtotalIngresosBrutosParaCalculoSAC'] / 12;   
        $gc = 118; $this->setRegistro($datos,$gc,$m);
        $datos['SAC12vaParteIngresos'] = $m;

        // Totalizo los Ingresos del mes
        // --------------------------------------------------------------------------------------------
        $m =  $datos['SubtotalIngresosBrutos'] + $datos['SAC12vaParteIngresos'];
        $gc = 119; $this->setRegistro($datos,$gc,$m);
        $datos['TotalIngresosBrutoMes'] = $m;
        // --------------------------------------------------------------------------------------------

        // Totalizo los Descuentos del Recibo en el mes
        // --------------------------------------------------------------------------------------------

        $topeSAC = 5332.98;
        if (abs($datos['SAC12vaParteIngresos']) > abs($topeSAC)) {
            echo  '--- 1 ---' . PHP_EOL;    
            $m = (($topeSAC * 0.17) + abs(($datos['SAC12vaParteIngresos'] * 0.058)));
            $m = -$m;
        } else {
            echo  '--- 2 ---' . PHP_EOL;    
            $m = ($datos['getSumDescuentosProrrateables'] - $datos['getDescuentosParteProrrateada'] + $datos['CuotaProrrateoParteDescuentos']) / 12;
        }
        $gc = 137; $this->setRegistro($datos,$gc,$m);
        $datos['SAC12vaParteDescuentos'] = $m;        


        // 112 - 300 + 900 + 137
        
        echo  '+++++++++++++++++++++' . PHP_EOL;
        echo  'getSumDescuentosProrrateables: ' . $datos['getSumDescuentosProrrateables'] . PHP_EOL;
        echo  'getDescuentosParteProrrateada: ' . $datos['getDescuentosParteProrrateada'] . PHP_EOL;
        echo  'CuotaProrrateoParteDescuentos: ' . $datos['CuotaProrrateoParteDescuentos'] . PHP_EOL;
        echo  'SAC12vaParteDescuentos: ' . $datos['SAC12vaParteDescuentos'] . PHP_EOL;
        echo  '+++++++++++++++++++++' . PHP_EOL;
        
        
        $m =  $datos['getSumDescuentosProrrateables'] - $datos['getDescuentosParteProrrateada'] + $datos['CuotaProrrateoParteDescuentos'] + $datos['SAC12vaParteDescuentos'];
        $gc = 120; $this->setRegistro($datos,$gc,$m);
        $datos['TotalDescuentosDelReciboMes'] = $m;
        // --------------------------------------------------------------------------------------------

        // Ahora queda acumular los valores que hagan falta
        // 119 - 120 
        // Los calculos auxiliares que mas importan para clarificar 
        // Las cuotas ya estan acumuladas mas arriba

        return 'ok';
    }

    /**
     * inserta un registro en la tabla
     *
     * @param   array   $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  none
     */
    public function setRegistro($datos,$gc,$m,$ma = 0) {
        $a = array( 'Persona'             => $datos['idPersona'],
                    'Recibo'              => $datos['idRecibo'],
                    'Liquidacion'         => $datos['idLiquidacion'],
                    'GananciaMesPeriodo'  => $datos['mes'],
                    'GananciaAnioPeriodo' => $datos['anio'],
                    'GananciaConcepto'    => $gc,
                    'Monto'               => $m
            );
        if ($ma) $a['MontoAcumulado'] = $ma;
        
        // if ($gc > 99) {
            $this->insert($a);
        // }
        echo $gc . ' -- ' . $m . ' -- ' .$ma . PHP_EOL;
    }
    
    /**
     * recupera la suma acumulada de las cuotas de prorrateo inclusive la del mes actual
     *
     * @param   array   $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  none
     */
    public function getMontoTotalCuotasAcumulado($datos,$baseId) {
        $idGCCuotaMesActual     = $baseId + $datos['mes']; /* la GC se forma como 5xx donde xx es el mes */
        
        $sql = "    SELECT  sum(PGL.MontoAcumulado) as MontoAcumulado
                    FROM    PersonasGananciasLiquidaciones PGL
                    WHERE   Persona                 = {$datos['idPersona']}
                    AND     PGL.GananciaMesPeriodo  = {$datos['mes']}
                    AND     PGL.GananciaAnioPeriodo = {$datos['anio']}
                    AND     PGL.GananciaConcepto    > $baseId
                    AND     PGL.GananciaConcepto    <= $idGCCuotaMesActual
        ";
        //echo PHP_EOL.PHP_EOL.$sql . PHP_EOL.PHP_EOL;
        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    /**
     * Recupera las cuotas de los meses anteriores y las agrega a este mes
     * Incremento el MontoAcumulado sumandole el monto de la cuota que viene en Monto
     *
     * @param   array   $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  none
     */    
    public function addCuotasAnteriores($datos,$baseId) {

        $mesAnterior            = $datos['mes'] - 1;
        $idGCCuotaMesAnterior   = $baseId + $mesAnterior; /* la GC se forma como 5xx donde xx es el mes */

        $sql = "    SELECT  PGL.Monto            as MontoCuota, 
                            PGL.MontoAcumulado   as MontoAcumuladoMesAnterior,
                            PGL.GananciaConcepto as GananciaConcepto
                    FROM    PersonasGananciasLiquidaciones PGL
                    WHERE   Persona                 = {$datos['idPersona']}
                    AND     PGL.GananciaMesPeriodo  = $mesAnterior
                    AND     PGL.GananciaAnioPeriodo = {$datos['anio']}
                    AND     PGL.GananciaConcepto    > $baseId
                    AND     PGL.GananciaConcepto    <= $idGCCuotaMesAnterior
        ";

        $CA = $this->_db->fetchRow($sql);
        
        if ($CA) {
            foreach ($CA as $row) {
                $gc = $row['GananciaConcepto'];
                $m  = $row['MontoCuota'];
                $ma = $row['MontoAcumuladoMesAnterior'] + $row['MontoCuota'];
                $this->setRegistro($datos,$gc,$m,$ma);
            }
        }
    }

    /**
     * Borra los registros de una liquidacion de Ganancias
     *
     * @param   array   $datos   Arreglo con los datos necesarios de persona y servicios
     * @return  none
     */
    public function delGananciaLiquidada($datos) {

        $where = "          Persona             = {$datos['idPersona']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaMesPeriodo  = {$datos['mes']}
                    AND     Recibo              = {$datos['idRecibo']}
                ";
        $existe = $this->fetchRow($where);
        if ($existe) $this->delete($where);
    }

    /**
     * Verifica si el tipo de liquidacion cuenta para ganancia
     *
     * @param   int         $tipoDeLiquidacion   identificador del tipo de Licencia
     * @return  boolean
     */
    public function liquidacionNoCuentaParaGanancia($tipoDeLiquidacion) {
        $sql = "SELECT * FROM TiposDeLiquidaciones WHERE Id = $tipoDeLiquidacion AND NoCuentaParaGanancia = 1";
        $R   = $this->_db->fetchAll($sql);
        if ($R) { return true; } else { return false; }
    }

    /**
     * Devuelve la suma de los Conceptos configurados como Habituales (Remunerativo y No Remunerativo)
     * Contiene la parte del plus de Vacaciones y del plus de Licencias
     *
     * 
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo
     * @return  decimal
     */
    public function getSumConceptosIngresosHabituales(&$datos,$periodoBusqueda) {

        $where = "  /* ConceptosIngresosHabituales (Remunerativos y no Rem) */ 
                    AND     V.NoHabitual            = 0
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['SumConceptosIngresosHabituales'] = $r;
        return $r;        
    }

    /**
     * Devuelve la suma de los Conceptos configurados como No Habituales (Remunerativo y No Remunerativo)
     * No Contiene la parte del plus de Vacaciones y del plus de Licencias
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumConceptosIngresosNoHabituales(&$datos,$periodoBusqueda) {

        $where = "  /* IngresoBrutoNoHabitual (sin plus Vacaciones) */ 
                    AND     V.NoHabitual            = 1
                    AND     V.TipoDeConceptoLiquidacion in (1,2,3,5) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['SumConceptosIngresosNoHabituales'] = $r;
        return $r;
    }



    /**
     * Devuelve la suma de los Conceptos configurados como No Habituales (No Remunerativo)
     * No hay conceptos No Remunerativos que sean en parte habituales y no habituales así que
     * con un select directo los obtengo
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumConceptosIngresosHabitualesRemunerativos(&$datos,$periodoBusqueda) {

        $where = "  /* IngresoBrutoNoHabitual (sin plus Vacaciones) */ 
                    AND     V.NoHabitual            <> 1
                    AND     V.TipoDeConceptoLiquidacion in (1,2) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['SumConceptosIngresosHabitualesRemunerativos'] = $r;
        return $r;
    }

    /**
     * Devuelve la suma de los Conceptos configurados como No Habituales (No Remunerativo)
     * No hay conceptos No Remunerativos que sean en parte habituales y no habituales así que
     * con un select directo los obtengo
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumConceptosIngresosNoHabitualesNoRemunerativos(&$datos,$periodoBusqueda) {

        $where = "  /* IngresoBrutoNoHabitual (sin plus Vacaciones) */ 
                    AND     V.NoHabitual            = 1
                    AND     V.TipoDeConceptoLiquidacion in (3,5) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['SumConceptosIngresosNoHabituales'] = $r;
        return $r;
    }
    /* Funcion que trasparenta la anterior -- solo para mantener la nomenclatura en la base */
    public function getTotalNoHabitualNoRemunerativo(&$datos,$periodoBusqueda) {
        $r = $this->getSumConceptosIngresosNoHabitualesNoRemunerativos($datos,$periodoBusqueda);
        $datos['getTotalNoHabitualNoRemunerativo'] = $r;
        return $r;        
    }

    /**
     * Busca el plus de las vacaciones
     * este monto hay que descontarlo de lo habitual y sumarlo a lo no habitual
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo
     * @return  decimal
     */
    public function getPlusVacaciones(&$datos,$periodoBusqueda) {

        $where = "  /* plus Vacaciones -- 17: descuentos dias vacaciones, 19: pago dias vacaciones */ 
                    AND     V.TipoDeConcepto in (17,19) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['getPlusVacaciones'] = $r;
        return $r;        
    }

    /**
     * Busca el plus de las licencias
     * este monto hay que descontarlo de lo habitual y sumarlo a lo no habitual
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getPlusLicencias(&$datos,$periodoBusqueda) {

        $where = "  /* plus Licencias -- 16: descuentos dias licencias, 18: pago dias licencias */ 
                    AND     V.TipoDeConcepto in (16,18) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['getPlusLicencias'] = $r;
        return $r; 
    }

    /**
     * Devuelve los remunerativos de un recibo
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumRemunerativos(&$datos,$periodoBusqueda) {

        $where = "  /* SumRemunerativos */ 
                    AND     V.TipoDeConceptoLiquidacion in (1,2) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['getSumRemunerativos'] = $r;
        return $r; 
    }

    /**
     * Devuelve los no remunerativos de un recibo
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumNoRemunerativos(&$datos,$periodoBusqueda) {

        $where = "  /* SumNoRemunerativos */ 
                    AND     V.TipoDeConceptoLiquidacion in (3,5) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['getSumNoRemunerativos'] = $r;
        return $r; 
    }

    /**
     * Devuelve los descuentos de un periodo... todos sin discriminar
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumDescuentos(&$datos,$periodoBusqueda) {

        $where = "  /* SumDescuentos */ 
                    AND     V.TipoDeConceptoLiquidacion in (4) ";
        $r = $this->getSumConceptos($datos,$where,$periodoBusqueda);
        $datos['getSumDescuentos'] = $r;
        return $r; 
    }

    /**
     * Devuelve el ingreso bruto mensual Habitual (Remunerativo y No Remunerativo)
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getTotalHabitual(&$datos,$periodoBusqueda) {
        
        $ibh            = $this->getSumConceptosIngresosHabituales($datos,$periodoBusqueda);
        $plusVacaciones = $this->getPlusVacaciones($datos,$periodoBusqueda);
        $plusLicencias  = $this->getPlusLicencias($datos,$periodoBusqueda);

        $r = $ibh - $plusVacaciones - $plusLicencias;
        $datos['getTotalHabitual'] = $r;
        return $r;         
    }

    /**
     * Devuelve el ingreso bruto mensual Habitual (Remunerativo y No Remunerativo)
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getTotalHabitualRemunerativo(&$datos,$periodoBusqueda) {
        
        $ibh            = $this->getSumConceptosIngresosHabitualesRemunerativos($datos,$periodoBusqueda);
        $plusVacaciones = $this->getPlusVacaciones($datos,$periodoBusqueda);
        $plusLicencias  = $this->getPlusLicencias($datos,$periodoBusqueda);

        $r = $ibh - $plusVacaciones - $plusLicencias;
        $datos['getTotalHabitualRemunerativo'] = $r;
        return $r;         
    }

    /**
     * Devuelve el ingreso bruto mensual No Habitual (Remunerativo y No Remunerativo)
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getTotalNoHabitual(&$datos,$periodoBusqueda) {
        
        $ibnh           = $this->getSumConceptosIngresosNoHabituales($datos,$periodoBusqueda);
        $plusVacaciones = $this->getPlusVacaciones($datos,$periodoBusqueda);
        $plusLicencias  = $this->getPlusLicencias($datos,$periodoBusqueda);

        $r =  $ibnh + $plusVacaciones + $plusLicencias;
        $datos['getTotalNoHabitual'] = $r;
        return $r;          
    }

    /**
     * Devuelve la suma de los conceptos del recibo que cumplan con lo solicitado
     *
     * @param   array       $datos              Arreglo con los datos necesarios de persona y servicios
     * @param   string      $where              Parte de la consulta que define lo que se busca
     * @param   Rad_db      $periodoBusqueda    objeto LiquidacionPeriodo     
     * @return  decimal
     */
    public function getSumConceptos(&$datos,$where,$periodoBusqueda) {

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
                    INNER JOIN  TiposDeLiquidaciones            TL  ON  TL.Id   = L.TipoDeLiquidacion
                    WHERE   LR.Ajuste   = 0
                    AND     LRD.Monto   <> 0
                    AND     TL.NoCuentaParaGanancia = 0
                    AND     V.NoCuentaParaGanancia  = 0
                    AND     V.Id not in (118) /* 118: Redondeo */
                    AND     LR.Periodo  = $idPeriodo
                    AND     LR.Persona  = {$datos['idPersona']}
                    AND     L.Empresa   = {$datos['idEmpresa']}                    
                    $where
        ";
        /*
        echo '-- getSumConceptos ---------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '----------------------------------------------------------------'.PHP_EOL;
        */
        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        return $Monto;
    }

    public function getSumDescuentosProrrateables(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "  SELECT      ifnull(sum(LRD.Monto),0) as Monto
                  FROM        LiquidacionesRecibosDetalles    LRD
                  INNER JOIN  LiquidacionesRecibos            LR  ON  LR.Id   = LRD.LiquidacionRecibo
                  INNER JOIN  Liquidaciones                   L   ON  L.Id    = LR.Liquidacion
                  INNER JOIN  VariablesDetalles               VD  ON  VD.Id   = LRD.VariableDetalle
                  INNER JOIN  Variables                       V   ON  V.Id    = VD.Variable
                  INNER JOIN  VariablesTiposDeConceptos       VTC ON  VTC.Id  = V.TipoDeConcepto
                  INNER JOIN  GananciasConceptos              GC  ON  GC.Id   = VTC.GananciaConcepto
                  WHERE   GC.GananciaConceptoTipo       = 1 /* Deducciones de ley */
                  AND     GC.GananciaDeduccionTipo      = 3 /* Deducciones */
                  AND     V.TipoDeConceptoLiquidacion   = 4 /* Descuentos */
                  AND     ifnull(V.NoCuentaParaGanancia,0) <> 1
                  AND     L.TipoDeLiquidacion in (1,2,3)
                  AND     LR.Ajuste   = 0
                  AND     LRD.Monto   <> 0
                  AND     LR.Periodo  = {$datos['idPeriodo']}
                  AND     LR.Persona  = {$datos['idPersona']}
                  AND     L.Empresa   = {$datos['idEmpresa']}
        ";
        /*
        echo '-- getSumDescuentosProrrateables -------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '----------------------------------------------------------------'.PHP_EOL;
        */
        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        $datos['getSumDescuentosProrrateables'] = $Monto;
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

        /*
        echo '--No getPlusHorasExtrasQueNoSuman ----------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;
        */

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        $datos['getPlusHorasExtrasQueNoSuman'] = $Monto;
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
    public function getHorasExtras(&$datos,$periodoBusqueda) {

        if($periodoBusqueda) {
            $idPeriodo   = $periodoBusqueda->getId();
        } else {
            $idPeriodo   = $datos['idPeriodo'];
        }

        $sql = "    SELECT SUM(Monto) 
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

        /*
        echo '--No getPlusHorasExtrasQueNoSuman ----------------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '------------------------------------------------------------------------'.PHP_EOL;
        */

        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;
        $datos['getHorasExtras'] = $Monto;
        return $Monto;
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
        $conceptos['RecibosTerceros_RemuneracionBrutaTotal']            = 150;
        $conceptos['RecibosTerceros_AporteJubilacion']                  = 151;
        $conceptos['RecibosTerceros_AporteObraSocial']                  = 152;
        $conceptos['RecibosTerceros_AporteSindical']                    = 153;
        $conceptos['RecibosTerceros_ImporteRetribucionesNoHabituales']  = 154;
        $conceptos['RecibosTerceros_RetencionGanancias']                = 155;
        $conceptos['RecibosTerceros_DevolucionGanancia']                = 156;
        $conceptos['RecibosTerceros_Ajustes']                           = 157;

        // Esto esta harcodeado en AFIP y por arrastre lo tengo que harcodear
        $sql = "SELECT  ifnull(RemuneracionBrutaTotal,0)            as RecibosTerceros_RemuneracionBrutaTotal,
                        ifnull(AporteJubilacion,0)                  as RecibosTerceros_AporteJubilacion,
                        ifnull(AporteObraSocial,0)                  as RecibosTerceros_AporteObraSocial,
                        ifnull(AporteSindical,0)                    as RecibosTerceros_AporteSindical,
                        ifnull(ImporteRetribucionesNoHabituales,0)  as RecibosTerceros_ImporteRetribucionesNoHabituales,
                        ifnull(RetencionGanancias,0)                as RecibosTerceros_RetencionGanancias,
                        ifnull(DevolucionGanancia,0)                as RecibosTerceros_DevolucionGanancia,
                        ifnull(Ajustes,0)                           as RecibosTerceros_Ajustes
                FROM    PersonasGananciasPluriempleoDetalle PPD
                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                WHERE   PPD.FechaDeLiquidacion <    '{$datos['periodoFH']}'
                AND     PPD.FechaDeLiquidacion >=   '{$datos['periodoFD']}'
                AND     PGP.Persona            =    {$datos['idPersona']}
                ";
        $R = $this->_db->fetchAll($sql);

        // el acumulado
        $sql2 = "SELECT sum(RemuneracionBrutaTotal)             as RecibosTerceros_RemuneracionBrutaTotal,
                        sum(AporteJubilacion)                   as RecibosTerceros_AporteJubilacion,
                        sum(AporteObraSocial)                   as RecibosTerceros_AporteObraSocial,
                        sum(AporteSindical)                     as RecibosTerceros_AporteSindical,
                        sum(ImporteRetribucionesNoHabituales)   as RecibosTerceros_ImporteRetribucionesNoHabituales,
                        sum(RetencionGanancias)                 as RecibosTerceros_RetencionGanancias,
                        sum(DevolucionGanancia)                 as RecibosTerceros_DevolucionGanancia,
                        sum(Ajustes)                            as RecibosTerceros_Ajustes
                FROM    PersonasGananciasPluriempleoDetalle PPD
                INNER JOIN PersonasGananciasPluriempleo PGP on PGP.Id = PPD.PersonaGananciaPluriempleo
                WHERE   PPD.FechaDeLiquidacion <    '{$datos['periodoFH']}'
                AND     PPD.FechaDeLiquidacion >=   '$periodoGananciaFD'
                AND     PGP.Persona            =    {$datos['idPersona']}
                ";
        $Ra = $this->_db->fetchAll($sql2);

        // Si tiene deducciones
        if ($Ra) {            
            foreach ($conceptos as $key => $value){

                $gc = $value;
                $m  = $R[0][$key];
                $ma = $Ra[0][$key];

                $m  = ($m) ? $m : 0;
                $ma = ($ma) ? $ma : 0;

                $this->setRegistro($datos,$gc,$m,$ma);
                $datos[$key] = $m;

                $datos['RecibosDeTercerosDescuentos'] = $datos['RecibosDeTercerosDescuentos'] + $m;
            }

            $gc = 158;
            $m  = $datos['RecibosDeTercerosDescuentos'];
            $this->setRegistro($datos,$gc,$m);
        }
    }

    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setDeduccionesPersonales(&$datos) {

        $deducciones = array();

        // Debo recorre mes a mes cuanto es el monto que le corresponde
        for ($mes = 1; $mes <= $datos['mes']; $mes++) {

            // Debo completar el array ya que existen deducciones que aparecen un solo mes y despues no las sume mas tarde
            if ($mes > 1) {
                $deducciones[$mes] = $deducciones[$mes-1];
                //echo '## DEDUCCIONES #########################################################'.PHP_EOL;
                //// echo print_r($deducciones[$mes], true).PHP_EOL;
                //echo '########################################################################'.PHP_EOL;
            }

            $periodoFD = $datos['anio']."-".str_pad($mes, 2,"0",STR_PAD_LEFT)."-01";

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
            // echo print_r($res, true).PHP_EOL;


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

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;
            // echo print_r($res, true).PHP_EOL;


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
                        Where Concepto is not null
            ";

            $res = $this->_db->fetchAll($sql);
            if ($res) $R = array_merge($R, $res);
            // echo print_r($R, true).PHP_EOL;
            // echo print_r($res, true).PHP_EOL;


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
            //echo print_r($R, true).PHP_EOL;

            if ($R) {

                foreach ($R as $row){

                    switch ($row['Tipo']) {
                        case 1:
                            // Monto Fijo Anual
                            $MontoDeduccion                 = ($row['Monto'] / 12);
                            $MontoDeduccionSinBeneficios    = ($row['Monto'] / 12);
                            break;
                        case 2:
                            // Monto Fijo por suceso
                            $MontoDeduccion                 = ($row['Monto'] / 12);
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
                    // Blanqueo
                    $MontoDeduccion = 0;
                }
            }
        }

        // grabo
        if ($deducciones) {
            // echo '===---===deducciones'.PHP_EOL;
            // echo print_r($deducciones, true).PHP_EOL;
            foreach ($deducciones as $Dmes => $arrValores){
                if ($Dmes == $datos['mes']) {
                    foreach ($arrValores as $Dconcepto => $Dmonto) {
                        $gc = $Dconcepto;
                        $m  = $ValorMes[$Dconcepto];
                        $ma = $Dmonto;
                        $this->setRegistro($datos,$gc,$m,$ma);

                        $datos['deduccionesMes']        = $datos['deduccionesMes'] + $m;
                        $datos['deduccionesAcumuladas'] = $datos['deduccionesAcumuladas'] + $ma;
                    }
                }
            }

            $gc = 121;
            $m  = $datos['deduccionesMes'];
            $ma = $datos['deduccionesAcumuladas'];
            $this->setRegistro($datos,$gc,$m,$ma);
        }
    }

    /**
     * Devuelve la suma de los Pagos de Ganancia anteriores al mes actual del año en curso
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getPagosAnteriores(&$datos,$periodoGananciaFD,$periodoGananciaFH,$idPersona,$idEmpresa) {
       
        $idPeriodo = $datos['idPeriodo'];

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
                        -- momentaneo
                        AND     LR2.Periodo       <> $idPeriodo                        

                        UNION 

                        SELECT      ifnull(sum(PGPD.RetencionGanancias),0) as MontoAcumulado
                                    -- ifnull(sum(PGPD.DevolucionGanancia),0) as MontoAcumulado
                        FROM        PersonasGananciasPluriempleoDetalle PGPD
                        INNER JOIN  PersonasGananciasPluriempleo PGP on PGP.Id = PGPD.PersonaGananciaPluriempleo
                        WHERE   Persona = $idPersona
                        AND     PGPD.FechaDeLiquidacion   >=  '$periodoGananciaFD'
                        AND     PGPD.FechaDeLiquidacion   <   '$periodoGananciaFH'
                ) X ";

        $pa = $this->_db->fetchOne($sql);
        $pa = ($pa) ? $pa : 0;
        return $pa;
    }

    /**
     * Devuelve la suma de las Devoluciones de Ganancia anteriores al mes actual del año en curso
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return decimal
     */
    public function getDevolucionesAnteriores(&$datos,$periodoGananciaFD,$periodoGananciaFH,$idPersona,$idEmpresa) {
       
        $idPeriodo = $datos['idPeriodo'];

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
                        -- momentaneo
                        AND     LR2.Periodo       <> $idPeriodo

                        UNION 

                        SELECT      ifnull(sum(PGPD.DevolucionGanancia),0) as MontoAcumulado
                        FROM        PersonasGananciasPluriempleoDetalle PGPD
                        INNER JOIN  PersonasGananciasPluriempleo PGP on PGP.Id = PGPD.PersonaGananciaPluriempleo
                        WHERE   Persona = $idPersona
                        AND     PGPD.FechaDeLiquidacion   >=  '$periodoGananciaFD'
                        AND     PGPD.FechaDeLiquidacion   <   '$periodoGananciaFH'
                ) X ";

        $da = $this->_db->fetchOne($sql);
        return ($da) ? $da : 0;
    }

    /**
     * Setea los valores para este mes y los acumulados hasta este mes de las deducciones sufridas en el recibo de sueldo
     * y agrega el concepto al recibo de sueldo
     *
     * @param array $datos   Arreglo con los datos necesarios de persona y servicios
     * @return none
     */
    public function setPagosyDevoluciones(&$datos,$recalculandoReciboYaLiquidado = false) {

        $this->setAcumulado($datos,119);
        $this->setAcumulado($datos,150);
        $this->setAcumulado($datos,120);
        $this->setAcumulado($datos,158);
        //$this->setAcumulado($datos,121); /* nunca se acumulan las deducciones, ser recalculan cada vez*/

        if ($datos['idRecibo']) {

            $periodoGananciaFD  = $datos['anio']."-01-01";
            $periodoGananciaFH  = $datos['periodoFH'];
            $idPersona          = $datos['idPersona'];
            $idRecibo           = $datos['idRecibo'];
            $mes                = $datos['mes'];
            $anio               = $datos['anio'];
            $idEmpresa          = $datos['idEmpresa'];

            // *******************************************************************************************
            // Ganancia Neta Sujeta a Retencion
            // *******************************************************************************************
            $gananciaNetaSujetaARetencionAcumulada  = $this->gananciaNetaSujetaARetencionAcumulada($datos);
            // *******************************************************************************************
            // Devoluciones Anteriores
            // *******************************************************************************************
            $devolucionesAnteriores                 = $this->getDevolucionesAnteriores($datos,$periodoGananciaFD,$periodoGananciaFH,$idPersona,$idEmpresa);
            if ($recalculandoReciboYaLiquidado and $datos['mes'] == 1) $devolucionesAnteriores = 0;
            // *******************************************************************************************
            // Pagos Anteriores
            // *******************************************************************************************
            $pagosAnteriores                        = $this->getPagosAnteriores($datos,$periodoGananciaFD,$periodoGananciaFH,$idPersona,$idEmpresa);            
            if ($recalculandoReciboYaLiquidado and $datos['mes'] == 1) $pagosAnteriores = 0;


            if ($gananciaNetaSujetaARetencionAcumulada > 0) {
                // *******************************************************************************************
                // Ganancia Neta Sujeta a Retencion Sin Horas Extra para buscar en escala
                // *******************************************************************************************
                $gc = 123;
                $m = $this->getHorasExtras($datos);
                $this->setRegistro($datos,$gc,$m);
                $HorasExtrasAcumuladas                      = $this->setAcumulado($datos,123);
                
                $gc = 106;
                $plusHorasExtrasQueNoSumanAcumuladas        = $this->setAcumulado($datos,106);

                $gananciaNetaSujetaARetencionSinHorasExtra  = $gananciaNetaSujetaARetencionAcumulada + $plusHorasExtrasQueNoSumanAcumuladas - $HorasExtrasAcumuladas ;
                $gc = 124;
                $this->setRegistro($datos,$gc,0,$gananciaNetaSujetaARetencionSinHorasExtra);

                $escala = $this->getEscala($datos,$gananciaNetaSujetaARetencionSinHorasExtra);

                // Calculo los valores
                $gc = 134; $this->setRegistro($datos,$gc,$escala['MontoDesde']);
                $gc = 135; $this->setRegistro($datos,$gc,$escala['Alicuota'] );
                $gc = 136; $this->setRegistro($datos,$gc,$escala['CanonFijo']);                
                 
                $limiteInferiorEscala   = $escala['MontoDesde'] / 12 * $datos['mes'];
                $alicuota               = $escala['Alicuota'] / 100;
                $canonFijo              = $escala['CanonFijo'] / 12 * $datos['mes'];                

                $ImpuestoAcumulado      = (($gananciaNetaSujetaARetencionAcumulada - $limiteInferiorEscala) * $alicuota) + $canonFijo;
                $ImpuestoParaLiquidar   = $ImpuestoAcumulado + abs($devolucionesAnteriores) - abs($pagosAnteriores);

                if (!$ImpuestoParaLiquidar) {
                    $MontoRetener   = 0;
                    $MontoDevolver  = 0;
                } else {
                    if ($ImpuestoParaLiquidar > 0) {
                        $VarDetalle     = 500;
                        $MontoRetener   = -$ImpuestoParaLiquidar;
                        $MontoDevolver  = 0;
                    } else {
                        $VarDetalle     = 484;
                        $MontoRetener   = 0;
                        $MontoDevolver  = -$ImpuestoParaLiquidar;                
                    }
                }
                $gc = 125; $this->setRegistro($datos,$gc,$devolucionesAnteriores);
                $gc = 126; $this->setRegistro($datos,$gc,$pagosAnteriores);
                $gc = 127; $this->setRegistro($datos,$gc,$limiteInferiorEscala);
                $gc = 128; $this->setRegistro($datos,$gc,$alicuota);
                $gc = 129; $this->setRegistro($datos,$gc,$canonFijo);
                $gc = 130; $this->setRegistro($datos,$gc,$ImpuestoAcumulado);
                $gc = 131; $this->setRegistro($datos,$gc,$ImpuestoParaLiquidar);
                $gc = 132; $this->setRegistro($datos,$gc,$MontoRetener);
                $gc = 133; $this->setRegistro($datos,$gc,$MontoDevolver);


                //if (!$tablaAfip) throw new Rad_Db_Table_Exception("Falta ingresar la tabla de datos de ganancia para el periodo seleccionado.");
            } else {

                $ImpuestoParaLiquidar = abs($pagosAnteriores) - abs($devolucionesAnteriores) ;

                if ($ImpuestoParaLiquidar > 0) {
                    $VarDetalle     = 484;
                    $MontoRetener   = 0;
                    $MontoDevolver  = -$ImpuestoParaLiquidar;                      
                }
            }
            
            if ($ImpuestoParaLiquidar) {
                $M_LRD          = Service_TableManager::get(Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles);
                $M_Concepto     = Service_TableManager::get(Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles);

                $Monto = ($MontoRetener) ? $MontoRetener : $MontoDevolver;

                $c = array(     'LiquidacionRecibo'   => $datos['idRecibo'],
                                'VariableDetalle'     => $VarDetalle,
                                'Monto'               => round($Monto, 2, PHP_ROUND_HALF_UP),
                                'MontoCalculado'      => round($Monto, 2, PHP_ROUND_HALF_UP),
                                'PeriodoDevengado'    => $datos['idPeriodo'],
                                'Detalle'             => '',
                                'ConceptoCodigo'      => $M_Concepto->getCodigo($VarDetalle),
                                'ConceptoNombre'      => $M_Concepto->getNombre($VarDetalle)
                    );
                if (!$recalculandoReciboYaLiquidado) {
                    $M_LRD->delete("LiquidacionRecibo = {$datos['idRecibo']} and VariableDetalle = $VarDetalle");
                    $M_LRD->insert($c);
                }
            }
                     
        }
    }

    public function getEscala($datos,$Monto) {

        $sql = "SELECT  E.*
                FROM    AfipGananciasEscalas E
                INNER JOIN AfipGananciasEscalasPeriodos P ON E.AfipEscalaPeriodo = P.Id
                WHERE   E.Desde/12*{$datos['mes']} < $Monto
                AND     E.Hasta/12*{$datos['mes']} >= $Monto
                AND     P.FechaHasta >= '".$datos['periodoFD']."'
                ORDER BY P.FechaDesde desc
                LIMIT 1
                ";

        //echo "--Datos Tabla rangos Afip (Con Beneficios) -----------------------------".PHP_EOL;
        //echo $sql.PHP_EOL;
        //echo '------------------------------------------------------------------------'.PHP_EOL;

        $tablaAfip = $this->_db->fetchRow($sql);

        $escala = array(    'MontoDesde'    => $tablaAfip['Desde'], 
                            'Alicuota'      => $tablaAfip['Alicuota'],
                            'CanonFijo'     => $tablaAfip['CanonFijo']
                );

        return $escala;
    }




    public function gananciaNetaSujetaARetencionAcumulada(&$datos) {

        $gnsrMes = 0;
        $gnsrA   = 0;

        // Recupero los que necesito
        $gc = 119;
        $gananciaBrutaMes                       = $this->getValor($datos,$gc);
        $gananciaBrutaAcumulada                 = $this->getValorAcumulado($datos,$gc);

        $gc = 150;
        $rTercerosGananciaBrutaMes              = $this->getValor($datos,$gc);
        $rTercerosGananciaBrutaAcumulada        = $this->getValorAcumulado($datos,$gc);

        $gc = 120;
        $descuentosRecibosMes                   = $this->getValor($datos,$gc);
        $descuentosRecibosAcumulados            = $this->getValorAcumulado($datos,$gc);

        $gc = 158;
        $rTercerosDescuentosRecibosMes          = $this->getValor($datos,$gc);
        $rTercerosDescuentosRecibosAcumulados   = $this->getValorAcumulado($datos,$gc);

        $gc = 121;
        $deduccionesMes                         = $this->getValor($datos,$gc);
        $deduccionesAcumuladas                  = $this->getMontoAcumulado($datos,$gc); /* Ojo con este que es diferente */

        $gnsrMes = $gananciaBrutaMes + $rTercerosGananciaBrutaMes + $descuentosRecibosMes + $rTercerosDescuentosRecibosMes + $deduccionesMes;
        $gnsrA   = $gananciaBrutaAcumulada + $rTercerosGananciaBrutaAcumulada + $descuentosRecibosAcumulados + $rTercerosDescuentosRecibosAcumulados + $deduccionesAcumuladas;

        $gc = 122;
        $this->setRegistro($datos,$gc,$gnsrMes,$gnsrA);
        return $gnsrA;

    }

    public function getValor($datos,$gc) {
        $sql    = " SELECT  Monto as Monto
                    FROM    PersonasGananciasLiquidaciones
                    WHERE   Persona             = {$datos['idPersona']}
                    AND     GananciaMesPeriodo  <= {$datos['mes']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaConcepto    = $gc
        ";
        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;

        //echo '--v: ' . $gc . ' --> ' . $Monto . PHP_EOL;

        return $Monto;
    }

    public function getValorAcumulado($datos,$gc) {
        $sql    = " SELECT  sum(Monto) as Monto
                    FROM    PersonasGananciasLiquidaciones
                    WHERE   Persona             = {$datos['idPersona']}
                    AND     GananciaMesPeriodo  <= {$datos['mes']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaConcepto    = $gc
        ";
        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;

        //echo '--a: ' . $gc . ' --> ' . $Monto . PHP_EOL;

        return $Monto;
    }

    public function getMontoAcumulado($datos,$gc) {
        $sql    = " SELECT  MontoAcumulado as MontoAcumulado
                    FROM    PersonasGananciasLiquidaciones
                    WHERE   Persona             = {$datos['idPersona']}
                    AND     GananciaMesPeriodo  = {$datos['mes']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaConcepto    = $gc
        ";
        $MontoAcumulado = $this->_db->fetchOne($sql);
        $MontoAcumulado = ($MontoAcumulado) ? $MontoAcumulado : 0;

        //echo '--a: ' . $gc . ' --> ' . $Monto . PHP_EOL;

        return $MontoAcumulado;
    }


    /**
     * setea la suma acumulada de las cuotas de prorrateo inclusive la del mes actual
     *
     * @param   array       $datos  Arreglo con los datos necesarios de persona y servicios
     * @param   integer     $gc     Identificador de Ganancia Concepto
     * @return  none
     */
    public function setAcumulado($datos,$gc) {
        
        $where = "          Persona             = {$datos['idPersona']}
                    AND     GananciaMesPeriodo  <= {$datos['mes']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaConcepto    = $gc
                ";

        $sql    = " SELECT  sum(Monto) as MontoAcumulado
                    FROM    PersonasGananciasLiquidaciones
                    WHERE   $where
        ";
        //echo PHP_EOL.PHP_EOL.$sql . PHP_EOL.PHP_EOL;
        $Monto = $this->_db->fetchOne($sql);
        $Monto = ($Monto) ? $Monto : 0;

        $d = array('MontoAcumulado' => $Monto);
        $where = "          Persona             = {$datos['idPersona']}
                    AND     GananciaMesPeriodo  = {$datos['mes']}
                    AND     GananciaAnioPeriodo = {$datos['anio']}
                    AND     GananciaConcepto    = $gc
                ";
        $this->update($d,$where);

        //echo '-sa: ' . $gc . ' --> ' . $Monto . PHP_EOL;

        return $Monto;
    }

    public function ee($idLiquidacion,$idPeriodo,$idServicio,$idRecibo,$sinReliquidar) {
        /*
        $idLiquidacion  = 887;
        $idPeriodo      = 50;

        // OS
        // Arbelo X

        $idRecibo       = 36031;
        $idServicio     = 221;

        // Gomez Viviana
        // $idRecibo       = 34932;
        // $idServicio     = 32;

        // Gomez Juan Ramon (Sind) --- horas Extra nuevas
        //$idRecibo       = 34902;
        //$idServicio     = 152;

        // Penco Antonella (Sind)
        //$idRecibo       = 34856;
        //$idServicio     = 157;

        $M_L    = new Liquidacion_Model_DbTable_Liquidaciones;
        $M_LP   = new Liquidacion_Model_DbTable_LiquidacionesPeriodos;
        $M_LR   = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
        $M_S    = new Rrhh_Model_DbTable_Servicios;

        $liquidacion= $M_L->fetchRow('Id = '.$idLiquidacion);
        $recibo     = $M_LR->fetchRow('Id = '.$idRecibo);
        $servicio   = $M_S->fetchRow('Id = '.$idServicio);
        $periodo    = $M_LP->getPeriodo($idPeriodo);

        $M_LG    = new Liquidacion_Model_DbTable_LiquidacionesGanancias;
        echo $M_LG->generarGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo);
        */

        /*
        $idLiquidacion  = 871;
        $idPeriodo      = 49;
        $sinReliquidar  = 1;
        $idServicio     = 208;
        */

        $M_L    = new Liquidacion_Model_DbTable_Liquidaciones;
        $M_LP   = new Liquidacion_Model_DbTable_LiquidacionesPeriodos;
        $M_LG   = new Liquidacion_Model_DbTable_LiquidacionesGanancias;
        $M_S    = new Rrhh_Model_DbTable_Servicios;

        $servicio = ($idServicio) ? " and Servicio = $idServicio " : "";
        $recibo   = ($idRecibo) ? " and Recibo = $idRecibo " : "";

        $M_LG->delete('Liquidacion ='.$idLiquidacion . $recibo);

        $liquidacion    = $M_L->fetchRow('Id = '.$idLiquidacion);
        $periodo        = $M_LP->getPeriodo($idPeriodo);

        $empresaId      = $liquidacion->Empresa;
        $liquidacionId  = $liquidacion->Id;
        $periodoId      = $periodo->getId();

        $where = "      Ajuste      = 0 
                    and Periodo     = $periodoId 
                    and Liquidacion = $liquidacionId
                    $servicio
                    and Servicio in (   Select Id 
                                        from   Servicios 
                                        where   Empresa = $empresaId
                                        and     (   ifnull(GananciaAlBajar,1) = 1 
                                                    or 
                                                    (   
                                                    ifnull(GananciaAlBajar,1) = 1
                                                    and     '$periodoFD' >  ifnull(FechaBaja,'2999-01-01')
                                                    and     '$periodoFH' <  ifnull(FechaBaja,'2999-01-01')
                                                    )
                                                )
                                    ) 
                    $and";       

        $M_LR = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
        $R_LR = $M_LR->fetchAll($where);

        $recibo = null;
        // Si existen liquidaciones para ese mes calculo
        if ($R_LR) {
            foreach ($R_LR as $row) {
                // Recupero el servicio de la persona
                echo "----------- servicio nuevo -----------";
                $servicio   = $M_S->fetchRow('Id = '.$row['Servicio']);
                $recibo     = $M_LR->fetchRow('Id = '.$row['Id']);
                // Armo el cuadro para ese mes y para esa persona
                echo $M_LG->generarGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo,$sinReliquidar);
            }
        }
    }



}


