<?php
class Liquidacion_Model_DbTable_VariablesDetallesAbstractas extends Rad_Db_Table
{
    protected $_name = 'VariablesDetalles';

    protected $_sort = array('Variable asc', 'FechaDesde desc');

    protected $_referenceMap    = array(

        'Variables' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesAbstractas',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'Variables',
            'refColumns'        => 'Id',
        ),
	    'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'			=> true,
        //    'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Convenios',
            'refColumns'        => 'Id',
        ),
	    'Empresas' => array(
            'columns'           => 'Empresa',
            'refTableClass'     => 'Base_Model_DbTable_Empresas',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'			=> true,
        //    'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Empresas',
            'refColumns'        => 'Id',
        ),
	    'GruposDePersonas' => array(
            'columns'           => 'GrupoDePersona',
            'refTableClass'     => 'Liquidacion_Model_DbTable_GruposDePersonas',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'			=> true,
        //    'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'GruposDePersona',
            'refColumns'        => 'Id',
        ),
	    'ConveniosCategorias' => array(
            'columns'           => 'ConvenioCategoria',
            'refTableClass'     => 'Rrhh_Model_DbTable_ConveniosCategorias',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'			=> true,
        //    'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'ConveniosCategorias',
            'refColumns'        => 'Id',
        ),
        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Persona'),
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'Servicio',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array(
        'Liquidacion_Model_DbTable_ConceptosTiposDeLiquidaciones',
        'Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles'
    );

    /**
     * Validadores
     *
     * FechaHasta    -> mayor a fechadesde
     *
     */
    protected $_validators = array(
        'FechaHasta'=> array(
            array( 'GreaterThan',
                    '{FechaDesde}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        )
    );

    /**
     * Campos a tener en cuenta para el log de la liquidacion, son aquellos que pueden generar retroactivos
     */
    protected $_logLiquidcionCampos = array(
        'Variable',         'Convenio',         'Empresa',      'ConvenioCategoria',
        'GrupoDePersona',   'Servicio',         'Formula',      'FormulaDetalle',
        'Selector',         'VariableJerarquia'
    );

   /**
     * Campos de fechas a tener en cuenta (inicio, fin, cierre)
     */
    protected $_logLiquidcionFechas = array(    'fechaDesde' =>  'FechaDesde',
                                                'fechaHasta' =>  'FechaHasta',
                                                'fechaCierre' =>  'FechaBaja'
    );

    /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = null;

    /**
     * Insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();
            /*
                OJO... VariableJerarquia es un valor default por lo tanto no viene en el data, pero es uno
                de los valores que necesitamos para referenciar en forma univoca a este registro.

                Por lo tanto los controles de superposicion los vamos a tener que hacer despues de insertar.
            */
            // inserto el registro
            $id          = parent::insert($data);
            $row         = $this->find($id)->current();
            $idJerarquia = $this->getIdJerarquia($row);
            // Verifico que no se superponga con otro periodo
            // $this->salirSi_superponeConOtroPeriodo($row);

            // Asiento la novedad en el log para liquidaciones
            $M_NL = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_NL->asentarNovedad('I', $row, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $idJerarquia);

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

            if (!count($reg))       throw new Rad_Db_Table_Exception('No se encuentran las Variables a modificar.');
            if ( count($reg) > 1 )  throw new Rad_Db_Table_Exception('Solo puede modificar una Variable a la vez.');

            $row = $reg->current();

            // No se puede modificar la jerarquia de una variable
            if (isset($data['VariableJerarquia']) && $data['VariableJerarquia'] != $row->VariableJerarquia) throw new Rad_Db_Table_Exception('No se puede modificar la jerarquia de una variable.');

            // Verifico que no cambie el Id de elemento de la Jerarquia
            $this->verificarCambioDeId($data,$row);

            // Verifico la consistencia de la fecha de alta y baja
            $FH = (isset($data['FechaHasta']) && $data['FechaHasta']) ? $data['FechaHasta'] : $row->FechaHasta;
            $FH = ($FH) ? $FH : '2999-01-01';
            $FD = (isset($data['FechaDesde'])) ? $data['FechaDesde'] : $row->FechaDesde;
            if ($FD > $FH) throw new Rad_Db_Table_Exception('La fecha de alta es mayor que la fecha de Baja.');

            // inserto el registro
            parent::update($data,"Id =".$row->Id);
            $rowU = $this->find($row->Id)->current();

            // Verifico que no se superponga con otro periodo
            //$this->salirSi_superponeConOtroPeriodo($row);

            // Asiento la novedad en el log para liquidaciones
            $idJerarquia = $this->getIdJerarquia($row);
            $M_NL = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_NL->asentarNovedad('U', $rowU, $row, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $idJerarquia);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * [delete description]
     * @param  [type] $where [description]
     * @return [type]        [description]
     */
    public function delete($where) {
        $reg = $this->fetchAll($where);
        if (!count($reg)) throw new Rad_Db_Table_Exception('No se encuentran las Variables a eliminar.');

        $M_NL = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
        foreach ( $reg as $row ) {
            // Asiento la novedad en el log para liquidaciones
            $idJerarquia = $this->getIdJerarquia($row);
            $M_NL->asentarNovedad('D', $row, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $idJerarquia);
            parent::delete("Id = $row->Id");
        }
    }

    public function getVariableDetallePeriodo($variableId, $periodo) {

        $fb         = $periodo->getHasta()->format('Y-m-d');
        $variableId = $this->_db->quote($variableId, 'INTEGER');
        $variable   = $this->fetchRow("Variable = $variableId AND FechaDesde <= '$fb' and ifnull(FechaHasta,'2199-01-01') >= '$fb' AND Historico <> 1", 'FechaDesde desc');

        return $variable;
    }

    /**
     * Retorna el id de una variable pasandole la variable detalle
     * @param  int      variableDetalleId   Identificador de VariablesDetalles
     * @return int                          Id de la variable
     */
    public function getVariableDesdeDetalle($variableDetalleId) {

        $variableDetalleId  = $this->_db->quote($variableDetalleId, 'INTEGER');
        $variable           = $this->_db->fetchOne("SELECT Variable FROM VariablesDetalles WHERE Id = $variableDetalleId");

        return $variable;
    }

    /**
     * Retorna el id del elemento de una Variable de la Jerarquia
     * @param  Rad_Db_Table_Row     $row    row de la tabla VariablesDetalles
     * @return int                          Id del elemento de la Jerarquia
     */
    public function getIdJerarquia($row) {
        switch ($row->VariableJerarquia) {
            case 1:     $R = $row->Servicio;            break;
            case 2:     $R = $row->GrupoDePersona;      break;
            case 3:     $R = $row->ConvenioCategoria;   break;
            case 4:     $R = $row->Empresa;             break;
            case 5:     $R = $row->Convenio;            break;
            default:    $R = null;                      break;
            //default:
            //    throw new Rad_Db_Table_Exception('Log de Novedades no puede ubicar el identificador del objeto jerarquico.');                      break;
        }
        //if ($row->VariableJerarquia != 6 && !$R) throw new Rad_Db_Table_Exception('Log de Novedades no puede ubicar el identificador del objeto jerarquico.');
        return $R;
    }

    /**
     * Verifica que no se hubiera cambiado el elemento al que apunta el registro
     * @param  array                $data   Datos a Verificar
     * @param  Rad_Db_Table_Row     $row    row con los valores que van a ser modificados
     * @return none
     */
    public function verificarCambioDeId($data,$row) {
        // No se debe modificar el identificador al que apunta la jerarquia
        if (isset($data['Servicio'])            && $data['Servicio'] != $row->Servicio)                     throw new Rad_Db_Table_Exception('No se puede modificar el Servicio al que apunta el concepto.');
        if (isset($data['GrupoDePersona'])      && $data['GrupoDePersona'] != $row->GrupoDePersona)         throw new Rad_Db_Table_Exception('No se puede modificar el Grupo al que apunta el concepto.');
        if (isset($data['ConvenioCategoria'])   && $data['ConvenioCategoria'] != $row->ConvenioCategoria)   throw new Rad_Db_Table_Exception('No se puede modificar la Categoria al que apunta el concepto.');
        if (isset($data['Empresa'])             && $data['Empresa'] != $row->Empresa)                       throw new Rad_Db_Table_Exception('No se puede modificar la Empresa al que apunta el concepto.');
        if (isset($data['Convenio'])            && $data['Convenio'] != $row->Convenio)                     throw new Rad_Db_Table_Exception('No se puede modificar el Convenio al que apunta el concepto.');
    }



    /**
     *
     *
     *   NO VA MAS CREO !  --> Pablo K
     *
     *
     *
     * Cierra el periodo anterior al ingresado a un dia antes de la fecha de inicio del nuevo periodo.
     * Tambien hace lo mismo en el caso que se modifique la fecha de inicio
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @param int   $Id     identificador del registro a modificar. No se usa en el insert.
     * @return none
    */
    private function cerrarPeriodoAnterior($data,$Id = null) {

        $FechaDesde_PA = null;

        $sql = "    SELECT  Id, FechaDesde
                    FROM    VariablesDetalles
                    WHERE   Variable            = {$data['Variable']}
                    AND     VariableJerarquia   = {$data['VariableJerarquia']}
                    AND     FechaDesde          < '{$data['FechaDesde']}'
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
                Rad_PubSub::publish('VariablesDetalles_Update', $rowU);
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
     * Revisa si el periodo ingresado no se superpone con uno existente
     * Esta funcion se ejecuta dentro de la transaccion de updat/insert pero despues de que
     * modifico o inserto el registro.
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @param int   $Id     identificador del registro a modificar. No se usa en el insert.
     * @return boolean
     */
    public function superponeConOtroPeriodo($row,$condicion = null) {

        $FH = ($row->FechaHasta) ? $row->FechaHasta : '2999-01-01';
        $FD = $row->FechaDesde;

        if($row->VariableJerarquia){
            $condicionJerarquia = " AND     VariableJerarquia   =   {$row->VariableJerarquia} ";
        }

        // controlo por si viene un null y lo cambio a un string vacio
        $condicion = ($condicion) ? $condicion : "";

        $sql = "    SELECT  Id
                    FROM    VariablesDetalles
                    WHERE   Variable            =   {$row->Variable}
                    AND     Historico           = 0
                    AND     Id                  <>  {$row->Id}
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
                    $condicion
                    $condicionJerarquia
                    limit 1";

        $R = $this->_db->fetchRow($sql);

        if ($R) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sale si el periodo ingresado no se superpone con uno existente
     *
     * @param row $row   Registro insertado o modificado
     * @return boolean
     */
    public function salirSi_superponeConOtroPeriodo($row, $condicion) {
        if ($this->superponeConOtroPeriodo($row, $condicion)) throw new Rad_Db_Table_Exception('El periodo ingresado se superpone con otro periodo existente.');
    }




}
