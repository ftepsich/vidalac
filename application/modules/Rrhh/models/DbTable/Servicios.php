<?php
class Rrhh_Model_DbTable_Servicios extends Rad_Db_Table
{
    protected $_name = 'Servicios';

    protected $_sort = array('FechaAlta desc');

    public static $JERARQUIAS = array('EMPRESA', 'CONVENIO', 'CATEGORIA', 'GRUPO_PERSONAS', 'SERVICIO', 'GENERICO');

    protected $_referenceMap    = array(

	    'Empresas' => array(
            'columns'           => 'Empresa',
            'refTableClass'     => 'Base_Model_DbTable_Empresas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Empresas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'EmpresasSucursales' => array(
            'columns'           => 'EmpresaSucursal',
            'refTableClass'     => 'Base_Model_DbTable_EmpresasSucursales',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'EmpresasSuursales',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ConveniosCategorias' => array(
            'columns'           => 'ConvenioCategoria',
            'refTableClass'     => 'Rrhh_Model_DbTable_ConveniosCategorias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ConveniosCategorias',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'TiposDeJornadas' => array(
            'columns'           => 'TipoDeJornada',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeJornadas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeJornadas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'TiposDeBajas' => array(
            'columns'           => 'TipoDeBaja',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeBajas',
            'refJoinColumns'    => array('Descripcion','Indemniza'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeBajas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ModalidadesDeContrataciones' => array(
            'columns'           => 'ModalidadDeContratacion',
            'refTableClass'     => 'Rrhh_Model_DbTable_ModalidadesDeContrataciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Activo',
            'refTable'          => 'ModalidadesDeContrataciones',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ServiciosCalificacionesProfesionales' => array(
            'columns'           => 'CalificacionProfesional',
            'refTableClass'     => 'Rrhh_Model_DbTable_ServiciosCalificacionesProfesionales',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ServiciosCalificacionesProfesionales',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_dependentTables = array(
        'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesPuestos',
        'Rrhh_Model_DbTable_ServiciosFeriados'
    );

    /**
     * Validadores
     *
     * FechaBaja    -> mayor a fecha alta
     *
     */
    protected $_validators = array(
        'FechaBaja'=> array(
            array( 'GreaterThan',
                    '{FechaAlta}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        )
    );

    /**
     * Campos a tener en cuenta para el log de la liquidacion, son aquellos que pueden generar retroactivos
     */
    protected $_logLiquidcionCampos = array(    'Convenio',     'ConvenioCategoria',
                                                'Empresa',      'Persona',
                                                'TipoDeJornada'
    );

    /**
     * Campos de fechas a tener en cuenta (inicio, fin, cierre)
     */
    protected $_logLiquidcionFechas = array(    'fechaDesde' =>  'FechaAlta',
                                                'fechaHasta' =>  'FechaBaja',
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
    public function insert($data) {

        //throw new Rad_Db_Table_Exception(print_r($data,true));
        $this->_db->beginTransaction();
        try {

            $id = parent::insert($data);

            // Guardo el Log de Liquidaciones --------------------------------------------------
            $rowAct = $this->find($id)->current();
            $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_NL->asentarNovedad('I', $rowAct, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $id);
            // ----------------------------------------------------------- Fin Log Liquidaciones

            // Inserto el SSR
            if ($id) {
                $dataSSR = array(
                    'Persona'               => $data['Persona'],
                    'Servicio'              => $id,
                    'FechaInicio'           => $data['FechaAlta'],
        	        'FechaFin'           	=> $data['FechaBaja'],
                    'SituacionDeRevista'    => 1
                );

                $M_SSR = new Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas;

                $idSSR = $M_SSR->insert($dataSSR);
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

                $M_NL = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
                $M_SSR    = new Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas;

                foreach ($reg as $row) {

                    // Controlo que no cambie la persona
                    if (isset($data['Persona']) && $data['Persona'] != $row->Persona) throw new Rad_Db_Table_Exception('No se puede cambiar la Persona de un Servicio.');

                    if(isset($data['FechaBaja']) || $data['FechaBaja'] != $row->FechaBaja){
                        if(!$data['FechaBaja']){
                            $FechaBaja = '2099-12-31';
                        } else {
                            $FechaBaja = $data['FechaBaja'];
                        }
                        $R_SSR = $M_SSR->fetchAll("Servicio = ".$row->Id." AND FechaInicio >= '".$FechaBaja."'");

                        if(count($R_SSR)>0){
                            throw new Rad_Db_Table_Exception("Existen Situaciones De Revistas posteriores a la fecha de baja.");
                        } else {
                            $R_SSR = $M_SSR->fetchRow("Servicio = ".$row->Id,'FechaInicio Desc');
                            $R_SSR->FechaFin = $data['FechaBaja'];
                            $R_SSR->save();
                        }
                    }

                    parent::update($data,'Id ='.$row->Id);

                    // Guardo el Log de Liquidaciones --------------------------------------------------
                    $rowAct = $this->find($row->Id)->current();
                    $M_NL->asentarNovedad('U', $rowAct, $row, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $row->Id);
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

    /**
     * delete
     *
     * @param array $where  Registros que se deben eliminar
     */
    public function delete($where) {

        // No hace falta logear para liquidaciones debido a que no deja borrar si hay un recibo

        $reg = $this->fetchAll($where);

        if (count($reg)) {

            $M_RS = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
            $M_NL = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;

            foreach ($reg as $row) {

                // Verifico que no tengan recibos de sueldo
                $r = $M_RS->fetchAll("Servicio = ".$row->Id);
                if (count($r)) throw new Rad_Db_Table_Exception('No se puede eliminar un servicio cuando tiene liquidaciones de sueldo.');

                parent::delete('Id ='.$row->Id);

                // Guardo el Log de Liquidaciones --------------------------------------------------
                $M_NL->asentarNovedad('D', $row, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $row->Id);
                // ----------------------------------------------------------- Fin Log Liquidaciones

            }
        }
    }

    public function getServiciosPeriodo($periodo, $jerarquia, $valor, $liquidacion)
    {
        // Controlo que la jerarquia exista
        if (!in_array($jerarquia, self::$JERARQUIAS)) throw new Rad_Db_Table_Exception("La jerarquia $jerarquia no existe");

        $inicioPeriodo  = $periodo->getDesde()->format('Y-m-d');
        $finPeriodo     = $periodo->getHasta()->format('Y-m-d');
        //$where          = " FechaAlta <= '$finPeriodo' and ifnull(FechaBaja,'2199-01-01') >= '$inicioPeriodo' and Empresa = $liquidacion->Empresa";
        //Lo cambie para que se use las finales cuando sean finales
        $where          = " FechaAlta <= '$finPeriodo' and Empresa = $liquidacion->Empresa";

        // Para que en el caso de las liquidaciones definitivas no tome a aquellos que no trabajan mas.
        if ($liquidacion->TipoDeLiquidacion == 3) {
            $where = $where . " and FechaBaja is not null and FechaBaja <= '$finPeriodo' and FechaBaja >= '$inicioPeriodo' and TipoDeBaja in ( select Id from TiposDeBajas where ifnull(Indemniza,0) = 1) ";
        } else {
            $where = $where . " and (   ifnull(FechaBaja,'2199-01-01') >= '$finPeriodo'
                                        or
                                        ( FechaBaja is not null and FechaBaja <= '$finPeriodo' and FechaBaja >= '$inicioPeriodo' and TipoDeBaja in ( select Id from TiposDeBajas where ifnull(Indemniza,0) = 0))
                                    )";
        }

        switch ($jerarquia) {
            case 'SERVICIO':
                $valor  = $this->_db->quote($valor, 'INTEGER');
                $s      = $this->fetchAll($where . " and Id = $valor ");
                return $s;
                break;
            case 'EMPRESA':
                $s      = $this->fetchAll($where,'Id ASC');
                return $s;
                break;
            default:
                throw new Rad_Db_Table_Exception("La jerarquia $jerarquia no existe, esto no deberia pasar");
                break;
        }
    }

    public function fetchOrdenadoPorPersona($where = null, $order = null, $count = null, $offset = null)
    {
        //$condicion = "LibrosIVADetalles.TipoDeLibro = 1";
        //$where = $this->_addCondition($where, $condicion);

        $order =array( " Personas.RazonSocial asc ");

        return parent:: fetchAll($where, $order, $count, $offset);
    }


}
