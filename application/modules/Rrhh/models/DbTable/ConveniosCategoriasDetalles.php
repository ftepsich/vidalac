<?php
class Rrhh_Model_DbTable_ConveniosCategoriasDetalles extends Rad_Db_Table
{
    protected $_name = 'ConveniosCategoriasDetalles';

    protected $_sort = array('FechaDesde DESC');

    protected $_referenceMap    = array(

	    'ConveniosCategorias' => array(
            'columns'           => 'ConvenioCategoria',
            'refTableClass'     => 'Rrhh_Model_DbTable_ConveniosCategorias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'ConveniosCategorias',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_dependentTables = array();

    /**
     * Validadores
     *
     * FechaHasta    -> mayor a fechadesde  ojo --> GreaterThan es solo para numeros, para fecha no tiene
     *
     */
    /*
    protected $_validators = array(
        'FechaHasta'=> array(
            array( 'GreaterThan',
                    '{FechaDesde}'
            ),
            'messages' => array('La fecha de baja no puede ser menor que la fecha de alta.')
        )
    );
    */

    /**
     * Campos a tener en cuenta para el log de la liquidacion, son aquellos que pueden generar retroactivos
     */
    protected $_logLiquidcionCampos = array(    'ConvenioCategoria',     'Valor',
                                                'ValorNoRemunerativo',   'FechaDesde',
                                                'FechaHasta'
    );

   /**
     * Campos de fechas a tener en cuenta (inicio, fin, cierre)
     */
    protected $_logLiquidcionFechas = array(    'fechaDesde' =>  'FechaDesde',
                                                'fechaHasta' =>  'FechaHasta',
                                                'fechaCierre' =>  null
    );

   /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 5; // 5: Convenio


    /**
     * Insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();

            if (!isset($data['FechaDesde']) || !isset($data['Valor'])) throw new Rad_Db_Table_Exception('Faltan datos obligatorios.');

            if (isset($data['Hasta'])) {

                $desde = DateTime::createFromFormat('d-m-Y H:i:s', $data['FechaDesde'].' 00:00:00');
                $hasta = DateTime::createFromFormat('d-m-Y H:i:s', $data['FechaHasta'].' 00:00:00');

                if ($desde >= $hasta) throw new Rad_Db_Table_Exception('La fecha desde es mayor que la fecha hasta.');

            }

            // Verifico que no se superponga con otro periodo cerrado (excepto el ultimo si esta abierto)
            $this->salirSi_superponeConOtroPeriodoCerrado($data);

            // Cierro el periodo anterior
            $this->cerrarPeriodoAnterior($data);

            // inserto el registro
            $id     = parent::insert($data);

            // Guardo el Log de Liquidaciones --------------------------------------------------
            $rowAct = $this->find($id)->current();
            
                // Recupero el id del convenio
                $M_CC   = new Rrhh_Model_DbTable_ConveniosCategorias;
                $rowCC  = $M_CC->find($rowAct->ConvenioCategoria)->current();
            
            $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_NL->asentarNovedad('I', $rowAct, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $rowCC->Convenio );
            // ----------------------------------------------------------- Fin Log Liquidaciones

            $this->_db->commit();

            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data,$where) {
        try {
            $this->_db->beginTransaction();

            $reg = $this->fetchAll($where);

            if (!count($reg))       throw new Rad_Db_Table_Exception('No se encuentran las Categoria a modificar.');
            if ( count($reg) > 1 )  throw new Rad_Db_Table_Exception('Solo puede modificar una Categoria a la vez.');

            $row = $reg->current();

            // Verifico que no se superponga con otro periodo
            /*if ((isset($data['FechaDesde']) && $data['FechaDesde'] != $row->FechaDesde) ||
                (isset($data['FechaHasta']) && $data['FechaHasta'] != $row->FechaDesde)) $this->salirSi_superponeConOtroPeriodo($data,$row);
			*/

            // Verifico si modifico la fecha de alta y reviso que hacer
            if (isset($data['FechaDesde']) && $data['FechaDesde'] != $row->FechaDesde) {

                if ($row->FechaHasta && $data['FechaDesde'] >= $row->FechaHasta) throw new Rad_Db_Table_Exception('La fecha de alta es mayor que la fecha de Baja.');

                // Rever, pero no se cumple mas que pueda cerrar el anterior

                $this->cerrarPeriodoAnterior($data,$row->Id);
            }
            //Rad_Log::debug($data);
            // inserto el registro
            parent::update($data,"Id =".$row->Id);

            // Guardo el Log de Liquidaciones --------------------------------------------------
            $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_CC   = new Rrhh_Model_DbTable_ConveniosCategorias;

            $rowAct = $this->find($row->Id)->current();
			$rowCC =  $M_CC->find($row->ConvenioCategoria)->current();

            $M_NL->asentarNovedad('U', $rowAct, $row, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $rowCC->Convenio);
            // ----------------------------------------------------------- Fin Log Liquidaciones

            $this->_db->commit();

            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Cierra el periodo anterior al ingresado a un dia antes de la fecha de inicio del nuevo periodo.
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @return none
    */
    private function cerrarPeriodoAnterior($data) {

        $FechaDesde_PA = null;

        $sql = "    SELECT  Id, FechaDesde
                    FROM    ConveniosCategoriasDetalles
                    WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                    AND     FechaDesde < '{$data['FechaDesde']}'
                    AND     FechaHasta is not null
                    ORDER BY FechaDesde DESC limit 1";

        $R_PA = $this->_db->fetchRow($sql);

        if ($R_PA) {

            // date de php > 5.2
            $FechaHasta_PA   = new DateTime($data['FechaDesde']);
            $FechaHasta_PA->sub(new DateInterval('P1D'));

            $FechaDesde_PA   = new DateTime($R_PA['FechaDesde']);
            $FechaDesde_N    = new DateTime($data['FechaDesde']);

            if ($FechaDesde_PA < $FechaDesde_N) {
                // Cierro al dia anterior
                $rowU   = $this->find($R_PA['Id']);
                $dataU  = array("FechaHasta" => $FechaHasta_PA->format('Y-m-d'));
                parent::update($dataU, "Id = ".$R_PA['Id']);
                Rad_PubSub::publish('ConveniosCategoriasDetalles_Update', $rowU);
                //Rad_Log::debug('Publico ConveniosCategoriasDetalles_Update(3)');
            } else {
                if ($FechaDesde_PA == $FechaDesde_N) {
                    // esto es un update de valores... debe hacerse como update
                    throw new Rad_Db_Table_Exception('La fecha de alta suministrada es la misma que la del periodo ultimo.');
                } else {
                    // Cargo un periodo historico, como no salio por el control de superposicion solo dejo que siga
                }
            }
        }
    }



    /**
     * Cierra el periodo anterior al ingresado a un dia antes de la fecha de inicio del nuevo periodo.
     * Tambien hace lo mismo en el caso que se modifique la fecha de inicio
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @param int   $Id     identificador del registro a modificar. No se usa en el insert.
     * @return none
    */
    private function moverCierrePeriodoAnterior($data,$Id = null) {

        $FechaDesde_PA = null;

        $sql = "    SELECT  Id, FechaDesde, FechaHasta
                    FROM    ConveniosCategoriasDetalles
                    WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                    AND     FechaDesde < '{$data['FechaDesde']}'
                    ORDER BY FechaDesde DESC limit 1";

        $R_PA = $this->_db->fetchRow($sql);

        if ($R_PA) {


            /*
            if ($R_PA['FechaHasta'] && $R_PA['FechaHasta'] > ) {

            }
            */

            // date de php > 5.2
            $FechaHasta_PA   = new DateTime($data['FechaDesde']);
            $FechaHasta_PA->sub(new DateInterval('P1D'));

            $FechaDesde_PA   = new DateTime($R_PA['FechaDesde']);
            $FechaDesde_N    = new DateTime($data['FechaDesde']);

            if ($FechaDesde_PA < $FechaDesde_N) {
                // Cierro al dia anterior
                $rowU   = $this->find($R_PA['Id']);
                $dataU  = array("FechaHasta" => $FechaHasta_PA->format('Y-m-d'));
                parent::update($dataU, "Id = ".$R_PA['Id']);
                Rad_PubSub::publish('ConveniosCategoriasDetalles_Update', $rowU);
                //Rad_Log::debug('Publico ConveniosCategoriasDetalles_Update(3)');
            } else {
                if ($FechaDesde_PA == $FechaDesde_N) {
                    // esto es un update de valores... debe hacerse como update
                    throw new Rad_Db_Table_Exception('La fecha de alta suministrada es la misma que la del periodo ultimo.');
                } else {
                    // Deberia verificar si no se superpone con otro anterior de no ser asi y de tener fecha de baja se lo dejo cargar.
                    $this->salirSi_superponeConOtroPeriodo($data,$Id);
                    // Es un periodo historico que no se superpone con otro periodo. Lo dejo insertar
                }
            }
        }
    }


    /**
     * Revisa si el periodo ingresado no se superpone con uno existente y cerrado
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @return boolean
    */
    public function superponeConOtroPeriodo2($data) {

        if (!$data['FechaHasta']) {
            $FH = '2999-01-01';
        } else {
            $FH = $data['FechaHasta'];
        }

        $FD = $data['FechaDesde'];

        $sql = "    SELECT  Id
                    FROM    ConveniosCategoriasDetalles
                    WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                    AND     FechaHasta is not null
                    AND  (
                            /* Que las fechas esten dentro de un periodo existente */
                            (
                                (   FechaDesde <= '$FD' AND FechaHasta >= '$FD' )
                                OR
                                (   FechaDesde <= '$FH' AND FechaHasta >= '$FH' )
                            )
                            OR
                            /* Que las fechas contengan un periodo existente */
                            (
                                (   '$FD' <= FechaDesde AND '$FD' >= FechaHasta )
                                OR
                                (   '$FH' <= FechaDesde AND '$FH' >= FechaHasta )
                            )
                        )
                    ";

        //Rad_Log::debug($sql);

        $R = $this->_db->fetchRow($sql);

        if ($R) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Revisa si el periodo ingresado no se superpone con uno existente
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @param int   $Id     identificador del registro a modificar. No se usa en el insert.
     * @return boolean
    */
	/*
    public function superponeConOtroPeriodo($data,$row = null) {

        $Id = (count($row)) : $row->Id : null;
        //$ConvenioCategoria
        $FH = ($data['FechaHasta']) ? $data['FechaHasta'] : ($row->FechaHasta) ? $row->FechaHasta : '2999-01-01';
        $FD = ($data['FechaDesde']) ? $data['FechaDesde'] : $row->FechaDesde;

//        if (!$data['FechaHasta']) {
//            $FH = '2999-01-01';
//        } else {
//            $FH = $data['FechaHasta'];
//        }
//        $FD = $data['FechaDesde'];

        // Si viene de un update no se tiene que tener en cuenta el mismo, y si viene de un insert se puede pisar con el ultimo,
        // ojo ... el ultimo no el anterior !!!
        // en este caso el control de si las fechas desde coinciden lo hace la funcion que cierra el periodo anterior
        $txtId = "";
        if (!$Id) {

            // Busco cual es el ultimo
            $sql = "    SELECT  Id
                        FROM    ConveniosCategoriasDetalles
                        WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                        ORDER BY FechaDesde DESC limit 1";

            $R_PA = $this->_db->fetchRow($sql);

            if ($R_PA) $Id = $R_PA['Id'];

        }

        // Para los update no se tiene en cuenta a si mismo y para los insert el ultimo
        if ($Id) $txtId = " AND ConveniosCategoriasDetalles.Id <> $Id ";

        $sql = "    SELECT  Id
                    FROM    ConveniosCategoriasDetalles
                    WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                    $txtId
                    AND  (
                            // Que las fechas esten dentro de un periodo existente
                            (
                                (   FechaDesde <= '$FD' AND ifnull(FechaHasta,'2999-01-01') >= '$FD' )
                                OR
                                (   FechaDesde <= '$FH' AND ifnull(FechaHasta,'2999-01-01') >= '$FH' )
                            )
                            OR
                            // Que las fechas contengan un periodo existente
                            (
                                (   '$FD' <= FechaDesde AND '$FD' >= ifnull(FechaHasta,'2999-01-01') )
                                OR
                                (   '$FH' <= FechaDesde AND '$FH' >= ifnull(FechaHasta,'2999-01-01') )
                            )
                        )
                    ";

        //Rad_Log::debug($sql);

        $R = $this->_db->fetchRow($sql);

        if ($R) {
            return true;
        } else {
            return false;
        }
    } */


    /**
     * Revisa si el periodo ingresado no se superpone con uno existente y cerrado
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @return boolean
    */
    public function superponeConOtroPeriodo3($data,$Id = null) {

        if (!$data['FechaHasta']) {
            $FH = '2999-01-01';
        } else {
            $FH = $data['FechaHasta'];
        }

        $FD = $data['FechaDesde'];

        // Si viene de un update no se tiene que tener en cuenta el mismo, y si viene de un insert se puede pisar con el ultimo,
        // ojo ... el ultimo no el anterior !!!
        // en este caso el control de si las fechas desde coinciden lo hace la funcion que cierra el periodo anterior
        $txtId = "";
        if (!$Id) {

            // Busco cual es el ultimo
            $sql = "    SELECT  Id
                        FROM    ConveniosCategoriasDetalles
                        WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                        ORDER BY FechaDesde DESC limit 1";

            $R_PA = $this->_db->fetchRow($sql);

            if ($R_PA) $Id = $R_PA['Id'];

        }

        // Para los update no se tiene en cuenta a si mismo y para los insert el ultimo
        if ($Id) $txtId = " AND ConveniosCategoriasDetalles.Id <> $Id ";

        $sql = "    SELECT  Id
                    FROM    ConveniosCategoriasDetalles
                    WHERE   ConvenioCategoria = {$data['ConvenioCategoria']}
                    $txtId
                    AND  (
                            /* Que las fechas esten dentro de un periodo existente */
                            (
                                (   FechaDesde <= '$FD' AND ifnull(FechaHasta,'2999-01-01') >= '$FD' )
                                OR
                                (   FechaDesde <= '$FH' AND ifnull(FechaHasta,'2999-01-01') >= '$FH' )
                            )
                            OR
                            /* Que las fechas contengan un periodo existente */
                            (
                                (   '$FD' <= FechaDesde AND '$FD' >= ifnull(FechaHasta,'2999-01-01') )
                                OR
                                (   '$FH' <= FechaDesde AND '$FH' >= ifnull(FechaHasta,'2999-01-01') )
                            )
                        )
                    ";

        //Rad_Log::debug($sql);

        $R = $this->_db->fetchRow($sql);

        if ($R) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Devuelve el valor del basico remunerativo de una categoria especifica para un periodo dado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    /*
    public static function getBasicoServicio ($servicio, $periodo) {
       return Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoCategoria ($servicio->ConvenioCategoria, $periodo);
    }
    */
    /**
     * Devuelve el valor del basico remunerativo de una categoria de un servicio especifico para un periodo dado
     *
     * @param int       $categoria    id de ConvenioCategoria
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    /*
    public static function getBasicoCategoria ($categoria, $periodo) {
        $db     = Zend_Registry::get("db");
        $sql    =   " SELECT Valor FROM ConveniosCategoriasDetalles WHERE ".
                    " ConvenioCategoria   =  ".$categoria .
                    " AND FechaDesde     <= '".$periodo->getDesde()->format('Y-m-d') ."'".
                    " AND IFNULL(FechaHasta,'2999-01-01')  >= '".$periodo->getHasta()->format('Y-m-d')."'";

        $R_CD   = $db->fetchRow($sql);

        if (!$R_CD) throw new Rad_Db_Table_Exception("La primitiva @basico no localiza el valor para el periodo solicitado.");

        return $R_CD['Valor'];
    }
    */

    /**
     * Devuelve el valor del basico remunerativo de una categoria especifica para un periodo dado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public static function getBasicoServicio ($servicio, $periodo) {
        // Cambio a partir del mes 8 / 2016 para camioneros

        // OJO ... no vienen $periodo->Anio ni $periodo->Valor asi que los saco de la fecha desde
        $anio = $periodo->getDesde()->format('Y');
        $mes  = $periodo->getDesde()->format('m');

        if ($servicio->Convenio == 3 && $anio >= 2016 && $mes >= 8) {
           $monto = Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoCalificacionProfesional ($servicio, $periodo);
           if ($monto) return $monto;
           return Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoCategoria ($servicio->ConvenioCategoria, $periodo);
        } else {
           return Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoCategoria ($servicio->ConvenioCategoria, $periodo);
        }
    }

    /**
     * Devuelve el valor del basico remunerativo de una categoria de un servicio especifico para un periodo dado
     *
     * @param int       $categoria    id de ConvenioCategoria
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public static function getBasicoCategoria ($categoria, $periodo) {
        $fechaDesde = $periodo->getDesde()->format('Y-m-d');
        $fechaHasta = $periodo->getHasta()->format('Y-m-d');

        $sql = "    SELECT  Valor 
                    FROM    ConveniosCategoriasDetalles 
                    WHERE   ConvenioCategoria   =  $categoria 
                    AND     FechaDesde          <= '$fechaDesde'
                    AND     IFNULL(FechaHasta,'2999-01-01')  >= '$fechaHasta'
                ";          

        $db     = Zend_Registry::get("db");
        $R_CD   = $db->fetchRow($sql);
        if (!$R_CD) throw new Rad_Db_Table_Exception("La primitiva @basico no localiza el valor para el periodo solicitado (usando Categoria).");
        return $R_CD['Valor'];
    }

    /**
     * Devuelve el valor del basico remunerativo de una calificacion profesional de un servicio especifico para un periodo dado (solo camioneros)
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public static function getBasicoCalificacionProfesional ($servicio, $periodo) {
        $calificacion = $servicio->CalificacionProfesional;
        $sql = "    SELECT  Monto as Valor
                    FROM    ServiciosCalificacionesProfesionales 
                    WHERE   Id = $calificacion
                ";          

        $db     = Zend_Registry::get("db");
        $R_CD   = $db->fetchRow($sql);

        if (!$R_CD) throw new Rad_Db_Table_Exception("La primitiva @basico no localiza el valor para el periodo solicitado (usando Calificacion profesional).");

        return $R_CD['Valor'];
    }

    /**
     * Devuelve el valor del basico NO remunerativo de una categoria de un servicio especifico para un periodo dado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public static function getBasicoNRServicio ($servicio, $periodo) {
        return Rrhh_Model_DbTable_ConveniosCategoriasDetalles::getBasicoNRCategoria ($servicio->ConvenioCategoria, $periodo);
    }

    /**::
     * Devuelve el valor del basico NO remunerativo de una categoria especifica para un periodo dado
     *
     * @param int       $ctegoria     id de ConvenioCategoria
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public static function getBasicoNRCategoria ($categoria, $periodo) {
        $db     = Zend_Registry::get("db");
        $sql    =   " SELECT ValorNoRemunerativo FROM ConveniosCategoriasDetalles WHERE ".
                    " ConvenioCategoria   =  ".$categoria .
                    " AND FechaDesde     <= '".$periodo->getDesde()->format('Y-m-d') ."'".
                    " AND IFNULL(FechaHasta,'2999-01-01')  >= '".$periodo->getHasta()->format('Y-m-d')."'";

        $R_CD = $db->fetchRow($sql);

        if (!$R_CD) throw new Rad_Db_Table_Exception("La primitiva @basicoNR no localiza el valor para el periodo solicitado.");

        return $R_CD['ValorNoRemunerativo'];
    }

    /**
     * Sale si el periodo ingresado no se superpone con uno existente
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @param int   $Id     identificador del registro a modificar. No se usa en el insert.
     * @return boolean
    */
    public function salirSi_superponeConOtroPeriodo($data,$row = null) {
        if ($this->superponeConOtroPeriodo($data,$row)) throw new Rad_Db_Table_Exception('El periodo ingresado se superpone con otro periodo existente.');
    }

    /**
     * Sale si el periodo ingresado se superpone con uno existente y cerrado
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @return boolean
    */
    public function salirSi_superponeConOtroPeriodoCerrado($data) {
        if ($this->superponeConOtroPeriodo2($data)) throw new Rad_Db_Table_Exception('El periodo ingresado se superpone con otro periodo existente.');
    }

}
