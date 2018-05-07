<?php

class Base_Model_DbTable_Articulos extends Rad_Db_Table
{
    protected $_name = 'Articulos';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap    = array(
        'Marcas' => array(
            'columns'           => 'Marca',
            'refTableClass'     => 'Base_Model_DbTable_Marcas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Marcas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'UnidadesDeMedidas' => array(
            'columns'           => 'UnidadDeMedida',
            'refTableClass'     => 'Base_Model_DbTable_UnidadesDeMedidas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'UnidadesDeMedidas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'UnidadesDeMedidasProduccion' => array(
            'columns'           => 'UnidadDeMedidaDeProduccion',
            'refTableClass'     => 'Base_Model_DbTable_UnidadesDeMedidas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'UnidadesDeMedidas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'ConceptosImpositivos' => array(
            'columns'           => 'IVA',
            'refTableClass'     => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/EsIva',
            'refTable'          => 'ConceptosImpositivos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'ArticulosSubGrupos' => array(
            'columns'           => 'ArticuloSubGrupo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosSubGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosSubGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'TiposDeControlesDeStock' => array(
            'columns'           => 'TipoDeControlDeStock',
            'refTableClass'     => 'Almacenes_Model_DbTable_TiposDeControlesDeStock',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeControlesDeStock',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'ArticulosGrupos' => array(
            'columns'           => 'ArticuloGrupo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'TiposDeArticulos' => array(
            'columns'           => 'Tipo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeArticulos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeArticulos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'PlanesDeCuentas' => array(
            'columns'           => 'Cuenta',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array('Jerarquia', 'Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Articulo',
            'refTable'          => 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_dependentTables = array(
        'Base_Model_DbTable_ArticulosListasDePreciosDetalle',
        'Almacenes_Model_DbTable_ArticulosStock',
        'Base_Model_DbTable_ArticulosVersiones',
        'Facturacion_Model_DbTable_ComprobantesDetalles',
        'Almacenes_Model_DbTable_Lotes',
        'Almacenes_Model_DbTable_LotesDeTerceros',
        'Produccion_Model_DbTable_OrdenesDeProducciones',
        'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles'
    );

    public function createRow(array $data = array(), $defaultSource = null)
    {

        $row = parent::createRow($data, $defaultSource);

        $row->Cuenta = 399;

        return $row;
    }

    /**
     * Inserta un Articulo
     * @param array $data Datos
     */
    public function insert($data)
    {
        if ($data['Tipo'] == 2) { // Es Servicio
            $this->_validarTieneIva($data);
            $this->_validarTieneMarca($data, true);
            $this->_validarTieneControlStock($data, true);
            $this->_validarTieneUnidadDeMedida($data, true);
        }

        $id = parent::insert($data);

        $this->_generarVersion($id, 'Version Inicial');

        return $id;
    }

    /**
     * Genera la version inicial de un articulo
     * @param int $id Id del Articulo
     */
    protected function _generarVersion($id, $desc)
    {
        /**
         * Generamos la primer version del articulo
         */
        $ArticulosVersiones = new Base_Model_DbTable_ArticulosVersiones();

        $version = $ArticulosVersiones->createRow();
        $version->Descripcion = $desc;
        $version->Articulo    = $id;
        $version->Fecha       = date('Y-m-d');

        $version->save();

        return $version;
    }

    public function getCantidadProductoArticulo($idArticulo)
    {
        throw new Rad_Db_Table_Exception('Articulos getCantidadProductoArticulo deprecated');
    }

    protected function _validarTieneControlStock($data, $inv = false)
    {
        $cond = !$data['TipoDeControlDeStock'];

        if ($inv) $cond = !$cond;

        if ($cond) {
            throw new Rad_Db_Table_Exception(ucfirst(( ($inv)?'no ':'' ).'debe definir un control de stock'));
        }
    }

    protected function _validarTieneUnidadDeMedida($data, $inv = false)
    {
        $cond = !$data['UnidadDeMedida'];

        if ($inv) $cond = !$cond;

        if ($cond) {
            throw new Rad_Db_Table_Exception(ucfirst(( ($inv)?'no ':'' ).'debe definir una unidad de medida'));
        }
    }

    protected function _validarTieneIva($data, $inv = false)
    {
        $cond = !$data['IVA'];

        if ($inv) $cond = !$cond;

        if ($cond) {
            throw new Rad_Db_Table_Exception(ucfirst(( ($inv)?'no ':'' ).'debe definir un tipo de IVA'));
        }
    }

    protected function _validarTieneMarca($data, $inv = false)
    {
        $cond = !$data['Marca'];

        if ($inv) $cond = !$cond;

        if ($cond) {
            throw new Rad_Db_Table_Exception(ucfirst((($inv)?'no ':'' ).'debe definir una Marca'));
        }
    }

    protected function _validarEsParaVenta($data, $inv = false)
    {
        $cond = $data['EsParaVenta'];

        if ($inv) $cond = !$cond;

        if (!$cond) {
            throw new Rad_Db_Table_Exception( ($inv)?'El Articulo no puede ser para venta.':'El Articulo debe ser para venta.');
        }
    }

    protected function _validarEsParaCompra($data, $inv = false)
    {
        $cond = $data['EsParaCompra'];

        if ($inv) $cond = !$cond;

        if (!$cond) {
            throw new Rad_Db_Table_Exception( ($inv)?'El Articulo no puede ser para compra.':'El Articulo debe ser para compra.');
        }
    }

    /**
     * Retorna la estructura de un articulo
     */
    public function getEstructuraArbol($ArticuloVersion, $MostrarFormula = null) {

        if (!$ArticuloVersion) {
            // Error no mando nada
            throw new Rad_Db_Table_Exception('Error, parametro faltante: se requiere el ArticuloVersion');
        }

        $ArticuloVersion = $this->_db->quote($ArticuloVersion, 'INTEGER');

        // Recupero la raiz del arbol
        $sql    = "select      A.Descripcion           as Articulo,
                                A.Id                    as ArticuloId,
                                A.ArticuloGrupo         as GrupoId,
                                AG.Descripcion          as Grupo,
                                A.ArticuloSubGrupo      as SubGrupoId,
                                ASG.Descripcion         as SubGrupo,
                                A.EsMateriaPrima,
                                1                       as Cantidad,
                                A.UnidadDeMedida        as UnidadId,
                                UM.Descripcion          as Unidad,
                                UM.DescripcionR         as UnidadR,
                                UM.TipoDeUnidad         as TipoDeUnidad,
                                AV.TieneFormula,
                                AV.Id                   as ArticuloVersionId
                    from        Articulos A
                    inner join  ArticulosVersiones AV       on AV.Articulo = A.Id
                    inner join  UnidadesDeMedidas UM        on UM.Id    = A.UnidadDeMedida
                    left join   ArticulosGrupos AG          on AG.Id    = A.ArticuloGrupo
                    left join   ArticulosSubGrupos ASG      on ASG.Id   = A.ArticuloSubGrupo
                    where       AV.Id = $ArticuloVersion
                    limit 1";


        $raiz  = $this->_db->fetchRow($sql);

        if (count($raiz)) {

            $nodoRaiz =  array( 'ArticuloId'        => $raiz['ArticuloId'],
                                'ArticuloVersionId' => $raiz['ArticuloVersionId'],
                                'ArticuloDesc'      => $raiz['Articulo'],
                                'Cantidad'          => $raiz['Cantidad'],
                                'CantidadTotal'     => $raiz['Cantidad'],
                                'UnidadDeMedidaId'  => $raiz['UnidadId'],
                                'UnidadDeMedida'    => $raiz['Unidad'],
                                'UnidadDeMedidaR'   => $raiz['UnidadR'],
                                'TipoDeUnidad'      => $raiz['TipoDeUnidad'],
                                'GrupoId'           => $raiz['GrupoId'],
                                'Grupo'             => $raiz['Grupo'],
                                'SubGrupoId'        => $raiz['SubGrupoId'],
                                'SubGrupo'          => $raiz['SubGrupo'],
                                'MateriaPrima'      => 0,
                                'EsContenedor'      => 1,
                                'TieneFormula'      => $raiz['TieneFormula'],
                                'TipoDeRelacionArticulo' => null // no deberia darse nunca preguntar a pablo porq lo puso
            );
            $desglose[]     = $nodoRaiz;
            unset($nodoRaiz['EsContenedor']);

            if (!$MostrarFormula && $raiz['TieneFormula'] == 1 ) {
            } else {
                $this->_articulosRecorrerArbol($ArticuloVersion,$ArticuloVersion,1,$desglose,$nodoRaiz,$MostrarFormula);
            }

            $arbol = $nodoRaiz;

        }

        /* Recorro el arbol y separo el Producto y su unidad */
        foreach ($desglose as $row) {
            if ($row['MateriaPrima'] == 1 && (!$row['TipoDeRelacionArticulo'] || $row['TipoDeRelacionArticulo'] == 2)) {
                $producto               = $row['ArticuloId'];
                $productoDescripcion    = $row['ArticuloDesc'];
                $productoUM             = $row['UnidadDeMedidaId'];
                $productoUMD            = $row['UnidadDeMedida'];
                $productoUMR            = $row['UnidadDeMedidaR'];
                $productoTUM            = $row['TipoDeUnidad'];
                $productoCantTotal      = $row['CantidadTotal'];
            }
        }

        $return = array(
            'arbol'                 => $arbol,
            'desglose'              => $desglose,
            'producto'              => $producto,
            'productoDescripcion'   => $productoDescripcion,
            'productoUM'            => $productoUM,
            'productoUMD'           => $productoUMD,
            'productoUMR'           => $productoUMR,
            'productoTipoUM'        => $productoTUM,
            'productoCantTotal'     => $productoCantTotal
        );
        return $return;
    }


    /**
     * Recorre un arbol de Articulos y retorna los datos necesarios para produccion como asi tambien
     * el dibujo del arbol en formato html y json
     *
     * @param int   $ArticuloVersion      identificador del ArticuloVersion
     * @param array $arbol                datos del Arbol
     * @param array $arbolDibujo          arbol dibujado en html y json
     *
     * @return array
     */
    protected function _articulosRecorrerArbol($ArticuloVersionRaiz, $ArticuloVersion, $multiplicador,&$desglose,&$arbol,$MostrarFormula = null)
    {
        $cantidadAcumulada  = 1;

        // get all records from database whose parent is $id
        $sql    = " select      distinct

                                AH.Descripcion          as Articulo,
                                AH.Id                   as ArticuloId,
                                AH.ArticuloGrupo        as GrupoId,
                                AG.Descripcion          as Grupo,
                                AH.ArticuloSubGrupo     as SubGrupoId,
                                ASG.Descripcion         as SubGrupo,
                                AH.EsMateriaPrima       as MatPrima,
                                AVD.Cantidad,
                                AVD.UnidadDeMedida      as UnidadId,
                                UM.Descripcion          as Unidad,
                                UM.DescripcionR         as UnidadR,
                                UM.TipoDeUnidad         as TipoDeUnidad,
                                AVD.ArticuloVersionPadre,
                                AVD.ArticuloVersionHijo,
                                AVH.TieneFormula,
                                AVD.TipoDeRelacionArticulo
                    from        ArticulosVersionesDetalles AVD
                    inner join ArticulosVersiones AVH   on AVH.Id   = AVD.ArticuloVersionHijo
                    inner join Articulos AH             on AH.Id    = AVH.Articulo
                    inner join UnidadesDeMedidas UM     on UM.Id    = AVD.UnidadDeMedida
                    left join ArticulosGrupos AG        on AG.Id    = AH.ArticuloGrupo
                    left join ArticulosSubGrupos ASG    on ASG.Id   = AH.ArticuloSubGrupo
                    where       AVD.ArticuloVersionPadre    = $ArticuloVersion
                    order by AVD.ArticuloVersionPadre ";
        //Rad_Log::debug($sql);
        $ramas  = $this->_db->fetchAll($sql);

        if (count($ramas)) {

            // Si tiene hijos es un contenedor
            end($desglose);
            $RegPadre = &$desglose[key($desglose)];
            $RegPadre['EsContenedor'] = 1 ;

            foreach ($ramas as $row)
            {
                $cantidadAcumulada  = $multiplicador * $row['Cantidad'];

                /* Agrego la rama al arbol*/
                $ramaX = array(     'ArticuloId'        => $row['ArticuloId'],
                                    'ArticuloVersionId' => $row['ArticuloVersionHijo'],
                                    'ArticuloDesc'      => $row['Articulo'],
                                    'Cantidad'          => $row['Cantidad'],
                                    'CantidadTotal'     => $cantidadAcumulada,
                                    'UnidadDeMedidaId'  => $row['UnidadId'],
                                    'UnidadDeMedida'    => $row['Unidad'],
                                    'UnidadDeMedidaR'   => $row['UnidadR'],
                                    'TipoDeUnidad'      => $row['TipoDeUnidad'],
                                    'GrupoId'           => $row['GrupoId'],
                                    'Grupo'             => $row['Grupo'],
                                    'SubGrupoId'        => $row['SubGrupoId'],
                                    'SubGrupo'          => $row['SubGrupo'],
                                    'MateriaPrima'      => $row['MatPrima'],
                                    'EsContenedor'      => 0,
                                    'TieneFormula'      => $row['TieneFormula'],
                                    'TipoDeRelacionArticulo' => $row['TipoDeRelacionArticulo']
                );

                $desglose[]         = $ramaX;
                unset($ramaX['EsContenedor']);

                /* Llamo para que busque los hijos */
                if ($MostrarFormula || $row['TieneFormula'] != 1 ) {
                    // Veo que no siga navegando las formulas de los componentes de una formula que tienen a su vez formula
                    if ($row['TipoDeRelacionArticulo'] != 1) {
                        $r = $this->_articulosRecorrerArbol($ArticuloVersionRaiz, $row['ArticuloVersionHijo'],$multiplicador * $row['Cantidad'],$desglose,$ramaX,$MostrarFormula);
                    }
                }

                $arbol['Hijos'][]   = $ramaX;

            }
        }
    }

    /**
     * Retorna la cantidad de producto que tiene un articulo
     * @param int     $idArtVer
     * @param numeric $cant            cantidad de articulos (para multiplicar)
     * @param int     $unidadDemMedida unidad para convertir el resultado
     * @param bool    $conUm           si es true se agregara la descripcion reducida de la unidad de medida
     */
    public function getCantidadProducto($idArtVer, $cant=1, $unidadDeMedida=null, $conUm=false)
    {
        $r = $this->getEstructuraArbol($idArtVer);

        $cantidad = $r['productoCantTotal']*$cant;

        // de ser necesario convierto la unidad de medida
        if ($unidadDeMedida && $unidadDeMedida != $r['productoUM']) {
            $um = Service_TableManager::get('Base_Model_DbTable_UnidadesDeMedidas');
            // $um = new Base_Model_DbTable_UnidadesDeMedidas;
            $cantidad = $um->convert($cantidad, (int)$r['productoUM'], $unidadDeMedida, $conUm);
        } else {
            if ($conUm) {
                $cantidad .= ' '.$r['productoUMR'];
            }
        }

        return $cantidad;
    }

    /**
     * Retorna los articulosversion que contienen este producto, que son insumos y finales
     *
     * @param int $productoVersion id de articuloversion del producto
     */
    public function getArticulosVersionesPorProductoVersion($productoVersion, $soloInsumos = false)
    {
        $productoVersion = $this->_db->quote($productoVersion, 'INTEGER');

        $q = '';

        if ($soloInsumos) $q = 'AND a.EsInsumo = 1';

        $artVer = $this->_db->fetchCol(
            "SELECT DISTINCT avr.ArticuloVersionRaiz as ArticuloVersion
            FROM ArticulosVersionesDetalles AVD
            INNER JOIN ArticulosVersionesRaices avr ON AVD.Id = avr.ArticuloVersionDetalle
            INNER JOIN ArticulosVersiones av    ON avr.ArticuloVersionRaiz = av.Id
            INNER JOIN Articulos a              ON av.Articulo = a.id
            WHERE AVD.ArticuloVersionHijo = $productoVersion
            AND  AVD.TipoDeRelacionArticulo = 2 AND a.EsFinal = 1 $q"
        );

        return $artVer;
    }

    public function fetchEsArticuloParaCompra($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsParaCompra = 1 AND Articulos.Tipo = 1 AND EnDesuso = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsServicioParaCompra($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsParaCompra = 1 AND Articulos.Tipo = 3 AND EnDesuso = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsArticuloParaVenta($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsParaVenta = 1 AND Articulos.Tipo = 1 AND EnDesuso = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsServicioParaVenta($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsParaVenta = 1 AND Articulos.Tipo = 3 AND EnDesuso = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsProducido($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsProducido = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsInsumo($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsInsumo = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsParaProduccion($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.EsInsumo = 1 and Articulos.EsFinal = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }
}