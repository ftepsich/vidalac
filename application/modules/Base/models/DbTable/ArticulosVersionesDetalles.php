<?php
class Base_Model_DbTable_ArticulosVersionesDetalles extends Rad_Db_Table
{
    protected $_name = 'ArticulosVersionesDetalles';

    protected $_referenceMap    = array(

        'ArticulosVersiones' => array(
            'columns'           => 'ArticuloVersionPadre',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosVersiones',
            //'refJoinColumns'    => array('Descripcion'),
            //'comboBox'          => true,
            //'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosVersiones',
            'refColumns'        => 'Id',
        ),
        'ArticulosVersionesHijo' => array(
            'columns'           => 'ArticuloVersionHijo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosVersiones',
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
        ),
        'TiposDeRelacionesArticulos' => array(
            'columns'           => 'TipoDeRelacionArticulo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeRelacionesArticulos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/NoFormula',
            'refTable'          => 'TiposDeRelacionesArticulos',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array();

    public function init()
    {

        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $joiner = $this->getJoiner();

            $joiner->with('ArticulosVersionesHijo')
               ->joinRef('Articulos', array('Descripcion','Codigo') );
        }
        /*
        $this->addAutoJoin(
            'Articulos',
            'Articulos.Id = ArticulosVersionesHijo.Articulo',
            array(
                'ArticuloHijo' => 'Articulos.Descripcion'
            )
        );
        */
    }


    /**
     * Inserta un ArticuloVersion
     * @param array $data Datos
     */
    public function insert($data)
    {
        try
        {
            $this->_db->beginTransaction();

            // Controlo que no quiera insertar elementos a una formula
            if($data['TipoDeRelacionArticulo'] == 1){
                throw new Rad_Db_Table_Exception('No se puede insertar elementos de una formula desde esta ventana.');
            }

            if (!$data['UnidadDeMedida']) {
               $data['UnidadDeMedida'] = $this->_getUnidadDeMedidaDeArticulo($data['ArticuloVersionPadre']);
            }

            if($data['ArticuloVersionPadre'] == $data['ArticuloVersionHijo']){
                throw new Rad_Db_Table_Exception('La version del Articulo no puede ser igual a la seleccionada anteriormente.');
            }

            // Veo si el padre tiene formula, de ser asi no dejo tocar nada
            $ArticulosVersiones = Service_TableManager::get('Base_Model_DbTable_ArticulosVersiones');
            $row = $ArticulosVersiones->find($data['ArticuloVersionPadre'])->current();
            if ($row && $row->TieneFormula == 1) {
                throw new Rad_Db_Table_Exception('No se puede insertar/modificar elementos de una formula desde esta ventana.');
            }


            if($this->salirSiExistePackaging($data['ArticuloVersionPadre'],$data['ArticuloVersionHijo'])){
                throw new Rad_Db_Table_Exception('Ya existe un packaging en esta version.');
            }

            if($this->salirSiExisteProducto($data['ArticuloVersionPadre'],$data['ArticuloVersionHijo'])){
                throw new Rad_Db_Table_Exception('Ya existe un producto en la escructura de esta version.');
            }

            if($this->salirSiExisteArticulo($data['ArticuloVersionPadre'],$data['ArticuloVersionHijo'])){
                throw new Rad_Db_Table_Exception('Ya existe el articulo en la escructura de esta version.');
            }

            $id = parent::insert($data);

            $this->getCompletarArticulosVersionesRaices($id,$data['ArticuloVersionPadre']);

            $this->_db->commit();
            return $id;
        } catch(Exception $e) {
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
    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {


                // Controlo que no quiera modificar elementos a una formula
                if($data['TipoDeRelacionArticulo'] == 1) {
                    throw new Rad_Db_Table_Exception('No se puede insertar/modificar elementos de una formula desde esta ventana.');
                }

                // Veo si antes era un elemento de una formula y ahora lo cambiaron por otra cosa
                $ra = $this->find($row->Id)->current();
                if ($ra->TipoDeRelacionArticulo == 1) {
                    throw new Rad_Db_Table_Exception('No se puede insertar/modificar elementos de una formula desde esta ventana.');
                }

                // Veo si el padre tiene formula, de ser asi no dejo tocar nada
                $ra = $row->findParentRow('Base_Model_DbTable_ArticulosVersiones', 'ArticulosVersiones');
                if ($ra->TieneFormula == 1) {
                    throw new Rad_Db_Table_Exception('No se puede insertar/modificar elementos de una formula desde esta ventana.');
                }

                if($data['ArticuloVersionHijo'] && $row->ArticuloVersionHijo != $data['ArticuloVersionHijo']){
                    $avh = $data['ArticuloVersionHijo'];

                    if($this->salirSiExistePackaging($row->ArticuloVersionPadre,$avh)){
                        throw new Rad_Db_Table_Exception('Ya existe un packaging en esta version.');
                    }

                    if($this->salirSiExisteProducto($row->ArticuloVersionPadre,$avh)){
                        throw new Rad_Db_Table_Exception('Ya existe un producto en la escructura de esta version.');
                    }

                    if($this->salirSiExisteArticulo($row->ArticuloVersionPadre,$avh)){
                        throw new Rad_Db_Table_Exception('Ya existe el articulo en la escructura de esta version.');
                    }
                }
            }

            parent::update($data, $where);

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Borra los registros indicados
     *
     * @param array $where
     *
     */
    public function delete($where)
    {

        try {
            $this->_db->beginTransaction();

            $M_AVR = new Base_Model_DbTable_ArticulosVersionesRaices;

            $reg = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg)) {
                foreach ($reg as $row) {
                    $M_AVR->delete("ArticuloVersionDetalle = ".$row['Id']);
                    parent::delete("Id =" . $row['Id']);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    protected function _getUnidadDeMedidaDeArticulo($idArticuloVersion)
    {
        $ArticulosVersiones = Service_TableManager::get('Base_Model_DbTable_ArticulosVersiones');
        $articuloVersion = $ArticulosVersiones->find($idArticuloVersion)->current();

        $articulo = $articuloVersion->findParentRow('Base_Model_DbTable_Articulos');

        if ($articulo->UnidadDeMedidaDeProduccion) {
            return $articulo->UnidadDeMedidaDeProduccion;
        } else {
            return $articulo->UnidadDeMedida;
        }
    }

    /**
     * Obtiene el ArticuloVersion que contiene el padre
     * @param array $data Datos
     */
    public function getCompletarArticulosVersionesRaices($avd,$avp)
    {
        $ArticulosVersionesRaices = Service_TableManager::get('Base_Model_DbTable_ArticulosVersionesRaices');
        $relacion = $ArticulosVersionesRaices->createRow();

        $relacion->ArticuloVersionRaiz      = $avp;
        $relacion->ArticuloVersionDetalle   = $avd;

        $relacion->save();

        $sqlrel = "SELECT avr.ArticuloVersionRaiz
                    FROM ArticulosVersionesDetalles avd
                        INNER JOIN ArticulosVersionesRaices avr ON avd.Id = avr.ArticuloVersionDetalle
                    WHERE avd.ArticuloVersionHijo = $avp ORDER BY avr.ArticuloVersionRaiz";

        $relaciones = $this->_db->fetchAll($sqlrel);

        foreach ($relaciones as $row){
            $relacion = $ArticulosVersionesRaices->createRow();
            $relacion->ArticuloVersionRaiz      = $row['ArticuloVersionRaiz'];
            $relacion->ArticuloVersionDetalle   = $avd;

            $relacion->save();
        }
    }

    /**
     * controla que en esa version no exista ya un packaging al mismo nivel
     * @param $avp articulo version padre
     * @param $avp articulo version hijo
     */
    public function salirSiExistePackaging($avp,$avh)
    {

        $sqlpack = "SELECT avd.Id
                FROM ArticulosVersionesDetalles avd
                    INNER JOIN ArticulosVersiones av  ON avd.ArticuloVersionHijo = av.Id
                    INNER JOIN Articulos a     ON av.Articulo = a.id
                WHERE avd.ArticuloVersionPadre = $avp AND a.EsMateriaPrima = 1 AND a.ArticuloGrupo = 1
                UNION
                SELECT av1.Id
                FROM ArticulosVersiones av1
                    INNER JOIN Articulos a1 ON av1.Articulo = a1.id
                WHERE av1.Id = $avh AND a1.EsMateriaPrima = 1 AND a1.ArticuloGrupo = 1";

        $packaging = $this->_db->fetchAll($sqlpack);

        if (count($packaging) > 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * controla que en el arbol del articulo exista solo un producto
     * @param $avp articulo version padre
     * @param $avp articulo version hijo
     */
    public function salirSiExisteProducto($avp,$avh)
    {

        $sqlprod = "SELECT DISTINCT avr.ArticuloVersionDetalle
                FROM ArticulosVersionesRaices avr
                    INNER JOIN ArticulosVersionesDetalles avd ON avr.ArticuloVersionDetalle = avd.Id
                    INNER JOIN ArticulosVersiones av  ON avd.ArticuloVersionHijo = av.Id
                    INNER JOIN Articulos a     ON av.Articulo = a.id
                WHERE avr.ArticuloVersionRaiz IN (SELECT DISTINCT ArticuloVersionRaiz FROM ArticulosVersionesRaices WHERE ArticuloVersionDetalle IN ( SELECT DISTINCT Id FROM ArticulosVersionesDetalles WHERE (ArticuloVersionPadre = $avp OR ArticuloVersionHijo =  $avp)))
                AND a.EsMateriaPrima = 1 AND a.ArticuloGrupo <> 1 AND avd.TipoDeRelacionArticulo <> 1
                UNION
                SELECT av1.Id
                FROM ArticulosVersiones av1
                    INNER JOIN Articulos a1 ON av1.Articulo = a1.id
                WHERE av1.Id = $avh AND a1.EsMateriaPrima = 1 AND a1.ArticuloGrupo <> 1";

        $producto = $this->_db->fetchAll($sqlprod);

        if (count($producto) > 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * controla que en el arbol del articulo no se haga recursivo es decir no existan asociado dos articulos iguales
     * @param $av articulo version
     */
    public function salirSiExisteArticulo($avp,$avh)
    {

        $sqlart = "SELECT DISTINCT av.Id
                FROM ArticulosVersionesRaices avr
                INNER JOIN ArticulosVersionesDetalles avd ON avr.ArticuloVersionDetalle = avd.Id
                INNER JOIN ArticulosVersiones av  ON avd.ArticuloVersionHijo = av.Id
                INNER JOIN Articulos a ON av.Articulo = a.id
                WHERE avr.ArticuloVersionRaiz IN (SELECT DISTINCT ArticuloVersionRaiz FROM ArticulosVersionesRaices WHERE ArticuloVersionDetalle IN ( SELECT DISTINCT Id FROM ArticulosVersionesDetalles WHERE (ArticuloVersionPadre = $avp OR ArticuloVersionHijo =  $avp)))
                AND a.ArticuloGrupo <> 1
                UNION
                SELECT DISTINCT ArticuloVersionRaiz FROM ArticulosVersionesRaices WHERE ArticuloVersionDetalle IN ( SELECT DISTINCT Id FROM ArticulosVersionesDetalles WHERE (ArticuloVersionPadre = $avp OR ArticuloVersionHijo =  $avp))";

        $articulos = $this->_db->fetchAll($sqlart);
        $cantidad = 0;
        foreach ($articulos as $row) {
            if($row['Id'] == $avh){
                return true;
            }
        }
        return false;
    }

}
