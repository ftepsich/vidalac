<?php
class Liquidacion_Model_DbTable_NovedadesDeLiquidaciones extends Rad_Db_Table
{
    protected $_name            = 'NovedadesDeLiquidaciones';
    protected $_sort            = array('FechaCarga desc','FechaInicioNovedad desc','FechaFinNovedad desc');
    protected $_referenceMap    = array();
    protected $_dependentTables = array();

    /**
     * asentarNovedad
     *
     * Asienta las modificaciones de las tablas importantes para liquidaciones. Estas pueden llegar a generar retroactivos.
     *
     * @param  varchar              $operacion     Identificador de la operacion (I,U y D)
     * @param  Rad_Db_Table_Row     $rowAct        row con los datos actuales
     * @param  Rad_Db_Table_Row     $rowOld        row con los datos antes de la modificacion (solo viene cuando update)
     * @param  array                arrayCampos    Array con los campos que pueden generar retroactivos
     * @param  array                $arrayFechas   Array con los campos que contienen las fechas de alta, baja y cierre
     * @param  integer              $jerarquia     Jerarquia a la que afecta una novedad (1 a 6, null = 6)
     * @param  integer              $idJerarquia   Id del elemento de la jerarquia que se ve afectado por la novedad. (ej: idServicio, idPersona, idConvenio, etc)
     * @param  date(Y-m-d)          $fechaNovedad  Fecha en la que sucede la novedad (caso particular para situaciones indirectas)
     * @return none
     */
    public function asentarNovedad($operacion, $rowAct, $rowOld, $arrayCampos, $arrayFechas, $jerarquia, $idJerarquia, $fechaNovedad = null) {
        // Si viene $fechaNovedad, la operacion solo puede ser I o D por tener una vinculacion indirecta (ej: FeriadosTrabajados)
        if ($fechaNovedad && $operacion == 'U') throw new Rad_Db_Table_Exception('No se puede modificar el registro. Eliminelo e ingreselo correctamente de ser necesario.');

        // Verifico si la fecha viene expresada en forma de periodo como ser el caso de las hs extra
        $fechaPorPeriodo = ($arrayFechas['periodoAnio']) ? true : false;

        // Por ahora salto con error pero puede ser interesante comparar todos los campos si no viene $arrayCampos
        // o si no vienen fechas asentarlo tambien, por ejemple si cambio un dato en Persona y no tiene fecha.
        // if (!$arrayCampos || !$arrayFechas) throw new Rad_Db_Table_Exception('Error de sistema. No se han indicado');

        // Recupero el usuario que esta logeado al sistema
        $usr            = Zend_Auth::getInstance()->getIdentity();
        $usuario        = $usr->Id;

        // Recupero la instancia del modelo que genero los row
        $M_row          = $rowAct->getTable();

        // Recupero los nombres de la tabla y de la clase desde donde se genero el row
        $nombreModelo   = get_class($M_row);
        $tablaModelo    = $M_row->getName();

        // Recupero el nombre de todas las columnas del modelo
        $columnas       = $M_row->getColumns();

        // Timestamp
        $ahora          = date('Y-m-d H:i:s');
        $hoy            = date('Y-m-d');

        // Armo el data
        $data = array(
                'Operacion'             => $operacion,
                'IdNovedad'             => $rowAct['Id'],
                'FechaCarga'            => $ahora,
                'Tabla'                 => $tablaModelo,
                'Modelo'                => $nombreModelo,
                'Usuario'               => $usuario,
                'Estado'                => 1,
                'Jerarquia'             => $jerarquia,
                'IdJerarquia'           => $idJerarquia
        );

        if ($operacion == 'I' || $operacion == 'D') {

            $data['Modificacion']       = json_encode($rowAct->toArray());

            if ($fechaNovedad) {
                // Novedada que sucede un solo dia pero el dato de la fecha esta en otra tabla
                $data['FechaInicioNovedad'] = $fechaNovedad;
                $data['FechaFinNovedad']    = $fechaNovedad;
            } else {
                if ($fechaPorPeriodo) {
                    // Dupla Año-mes
                    $data['FechaInicioNovedad'] = $rowAct[$arrayFechas['periodoAnio']].'-'.str_pad($rowAct[$arrayFechas['periodoMes']], 2, "0", STR_PAD_LEFT).'-10';
                    $data['FechaFinNovedad']    = $data['FechaInicioNovedad'];
                } else {
                    // Fechas normales
                    $data['FechaInicioNovedad'] = $rowAct[$arrayFechas['fechaDesde']];
                    $data['FechaFinNovedad']    = $rowAct[$arrayFechas['fechaHasta']];
                }
            }

            $id = parent::insert($data);
            unset($data);

        } else {

            // Inicializo
            $cambioDatos = false;
            $cambioFecha = false;
            $cambioDatosLiquidaciones = false;

            // Verifico si cambio un dato importante
            foreach ($columnas as $campos) {
                // Si no es un campo de descripcion de un combo lo reviso
                if (!preg_match("/_cdisplay/", $rowAct[$campos] )) {

                    if ($rowOld[$campos] != $rowAct[$campos]) {

                        // Agrego un renglo con los datos modificados
                        $arrayValoresAnteriores[$campos] =
                                array(  'Antes'     => $rowOld[$campos],
                                        'Despues'   => $rowAct[$campos]
                                );

                        // Marco que cambio un dato cualquiera
                        $cambioDatos                = true;

                        // Marco si cambio un dato que afecta a liquidciones
                        // el && !in_array($campos, $arrayFechas) es para qeu si agrego los campos de fecha como importantes. El manejo de fecha es aparte
                        $cambioDatosLiquidaciones   = (!$cambioDatosLiquidaciones && in_array($campos, $arrayCampos) && !in_array($campos, $arrayFechas)) ? true : $cambioDatosLiquidaciones;
                    }
                }
            }

            // Mapeo las fechas a variables
            // TODO: Falta ver la fecha de Cierre

            if ($fechaPorPeriodo) {

                $FD_Act = $rowAct[$arrayFechas['periodoAnio']].'-'.str_pad($rowAct[$arrayFechas['periodoMes']], 2, "0", STR_PAD_LEFT).'-10';
                $FH_Act = $FD_Act;

                $FD_Old = $rowOld[$arrayFechas['periodoAnio']].'-'.str_pad($rowOld[$arrayFechas['periodoMes']], 2, "0", STR_PAD_LEFT).'-10';
                $FH_Old = $FD_Old;

                $cambioPeriodo = ($FD_Old != $FD_Act) ? true : false;

                if ($cambioPeriodo) {

                    if ($rowAct[$arrayFechas['periodoAnio']] != $rowOld[$arrayFechas['periodoAnio']]) {
                        $arrayValoresAnteriores[$arrayFechas['periodoAnio']] =
                                array(  'Antes'     => $rowOld[$arrayFechas['periodoAnio']],
                                        'Despues'   => $rowAct[$arrayFechas['periodoAnio']]
                                );
                    }

                    if ($rowAct[$arrayFechas['periodoMes']] != $rowOld[$arrayFechas['periodoMes']]) {
                        $arrayValoresAnteriores[$arrayFechas['periodoMes']] =
                                array(  'Antes'     => $rowOld[$arrayFechas['periodoMes']],
                                        'Despues'   => $rowAct[$arrayFechas['periodoMes']]
                                );
                    }
                }


            } else {

                $FD_Act = $rowAct[$arrayFechas['fechaDesde']];
                $FH_Act = $rowAct[$arrayFechas['fechaHasta']];
                $FH_Act = ($FH_Act) ? $FH_Act : '2999-01-01'; // para no tener null

                $FD_Old = $rowOld[$arrayFechas['fechaDesde']];
                $FH_Old = $rowOld[$arrayFechas['fechaHasta']];
                $FH_Old = ($FH_Old) ? $FH_Old : '2999-01-01'; // para no tener null

                // Verifico si cambio fecha
                $cambioFechaDesde = ($FD_Old != $FD_Act) ? true : false;
                $cambioFechaHasta = ($FH_Old != $FH_Act) ? true : false;

                // Si cambio alguna, agrego la fecha a las modificaciones (lo hago aca para no complicar abajo el manejo de fechas)
                if ($cambioFechaDesde || $cambioFechaHasta) {

                    if ($FD_Old != $FD_Act) {
                        $arrayValoresAnteriores[ $arrayFechas['fechaDesde']] =
                                array(  'Antes'     => $FD_Old,
                                        'Despues'   => $FD_Act
                            );
                    }

                    if ($FH_Old != $FH_Act) {
                        $arrayValoresAnteriores[$arrayFechas['fechaHasta']] =
                                array(  'Antes'     => $FH_Old,
                                        'Despues'   => $FH_Act
                                );
                    }
                }
            }

            $data['Modificacion'] = json_encode($arrayValoresAnteriores);

            // Si cambio datos que puedan impactar en la liquidacion las fechas deben ser las mas amplias,
            // es decir la menor fecha de inicio o la mayor fecha de baja.

            if ($cambioDatosLiquidaciones) {

                $FD = ($FD_Old < $FD_Act) ? $FD_Old : $FD_Act ;
                $FH = (!$FH_Old || !$FH_Act) ? null : ($FH_Old > $FH_Act) ? $FH_Old : $FH_Act;

                // insert
                $dataN = $data;
                $dataN['FechaInicioNovedad']    = $FD;
                $dataN['FechaFinNovedad']       = $FH;

                $id = parent::insert($dataN);
                unset($dataN);

            } else  {

                // Si no cambio datos importantes veo que paso con las fechas
                // Puede que termine haciendo dos periodos, uno entre las fechas de inicio y otro entre las fechas de baja

                // MUY IMPORTANTE
                // En el analisis de fechas
                //
                // En el estudio de las fechas desde, a la hasta hay que restarle 1 para pasarla de 01/01/2014 a 31/12/2013
                // (del primer dia del periodo liquidado siguiente al ultimo del que corresponde)
                //
                // En el estudio de las fechas hasta, a la desde hay que sumarle 1 para pasarla de 31/12/2013 a 01/01/2014
                // (del ultimo dia del periodo liquidado anterior al primero del que corresponde)
                //
                // Esto es para que no liquide denuevo un periodo que no debe.
                // Por regla general la fecha desde debe coincidir con un inicio de periodo y la hasta con el fin de un periodo.

                // Periodo 1 que se da entre las dos fechas Desde
                if ($cambioFechaDesde || $cambioPeriodo) {
                    if ($FD_Old < $FD_Act) {
                        $FD = $FD_Old;
                        $FH = $FD_Act;
                    } else {
                        $FD = $FD_Act;
                        $FH = $FD_Old;
                    }

                    // Ajusto la fecha para qeu sea un fin de periodo (ultimo dia de un mes)
                    $f  = new DateTime($FH);
                    $f->sub(new DateInterval('P1D'));
                    $FH = $f->format('Y-m-d');

                    // insert
                    $dataN = $data;
                    $dataN['FechaInicioNovedad']    = $FD;
                    $dataN['FechaFinNovedad']       = $FH;

                    $id = parent::insert($dataN);
                    unset($dataN);
                }
                    // Periodo 2 que se da entre las dos fechas Hasta
                    // Veo si hay que marcar un segundo periodo

                if ($cambioFechaHasta || $cambioPeriodo) {
                    if ($FH_Old < $FH_Act) {
                        $FD = $FH_Old;
                        $FH = ($FH_Act != '2999-01-01') ? $FH_Act : null; // Vuelvo a poner el null
                    } else {
                        $FD = $FH_Act;
                        $FH = ($FH_Old != '2999-01-01') ? $FH_Old : null; // Vuelvo a poner el null
                    }

                    // Ajusto la fecha para qeu sea un primer dia de un periodo (primer dia de un mes)
                    $f  = new DateTime($FD);
                    $f->add(new DateInterval('P1D'));
                    $FD = $f->format('Y-m-d');

                    // insert
                    $dataN = $data;
                    $dataN['FechaInicioNovedad']    = $FD;
                    $dataN['FechaFinNovedad']       = $FH;

                    $id = parent::insert($dataN);
                    unset($dataN);
                }
            }
        }
    }

