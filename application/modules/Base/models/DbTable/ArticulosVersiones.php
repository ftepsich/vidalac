<?php

class Base_Model_DbTable_ArticulosVersiones extends Rad_Db_Table
{
    protected $_name = 'ArticulosVersiones';

    protected $_sort = array('Articulo_cdisplay asc');

    protected $_referenceMap    = array(
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_Articulos',
            'refJoinColumns'    => array('Descripcion','Codigo'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Articulos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_dependentTables = array(
        'Base_Model_DbTable_ArticulosVersionesDetalles',
    );

    /**
     * Inserta un ArticuloVersion
     * @param array $data Datos
     */
    public function insert($data)
    {
        $data['Version'] = $this->_getNroVersion($data['Articulo']);
        $id = parent::insert($data);
        return $id;
    }

    /**
     * Retorna el siguiente nro de version para un articulo
     * @param int $idArticulo id articulo
     */
    public function _getNroVersion($idArticulo)
    {
        $vernum = count($this->fetchAll("Articulo = $idArticulo"));
        return $vernum + 1;
    }

    /**
     * Agrega un articulo como componente
     *
     * @param mixed $articuloVersion     Id del ArticuloVersion al que se le agregara el detalle o row de ArticuloVersion
     * @param int   $articuloVersionHijo Id del Articulo
     * @param float $cantidad            cantidad del Articulo
     * @param int   $proceso             Id del Articulo
     */
    // public function agregarDetalle($articuloVersion, $articuloVersionHijo, $cantidad, $proceso = null, $unidadDeMedida = null)
    // {
    //     if ($articuloVersion instanceof Rad_Db_Table_Row) {
    //         if (! ($articuloVersion->getTable() instanceof Base_Model_DbTable_ArticulosVersiones) ) {
    //             throw new Rad_Db_Table_Exception("El Row pasado como parametro debe ser del modelo ArticulosVersiones");
    //         }

    //         $idArtVer = $articuloVersion->Id;

    //     } else {
    //         if (!is_int($articuloVersion)) {
    //             throw new Rad_Db_Table_Exception("El parametro articuloVersion debe ser un int o row de ArticulosVersiones");
    //         }
    //         $idArtVer = $articuloVersion;

    //         $articuloVersion = $this->find($idArtVer)->current();

    //         if (!$articuloVersion) {
    //             throw new Exception(
    //                 "No se encontro el articulo de la Version id: $idArtVer"
    //             );
    //         }
    //     }

    //     $ArticulosVersionesDetalles = new Base_Model_DbTable_ArticulosVersionesDetalles;

    //     $detalle = $ArticulosVersionesDetalles->createRow();

    //     $detalle->ArticuloVersionHijo  = $articuloVersionHijo;
    //     $detalle->ArticuloVersionPadre = $idArtVer;
    //     $detalle->Cantidad             = $cantidad;
    //     $detalle->Proceso              = $proceso;
    //     $detalle->UnidadDeMedida       = $unidadDeMedida;

    //     if ($articuloVersion->ArticuloVersion) {
    //         // Un hijo (rama) mas
    //         $detalle->ArticuloVersion = $articuloVersion->ArticuloVersion;
    //     } else {
    //         // Raiz del arbol
    //         $detalle->ArticuloVersion = $idArtVer;
    //     }

    //     $detalle->save();

    //     return $detalle;
    // }


    /**
     * Agrega un articulo como componente
     *
     * @param mixed $articuloVersion     Id del ArticuloVersion al que se le agregara el detalle o row de ArticuloVersion
     */
    public function clonarVersion($articuloVersion)
    {
        try
        {
            $this->_db->beginTransaction();

            // valido y obtengo ArticuloVersion
            if ($articuloVersion instanceof Rad_Db_Table_Row) {

                if (! ($articuloVersion->getTable() instanceof Base_Model_DbTable_ArticulosVersiones) ) {
                    throw new Rad_Db_Table_Exception(
                        "El Row pasado como parametro debe ser del modelo ArticulosVersiones"
                    );
                }

            } else {

                if (!is_int($articuloVersion)) {
                    throw new Rad_Db_Table_Exception(
                        "El parametro articuloVersion debe ser un int o row de ArticulosVersiones"
                    );
                }

                $articuloVersion = $this->find($articuloVersion)->current();

                if (!$articuloVersion) {
                    throw new Rad_Db_Table_Exception(
                        "ArticulosVersiones: no se encontro el articuloVersion al que quiere agregarle el detalle."
                    );
                }
            }

            // Clonamos el registro de ArticuloVersion
            $datos = $articuloVersion->toArray();

            unset($datos['Id']);

            $nuevaVersion = $this->createRow($datos);
            $idNuevo      = $nuevaVersion->save();

            if (!$idNuevo) {
                throw new Rad_Db_Table_Exception("Error al clonar la version.");
            }

            // Clonamos el detalle de la version
            $ArticulosVersionesDetalles = new Base_Model_DbTable_ArticulosVersionesDetalles;

            $detalles = $ArticulosVersionesDetalles->fetchAll("ArticuloVersionPadre = $articuloVersion->Id");

            foreach ($detalles as $det) {

                $datosD = $det->toArray();
                unset($datosD['Id']);
                $datosD['ArticuloVersionPadre'] = $idNuevo;

                $clonDet = $ArticulosVersionesDetalles->createRow($datosD);
                $idDet = $clonDet->save();

                if (!$idDet) {
                    throw new Rad_Db_Table_Exception("Error al clonar el detalle de la version.");
                }

            }

            $this->_db->commit();

        } catch(Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchEsProducto($where = null, $order = null, $count = null, $offset = null)
    {
        // Joineo con areas de trabajo
        // $joiner = $this->getJoiner();

        // $joiner->with('Articulos')
        //        ->joinRef('Articulos', array('ArticuloGrupo','EsMateriaPrima') );

        $where = $this->_addCondition($where, "Articulos.ArticuloGrupo <> 1 and Articulos.EsMateriaPrima = 1");

        // llamo al local para q me agrege el filtro de Tipo
        return self::fetchAll($where, $order, $count, $offset);
    }


    public function fetchEsPackaging($where = null, $order = null, $count = null, $offset = null)
    {
        // Joineo con areas de trabajo
        // $joiner = $this->getJoiner();

        // $joiner->with('Articulos')
        //        ->joinRef('Articulos', array('ArticuloGrupo','EsMateriaPrima') );

        $where = $this->_addCondition($where, "Articulos.ArticuloGrupo = 1 and Articulos.EsMateriaPrima = 1");

        // llamo al local para q me agrege el filtro de Tipo
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsArticulo($where = null, $order = null, $count = null, $offset = null)
    {
        // Joineo con areas de trabajo
        // $joiner = $this->getJoiner();

        // $joiner->with('Articulos')
        //        ->joinRef('Articulos', array('ArticuloGrupo','EsMateriaPrima') );

        $where = $this->_addCondition($where, "(Articulos.ArticuloGrupo <> 1 or Articulos.ArticuloGrupo is null) and (Articulos.EsMateriaPrima = 0 or Articulos.EsMateriaPrima is null)");

        // llamo al local para q me agrege el filtro de Tipo
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsArticuloInsumo($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "(Articulos.ArticuloGrupo <> 1 or Articulos.ArticuloGrupo is null) and (Articulos.EsMateriaPrima = 0 or Articulos.EsMateriaPrima is null) and Articulos.EsInsumo = 1 and Articulos.EsFinal = 1");

        // llamo al local para q me agrege el filtro de Tipo
        return self::fetchAll($where, $order, $count, $offset);
    }

    /**
     * NO TIENE QUE PODER LISTAR OTRA COSA Q NO SEA UN ARTICULO GENERICO
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        if ($this->_fetchWithAutoJoins){
            $where = $this->_addCondition($where, 'Articulos.Tipo = 1');
        }

        return parent:: fetchAll($where, $order, $count, $offset);
    }

}