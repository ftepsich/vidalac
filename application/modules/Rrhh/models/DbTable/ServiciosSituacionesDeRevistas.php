<?php
class Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas extends Rad_Db_Table
{
    protected $_name = 'ServiciosSituacionesDeRevistas';

    protected $_sort = array('Servicio','FechaInicio desc');

    protected $_calculatedFields = array('Dias' => 'DATEDIFF(FechaFin, FechaInicio)+1');

    protected $_attachedFiles = array(
        'Imagen' => array(
            'validators' => array(
                 array('MimeType',array('image/jpeg','image/png','application/pdf')),
            )
        )
    );

    protected $_referenceMap    = array(

        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Id'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Servicios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ConveniosLicencias' => array(
            'columns'           => 'ConvenioLicencia',
            'refTableClass'     => 'Rrhh_Model_DbTable_ConveniosLicencias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ConveniosLicencias',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'SituacionesDeRevistas' => array(
            'columns'           => 'SituacionDeRevista',
            'refTableClass'     => 'Rrhh_Model_DbTable_SituacionesDeRevistas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Activo',
            'refTable'          => 'SituacionesDeRevistas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    /**
     * Validadores
     *
     * FechaBaja    -> mayor a fecha alta
     *
     */
    // NO ANDA
    /*
    protected $_validators = array(
        'FechaFin'=> array(
            array( 'GreaterThan',
                    '{FechaInicio}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        )
    );
    */

    protected $_dependentTables = array();

    /**
     * Campos a tener en cuenta para el log de la liquidacion, son aquellos que pueden generar retroactivos
     */
    protected $_logLiquidcionCampos = array(    'Persona',          'Servicio',
                                                'ConvenioLicencia', 'SituacionDeRevista',
                                                'TipoDeJornada'
    );

   /**
     * Campos de fechas a tener en cuenta (inicio, fin, cierre)
     */
    protected $_logLiquidcionFechas = array(    'fechaDesde' =>  'FechaInicio',
                                                'fechaHasta' =>  'FechaFin',
                                                'fechaCierre' =>  null
    );

   /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 1; // 1: Servicio


    /**
     * Inserta un registro y lleva la persona del servicio
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            // Verifico que venga el servicio, sino tiro error
            if(!$data['Servicio']) throw new Rad_Db_Table_Exception("Falta el parametro del servicio del agente");

            // Recupero el servicio
            $M_S = new Rrhh_Model_DbTable_Servicios;
            $R_S = $M_S->find($data['Servicio'])->current();

            if(!$R_S) throw new Rad_Db_Table_Exception("No se encontro el servicio.");

            // Si no vino persona lo completo
            if(!$data['Persona'])  $data['Persona'] = $R_S->Persona;

            //si viene el convenio de licencia le seteo la situacion de revista Asociada.
            if($data['ConvenioLicencia']){
                $model_ConveniosLicencias   = new Rrhh_Model_DbTable_ConveniosLicencias;
                $row_ConveniosLicencias     = $model_ConveniosLicencias->fetchRow("Id = ".$data['ConvenioLicencia']); 
                $data['SituacionDeRevista'] = $row_ConveniosLicencias->SituacionDeRevista; 
            }  

            //controlo que la fecha Inicio no sea menor a la fecha alta del Servicio.
            if ($data['FechaInicio'] < $R_S->FechaAlta) throw new Rad_Db_Table_Exception("La fecha de inicio no puede ser menor a la del servicio.");

            if($data['FechaFin']){
                $FechaFin = $data['FechaFin'];
            } else {
                $FechaFin           = '2099-12-31';
                $S_FechaHasta         = $R_S->FechaBaja;
                //$data['FechaFin']   = $R_S->FechaBaja;
            }

            //controlo que no haya superposicion de fechas con los subservicios distintos a activo
            $this->salirSi_existeSuperposicionDeServiciosSR($R_S->Id,$data['FechaInicio'],$FechaFin);

            // busco el servicio SR anterior a cerrar

//            $R_ServicioSR_Anterior = $this->fetchRow("Servicio = ".$R_S->Id." AND SituacionDeRevista = 1 and FechaInicio <= '".$data['FechaInicio']."' AND ifnull(FechaFin,'2099-12-31') >= '".$FechaInicio."'");

            $R_ServicioSR_Anterior = $this->fetchRow("Servicio = ".$R_S->Id." AND SituacionDeRevista = 1 and FechaInicio < '".$data['FechaInicio']."' AND ifnull(FechaFin,'2099-12-31') >= '".$data['FechaInicio']."'");
            //Rad_Log::debug($data['FechaInicio']);
            //Rad_Log::debug($R_ServicioSR_Anterior);

            if($R_ServicioSR_Anterior){
                //resto un dia a la fecha fin para cerrar el servicio SR anterior
                $FechaHasta = new DateTime($data['FechaInicio']);
                $FechaHasta->sub(new DateInterval('P1D'));

                $R_ServicioSR_Anterior->FechaFin  = $FechaHasta->format('Y-m-d');
                parent::update($R_ServicioSR_Anterior->toArray(),'Id ='.$R_ServicioSR_Anterior->Id);

            }

            // busco el servicio SR posterior a cerrar
            $R_ServicioSR_Posterior = $this->fetchRow("Servicio = ".$R_S->Id." AND SituacionDeRevista = 1 and FechaInicio >= '".$data['FechaInicio']."' AND FechaInicio <= '".$FechaFin."'");
            //Rad_Log::debug($data['FechaFin']);
            //Rad_Log::debug($R_ServicioSR_Posterior);
            if($R_ServicioSR_Posterior){
                if($data['FechaFin']){
                    //sumo un dia a la fecha fin para cerrar el servicio SR posterior
                    $FechaDesde =  new DateTime($data['FechaFin']);
                    $FechaDesde->add(date_interval_create_from_date_string('1 days'));

                    $R_ServicioSR_Posterior->FechaInicio = $FechaDesde->format('Y-m-d');
                    parent::update($R_ServicioSR_Posterior->toArray(),'Id ='.$R_ServicioSR_Posterior->Id);
                } else {
                    parent::delete('Id ='.$R_ServicioSR_Posterior->Id);
                }
            }

           // inserto
            $id = parent::insert($data);

            // Guardo el Log de Liquidaciones --------------------------------------------------
            $rowAct = $this->find($id)->current();
            $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_NL->asentarNovedad('I', $rowAct, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $data['Servicio']);
            // ----------------------------------------------------------- Fin Log Liquidaciones

            if(!$R_S->FechaBaja){
                $FechaBaja = '2099-12-31';
            } else {
                $FechaBaja = $R_S->FechaBaja;
            }
            //Rad_Log::debug($FechaFin);
            // Si el SS es mas corto que el S y no hay otro servicio posterior agrego uno
            if($FechaFin < $FechaBaja) {
                // busco el servicio SR Posterior para no insertar si no hay alguno
                $R_ServicioSR_Posterior_Control = $this->fetchRow("Servicio = ".$R_S->Id." AND Id <> ".$id." AND SituacionDeRevista = 1 and FechaInicio >= '".$data['FechaInicio']."'");
                If(!$R_ServicioSR_Posterior_Control){
                    $fecha =  new DateTime($FechaFin);
                    $fecha->add(date_interval_create_from_date_string('1 days'));

                    $dataSSR = array(
                        'Persona'               => $data['Persona'],
                        'Servicio'              => $R_S->Id,
                        'FechaInicio'           => $fecha->format('Y-m-d'),
                        'FechaFin'              => $R_S->FechaBaja,
                        'SituacionDeRevista'    => 1
                    );

                    $idSSR = parent::insert($dataSSR);
                }
            } else {
                if ($FechaFin > $FechaBaja){
                    throw new Rad_Db_Table_Exception("La fecha no puede ser mayor a la del servicio.");
                }
            }

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
     * @param array $data   Informacion a cambiar
     * @param array $where  Registros que se deben modificar
     */
    public function update ($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            if (count($reg)) {

                $M_S    = new Rrhh_Model_DbTable_Servicios;
                $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;

                foreach ($reg as $row) {
                    $R_S = $M_S->find($row->Servicio)->current();

                    //si viene el convenio de licencia le seteo la situacion de revista Asociada.
                    if($data['ConvenioLicencia']){
                        $model_ConveniosLicencias   = new Rrhh_Model_DbTable_ConveniosLicencias;
                        $row_ConveniosLicencias     = $model_ConveniosLicencias->fetchRow("Id = ".$data['ConvenioLicencia']); 
                        $data['SituacionDeRevista'] = $row_ConveniosLicencias->SituacionDeRevista; 
                    } 

                    if($data['FechaInicio']){
                        $FechaInicio = $data['FechaInicio'];
                        if ($data['FechaInicio'] < $R_S->FechaAlta) throw new Rad_Db_Table_Exception("La fecha de inicio no puede ser menor a la del servicio.");

                        //resto un dia a la fecha que tenia el registro anteriormente para buscar el servicios situacion de revista anterior
                        $FechaHasta = new DateTime($row->FechaInicio);
                        $FechaHasta->sub(new DateInterval('P1D'));

                    } else {
                        $FechaInicio = $row->FechaInicio;
                    }

                    if($data['FechaFin']){
                        $FechaFin = $data['FechaFin'];

                        //sumo un dia a la fecha que tenia el registro anteriormente para buscar el servicios situacion de revista posterior
                        $FechaDesde =  new DateTime($row->FechaFin);
                        $FechaDesde->add(date_interval_create_from_date_string('1 days'));
                    } else {
                        $FechaFin = ($row->FechaFin) ? $row->FechaFin:'2099-12-31';
                    }

                    //controlo que no haya superposicion de fechas con los subservicios distintos a activo
                    $this->salirSi_existeSuperposicionDeServiciosSR($R_S->Id,$FechaInicio,$FechaFin,$row->Id);

                    //controlo que la feche de inicio no sea mayor que la fecha fin
                    if($FechaInicio > $FechaFin) throw new Rad_Db_Table_Exception("La fecha de inicio no puede ser mayor a la fecha fin.");

                    if($FechaHasta){
                        // busco el servicio SR anterior a cerrar
                        $R_ServicioSR_Anterior = $this->fetchRow("Servicio = ".$R_S->Id." AND SituacionDeRevista = 1 and FechaFin = '".$FechaHasta->format('Y-m-d')."'");

                        //resto un dia a la fecha fin para cerrar el servicio SR anterior
                        $FechaHasta = new DateTime($data['FechaInicio']);
                        $FechaHasta->sub(new DateInterval('P1D'));

                        if($R_ServicioSR_Anterior){
                            $R_ServicioSR_Anterior->FechaFin  = $FechaHasta->format('Y-m-d');
                            parent::update($R_ServicioSR_Anterior->toArray(),'Id ='.$R_ServicioSR_Anterior->Id);
                        }
                    }
                    if($FechaDesde) {
                        // busco el servicio SR posterior a cerrar
                        $R_ServicioSR_Posterior = $this->fetchRow("Servicio = ".$R_S->Id." AND SituacionDeRevista = 1 and FechaInicio = '".$FechaDesde->format('Y-m-d')."'");

                        //sumo un dia a la fecha fin para cerrar el servicio SR posterior
                        $FechaDesde =  new DateTime($data['FechaFin']);
                        $FechaDesde->add(date_interval_create_from_date_string('1 days'));

                        if($R_ServicioSR_Posterior){
                            $R_ServicioSR_Posterior->FechaInicio  = $FechaDesde->format('Y-m-d');
                            parent::update($R_ServicioSR_Posterior->toArray(),'Id ='.$R_ServicioSR_Posterior->Id);
                        }
                    } 
                    parent::update($data,'Id ='.$row->Id);

                    // Guardo el Log de Liquidaciones --------------------------------------------------
                    $rowAct = $this->find($row->Id)->current();
                    $M_NL->asentarNovedad('U', $rowAct, $row, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $row->Servicio);
                    // ----------------------------------------------------------- Fin Log Liquidaciones


                }
            }

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public static function getDias($servicio, $periodo, $where){

        $fd     = $periodo->getDesde()->format('Y-m-d');
        $fh     = $periodo->getHasta()->format('Y-m-d');

        $where  .= " AND FechaInicio <= '$fh' ";
        $where  .= " AND ifnull(FechaFin,'2199-01-01') >= '$fd' ";
        $where  .= " AND Servicio = ".$servicio->Id;



        $db     = Zend_Registry::get("db");
        $sql    = "Select * From ServiciosSituacionesDeRevistas Where $where";
        $R      = $db->fetchAll($sql);
        if (!count($R)) return 0;

        // Encontro algo asi que cuento
        $cantDias = 0;
        foreach ($R as $row) {

            // esta linea esta para que no joda el null
            $ff = (!$row['FechaFin']) ? $fh : $row['FechaFin'];
            $fi = $row['FechaInicio'];

            // Recorto lo que sobresale del periodo
            $fi = ($fi < $fd) ? $fd : $fi;
            $ff = ($ff > $fh) ? $fh : $ff;

            $p          = new Rad_Util_RangoFechas($fi, $ff);
            $cantDias   = $cantDias + $p->getDias() +1;
            unset($p);
        }
        return $cantDias;

    }

    /**
     * Revisa si el ServicioSituacionDeRevista se superpone con otro
     *
     * @param idServicio    id del servicio a controlar
     * @param fechaInicio   feche de inicio
     * @param fechaFin      fecha fin
     * @return boolean
    */
    public function existeSuperposicionDeServiciosSR($idServicio,$fechaInicio,$fechaFin,$idServicioSR = null) {
        if($idServicioSR){
            $condicion = " ServiciosSituacionesDeRevistas.Id <> ".$idServicioSR;
        } else {
            $condicion = " 1 = 1 ";
        }

        $condicon1 = " ('$fechaInicio' <= FechaInicio and '$fechaFin' >= FechaInicio) ";
        $condicon2 = " ('$fechaInicio' >= FechaInicio and '$fechaFin' <= FechaFin) ";
        $condicon3 = " ('$fechaInicio' <= FechaFin    and '$fechaFin' >= FechaFin) ";


//        $where  = "ServiciosSituacionesDeRevistas.Servicio = '$idServicio' AND ServiciosSituacionesDeRevistas.SituacionDeRevista <> 1 $condicion AND ($condicon1 OR $condicon2 or $condicon3)";
        $where  = "ServiciosSituacionesDeRevistas.Servicio = '$idServicio' AND ServiciosSituacionesDeRevistas.SituacionDeRevista <> 1 AND $condicion AND ($condicon1 OR $condicon2 or $condicon3)";

        //Rad_Log::debug($where);
        $R  = $this->fetchAll($where);
        //Rad_Log::debug($R);
        if (count($R)>0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Sale si el ServicioSituacionDeRevista se superpone con otro
     *
     * @param idServicio    id del servicio a controlar
     * @param fechaInicio   feche de inicio
     * @param fechaFin      fecha fin
     * @return boolean
    */
    public function salirSi_existeSuperposicionDeServiciosSR($idServicio,$fechaInicio,$fechaFin,$idServicioSR) {
        //Rad_Log::debug($idTabla);
        if ($this->existeSuperposicionDeServiciosSR($idServicio,$fechaInicio,$fechaFin,$idServicioSR)) throw new Rad_Db_Table_Exception('La situacion de revista que quiere ingresar se superpone con otra.');
    }
}