    /**
     * [getRetroactivos description]
     * @param  Liquidacion_Model_DbTable_LiquidacionesPeriodos  $periodo        Objeto de tipo Periodo o el id del Periodo
     * @param  row                                              $liquidacion    Registro de la liquidacion actual
     * @param  integer                                          $jerarquiaFiltro      Se usa para las liq de una persona (o en un futuro de una jearquia)
     * @param  integer                                          $jerarquiaFiltroValor Id del elemento de la jerarquia indicada (ej: j=1, jv= id del servicio)
     * @return array          Array de dos dimensiones donde la primera es el periodo y la segunda es el servicio
     */
    public function getRetroactivos($periodo, $liquidacion, $jerarquiaFiltro = null, $JerarquiaFiltroValor = null) {
        // Recupero los datos de la liq anterior para ese tipo y empresa
        $FDLiqAnterior = '2000-01-01';

        $retroactivos = array();

        $sql    = " SELECT  Id, Ejecutada
                    FROM    Liquidaciones
                    WHERE   TipoDeLiquidacion   = {$liquidacion['TipoDeLiquidacion']}
                    AND     Empresa             = {$liquidacion['Empresa']}
                    AND     Ejecutada           < '{$liquidacion['Ejecutada']}'
                    ORDER BY Ejecutada desc
                    LIMIT   1";

        $R = $this->_db->fetchRow($sql);

        // Si no hay liquuidaciones anteriors no puedo hacer un retroactivo
        if (count($R)) {
            $FDLiqAnterior = $R['Ejecutada'];

            // $retroactivos es un array con tres dimensiones la primera es el periodo y la segunda es el servicio
            // y la tercera la Liquidacion Normal

            /*  Se comento para que no joda mientras implementamos
                Esto debe estar andando para cuando liquidemos retroactivos*/
            $this->RetroactivosPorJerarquia ($retroactivos, $periodo, $liquidacion, $FDLiqAnterior, 6, $jerarquiaFiltro, $JerarquiaFiltroValor);
            $this->RetroactivosPorJerarquia ($retroactivos, $periodo, $liquidacion, $FDLiqAnterior, 5, $jerarquiaFiltro, $JerarquiaFiltroValor);
            $this->RetroactivosPorJerarquia ($retroactivos, $periodo, $liquidacion, $FDLiqAnterior, 4, $jerarquiaFiltro, $JerarquiaFiltroValor);
            $this->RetroactivosPorJerarquia ($retroactivos, $periodo, $liquidacion, $FDLiqAnterior, 3, $jerarquiaFiltro, $JerarquiaFiltroValor);
            $this->RetroactivosPorJerarquia ($retroactivos, $periodo, $liquidacion, $FDLiqAnterior, 2, $jerarquiaFiltro, $JerarquiaFiltroValor);
            $this->RetroactivosPorJerarquia ($retroactivos, $periodo, $liquidacion, $FDLiqAnterior, 1, $jerarquiaFiltro, $JerarquiaFiltroValor);
        }

        echo ' -- RETROACTIVOS (Perido, servicio, Liquidacion) ------------------------------------- '.PHP_EOL;
        echo ' -- RETROACTIVOS (Perido, servicio, Liquidacion) ------------------------------------- '.PHP_EOL;
        print_r($retroactivos);
        echo ' ------------------------------------------------------------------------------------- '.PHP_EOL;

        return $retroactivos;
    }

    /**
     * Completa en el array retroactivos las personas que deben tener retroactivos y en que periodos
     * @param array     $retroactivos   Array con las duplas idPersona|idPeriodo
     * @param obj       $periodo        Periodo que se esta liquidando
     * @param int       $jerarquia      Identificador de la jerarquia en la que hay buscar los retroactivos
     */
    public function RetroactivosPorJerarquia (&$retroactivos, $periodo, $liquidacion, $FDLiqAnterior, $jerarquia, $jerarquiaFiltro, $JerarquiaFiltroValor) {

        // Inicializo
        $pFD           = $periodo->getDesde()->format('Y-m-d');
        $pFH           = $periodo->getHasta()->format('Y-m-d');
        $idPeriodo     = $periodo->getId();
        $where         = "FechaInicioNovedad < '$pFD' and Estado = 1 ";
        if ($jerarquia) $where .= " and Jerarquia = $jerarquia ";

        /*
            Debo agregar que busque solo aquellas novedades cargadas entre la fecha de liq anterior y la actual.
            Puede ser que algo se cargue antes de la fecha de cierre de liq y algun empleado ya tenga esa
            novedad reflejada en el recibo de sueldo anterior en el caso que se le reliquide antes del
            cierre. Si sucede no hay problema ya que cuando compare los recibos le va a dar 0 y no va a
            liquidar por demas. Por esta razon es que vamos a usar la fecha de calculo y no la de cierre de la liq.
        */
        //$where         .= " AND FechaCarga > '$FDLiqAnterior' AND FechaCarga < '{$liquidacion['Ejecutada']}'";

        /* TODO: Parche momentaneo debe buscar las cosas que se tiraron entre ambas liquidaciones no hasta ahora. */

        $where         .= " AND FechaCarga > '$FDLiqAnterior' AND FechaCarga <= DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')";
        $where         .= " AND FechaCarga > '$pFD' AND FechaCarga <= DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')";

        $R            = $this->fetchAll($where);

         echo $jerarquia."\n".$where."\n"; print_r($R->toArray());

        if (count($R)) {

            // Recorro las novedades
            foreach ($R as $row) {

                $nFI = $row->FechaInicioNovedad;

                $nFF = ($row->FechaFinNovedad) ? (($row->FechaFinNovedad > $pFD) ? $pFD : $row->FechaFinNovedad) : $pFD;

                // Recupero los servicios y periodos afectados por la novedad
                $sql = "    SELECT  distinct S.Id as Servicio, P.Id as Periodo, L.Id as Liquidacion
                            FROM    Servicios S
                            INNER JOIN LiquidacionesRecibos LR  on S.Id = LR.Servicio
                            INNER JOIN LiquidacionesPeriodos P  on P.Id = LR.Periodo
                            INNER JOIN Liquidaciones L          on L.Id = LR.Liquidacion
                            WHERE   /* Los periodos qeu abarque la novedad */
                                    P.FechaDesde <= '$nFF'
                            AND     P.FechaHasta >= '$nFI'
                            AND     P.Id <> $idPeriodo
                                    /* Los servicios que tengan alguna fraccion dentro de la novedad */
                            AND     S.FechaAlta <= P.FechaHasta
                            AND     ifnull(S.FechaBaja, '2199-01-01' >= P.FechaDesde)
                                    /* Y que al menos tenga una liquidacion del mismo tipo para ese mes */
                            AND     L.TipoDeLiquidacion = {$liquidacion['TipoDeLiquidacion']}
                                    /* agregado despues del cambio de que las liq son por empresa */
                            AND     S.Empresa = {$liquidacion['Empresa']}

                            AND     LR.Ajuste = 0
                                    /* que al menos tenga un concepto perteneciente a ese periodo y que no sea el redondeo, esto
                                        es por si solo le pagan un retroactivo ese mes */
                            AND     LR.Id in (  select  LRD.LiquidacionRecibo
                                                from    LiquidacionesRecibosDetalles LRD
                                                inner join VariablesDetalles VD on VD.Id = LRD.VariableDetalle
                                                where   LRD.LiquidacionRecibo = LR.Id
                                                and     VD.Variable not in (118) /* ajuste por redondeo */
                                                and     LRD.PeriodoDevengado = P.Id
                                            )
                                    /* controlo que no haga retroactivos del año anterior a la implementacion*/
                            AND     P.Anio > 2016
                ";

                 echo ' -- Novedades ------------------------------------- '.PHP_EOL;
                 echo ' -- Sin Filtros ----------------------------------- '.PHP_EOL;
                 echo $sql.PHP_EOL;

                // Filtro por la jerarquia de la novedad
                $sql .= $this->filtroSqlJerarquia($row->Jerarquia,$row->IdJerarquia);
                // Filtro por la jerarquia del rango que se esta liquidando (empresa, persona, grupo, etc)

                if ($jerarquiaFiltro && $JerarquiaFiltroValor) {
                    $sql .= $this->filtroSqlJerarquia2($jerarquiaFiltro, $JerarquiaFiltroValor);
                }

                $P = $this->_db->fetchAll($sql);

                //throw new Rad_Db_Table_Exception($sql);

                //echo ' -- Con Filtros ----------------------------------- '.PHP_EOL;
                // echo $sql.PHP_EOL;
                // echo ' -- Fin Novedades --------------------------------- '.PHP_EOL;

                if (count($P)) {
                    // recorro las duplas periodos-servicios y las agrego, si se duplican las va a pisar
                    foreach ($P as $serv) {
                        $retroactivos[$serv['Periodo']] [$serv['Servicio']] [$serv['Liquidacion']] = 1;
                    }
                }
            }
        }

        return $n;
    }


    public function filtroSqlJerarquia($jerarquia,$idJerarquia) {
        // Armo el filtro para cada caso ($row->Jerarquia)
        switch ($jerarquia) {
            case 1:
                $sql = " AND S.Id = $idJerarquia";
                break;
            case 2:
                $sql = " AND S.Id in ( SELECT GP1.Persona FROM GruposDePersonas GP1 WHERE GP1.Id = $idJerarquia ) ";
                break;
            case 3:
                $sql = " AND S.ConvenioCategoria = $idJerarquia";
                break;
            case 4:
                $sql = " AND S.Empresa = $idJerarquia";
                break;
            case 5:
                $sql = " AND S.Convenio = $idJerarquia";
                break;
            case 6:
                // no hace falta hacer nada
                break;
            default:
                throw new Rad_Db_Table_Exception('No se puede determinar la jerarquia en una novedad que puede generar un retroactivo.');
                break;
        }
        return $sql;
    }

    public function filtroSqlJerarquia2($jerarquia,$idJerarquia) {
        // Armo el filtro para cada caso ($row->Jerarquia)
        switch ($jerarquia) {
            case 'SERVICIO':
                $sql = " AND S.Id = $idJerarquia";
                break;
            case 'GRUPO_PERSONAS':
                $sql = " AND S.Id in ( SELECT Persona FROM GruposDePersonas WHERE Id = $idJerarquia ) ";
                break;
            case 'CATEGORIA':
                $sql = " AND S.ConvenioCategoria = $idJerarquia";
                break;
            case 'EMPRESA':
                $sql = " AND S.Empresa = $idJerarquia";
                break;
            case 'CONVENIO':
                $sql = " AND S.Convenio = $idJerarquia";
                break;
            case 'GENERICO':
                $sql = '';
                break;
            default:
                throw new Rad_Db_Table_Exception('No se puede determinar la jerarquia en una novedad que puede generar un retroactivo.');
                break;
        }
        return $sql;
    }


}