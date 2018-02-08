<?php

/**
 * Produccion_Model_DbTable_OrdenesDeProducciones
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_OrdenesDeProducciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_OrdenesDeProducciones extends Rad_Db_Table
{
    /**
     * Tabla mapeada de la DB
     * @var string
     */
    protected $_name = 'OrdenesDeProducciones';

    protected $_sort = array('Id Desc');


    /**
     * Validacion de la clase
     */

    protected $_validators = array(
        'Cantidad' => array(
            array('GreaterThan', "0"),
            'messages' => array('Debe indicar la cantidad a producir.')
        ),
//        'FechaInicio' => array(
//            'NotEmpty',
//            'messages' => array('Falta ingresar la fecha de inicio de producción.')
//        ),
    );


    protected $_referenceMap = array(
        'OrdenesDeProduccionesEstados' => array(
            'columns'        => 'Estado',
            'refTableClass'  => 'Produccion_Model_DbTable_OrdenesDeProduccionesEstados',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'OrdenesDeProduccionesEstados',
            'refColumns'     => 'Id'
        ),
        'Articulos' => array(
            'columns'        => 'Articulo',
            'refTableClass'  => 'Base_Model_DbTable_Articulos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/EsProducido',
            'comboPageSize'  => 20,
            'refTable'       => 'Articulos',
            'refColumns'     => 'Id'
        ),
        'ArticulosVersiones' => array(
            'columns'        => 'ArticuloVersion',
            'refTableClass'  => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'comboPageSize'  => 20,
            'refTable'       => 'ArticulosVersiones',
            'refColumns'     => 'Id'
        ),
        'ArticulosVersiones' => array(
            'columns' => 'ArticuloVersion',
            'refTableClass' => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'comboPageSize' => 20,
            'refTable' => 'ArticulosVersiones',
            'refColumns' => 'Id',
        ),
        'LineasDeProducciones' => array(
            'columns'        => 'LineaDeProduccion',
            'refTableClass'  => 'Produccion_Model_DbTable_LineasDeProducciones',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'LineasDeProducciones',
            'refColumns'     => 'Id'
        ),
        'ActividadesConfiguraciones' => array(
            'columns'        => 'ActividadConfiguracion',
            'refTableClass'  => 'Produccion_Model_DbTable_ActividadesConfiguraciones',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'ActividadesConfiguraciones',
            'refColumns'     => 'Id'
        ),
        'TiposDePrioridades' => array(
            'columns'        => 'TipoDePrioridad',
            'refTableClass'  => 'Base_Model_DbTable_TiposDePrioridades',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDePrioridades',
            'refColumns'     => 'Id'
        ),
        'Lotes' => array(
            'columns'        => 'Lote',
            'refTableClass'  => 'Almacenes_Model_DbTable_LotesPropios',
            'refJoinColumns' => array('Numero'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Lotes',
            'refColumns'     => 'Id'
        ),
        'Personas' => array(
            'columns'        => 'Persona',
            'refTableClass'  => 'Base_Model_DbTable_Clientes',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'comboPageSize'  => 20,
            'refTable'       => 'Personas',
            'refColumns'     => 'Id'
        )
    );

    protected $_dependentTables = array(
        'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles'
    );


    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {

            $j = $this->getJoiner();
            $j->joinDep(
                'Produccion_Model_DbTable_Producciones'
            );

            $j->with('Produccion_Model_DbTable_Producciones')
                ->joinDep('Produccion_Model_DbTable_ProduccionesMmis', array(),null,null, false) // false para que no agrupe automaticamente por el padre (Producciones)
                ->with('Produccion_Model_DbTable_ProduccionesMmis')
                    ->joinRef(
                        'Mmis',
                        array('Terminado' => 'sum(CantidadOriginal)')
                    );
        }
    }

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $data['Estado'] = 1; //Estado inicial

            $data['FechaOrdenDeProduccion'] = date('Y-m-d'); // Fecha de creacion

            $id = parent::insert($data);

            //$this->updateFormulaProducto($data['Articulo'], $data['Cantidad'], $id);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function update($data, $where)
    {
        try {
            // no se cambia el estado desde aca
            unset($data['Estado']);

            parent::update($data, $where);
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete
     *
     * @param array $where Registros que se deben eliminar
     */
    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R_ODP) {
                $this->salirSi_estaCerrado($R_ODP->Id);
                // Borro los registros del Detalle
                // $this->eliminarDetalle($R_ODP->Id);
                parent::delete('OrdenesDeProducciones.Id =' . $R_ODP->Id);
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    public function getRequerimientosProductos($ordenProduccionId)
    {
        $ordenProduccionId = $this->_db->quote($ordenProduccionId, 'INTEGER');

        // busco la orden de produccion
        $R_OrdenDeProduccion = $this->find($ordenProduccionId)->current();

        if (!$R_OrdenDeProduccion) {
            throw new Rad_Db_Table_Exception("No se encontro La Orden De Produccion $ordenProduccionId");
        }

        $M_Articulo = Service_TableManager::get('Base_Model_DbTable_Articulos');

        // traigo la estructura del articulo
        $estructura = $M_Articulo->getEstructuraArbol($R_OrdenDeProduccion->ArticuloVersion, true);

        $req = array();

        foreach ($estructura['desglose'] as $prod) {
            if ($prod['MateriaPrima'] == '1' && ($prod['TipoDeRelacionArticulo'] != '2' || $prod['TieneFormula'] != '1')) {
                $prod['CantidadTotal'] *= $R_OrdenDeProduccion->Cantidad;
                $req[] = $prod;
            }
        }

        return $req;
    }

    /**
     * Carga los articulos y las cantidades necesarias de produccion para la produccion del $articulo
     * en la tabla OrdenesDeProduccionOP
     *
     * @param <int> $ordenProduccionId Id de la orden de produccion
     */
    public function generarPedidoMateriales($ordenProduccionId, $recalcular = null)
    {
        $modelArticulo                 = Service_TableManager::get('Base_Model_DbTable_Articulos');
        $modelOrdenDeProduccionDetalle = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProduccionesDetalles');
        $modelUnidadesMedidas          = Service_TableManager::get('Base_Model_DbTable_UnidadesDeMedidas');

        // Obtengo los requerimientos de productos
        $requrimientos = $this->getRequerimientosProductos($ordenProduccionId);

        // borro el pedido de materiales existente
        if ($recalcular == 'S') {
            $modelOrdenDeProduccionDetalle->delete("OrdenDeProduccion = $ordenProduccionId");
        }

        $log = '';

        // traigo de haberlo el pedido actual
        $R_OrdenDeProduccionDetalle = $modelOrdenDeProduccionDetalle->fetchAll("OrdenDeProduccion = $ordenProduccionId");

        if (!count($R_OrdenDeProduccionDetalle)) {

            foreach ($requrimientos as $row) {
                // Rad_Log::getLog()->debug('Analizando req '.$row['ArticuloDesc']);
                // veo que articulos contienen este producto como producto
                $articulosId = $modelArticulo->getArticulosVersionesPorProductoVersion($row['ArticuloVersionId'], true);
                // Rad_Log::getLog()->debug($articulosId);
                // si hay articulo
                if ($articulosId) {
                    // que cantidad de producto tiene por articulo
                    $cantidadDeProducto = $modelArticulo->getCantidadProducto($articulosId[0],1,$row['UnidadDeMedidaId']);

                    // Rad_Log::getLog()->debug('cantidad de producto '.$cantidadDeProducto. ' '.$row['UnidadDeMedida']);

                    if ($cantidadDeProducto > 0) {
                        $cantArtNecesaria   = $row['CantidadTotal']/$cantidadDeProducto;
                        $oPDRow = $modelOrdenDeProduccionDetalle->createRow();

                        $oPDRow->OrdenDeProduccion = $ordenProduccionId;
                        $oPDRow->ArticuloVersion = $articulosId[0];
                        $oPDRow->Cantidad = $cantArtNecesaria;
                        $oPDRow->Fecha = date('Y-m-d H:i:s');
                        $oPDRow->save();
                    } else {
                        $log .= 'Retorno 0 la cantidad de producto del articulo '.$articulosId[0].', no se genero pedido para '.$row['ArticuloDesc'].PHP_EOL;
                    }
                } else {
                    $log .= 'No se encontraron Artículos para la demanda de '.$row['ArticuloDesc'].PHP_EOL;
                }
            }
        }
        return $log;
    }



    /**
     * Permite cerrar una Orden de Produccion y los comprobantes Hijos
     *
     * @param int $idFactura 	identificador de la factura a cerrar
     *
     */
    public function cerrar($idOrdenDeProduccion)
    {
        try {
            $this->_db->beginTransaction();

            // Controles
            $this->salirSi_NoExiste($idOrdenDeProduccion);
            $this->salirSi_EstaCerrado($idOrdenDeProduccion);

            // Cierro la Orden De Produccion
            $this->cambiarEstado($idOrdenDeProduccion, 2);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Cambia el estado de la orden de produccion
     *
     * @param int $idOrdenDeProduccion 	identificador de la de la orden de produccion
     * @param int $Estado               identificador de la del estado
     *
     */
    public function cambiarEstado($idOrdenDeProduccion, $estado)
    {
        $data = array('Estado' => $estado);
        parent::update($data, 'Id =' . $idOrdenDeProduccion);
    }

    /**
     * Asigna temporalmente en una variable de sesion los Mmis asignados a una
     * orden de produccion y sus detalles
     *
     * Estructura de la variable de sesion:
     * -> OrdenDeProduccion 1
     *     -> OrdenDeProduccionDetalle 1
     *         -> Mmi 1 => true
     *         -> Mmi 2 => true
     *     -> OrdenDeProduccionDetalle 2
     *         -> Mmi 3 => true
     *
     * @param int $ODP
     * @param int $ODPDetalle
     * @param array $idMmis
     */
    public function asignarOrdenDeProduccionDetalleMmi_Temporal($ODP, $ODPDetalle, $idMmis)
    {
        $session = new Zend_Session_Namespace('OrdenesDeProducciones');



        $ordenDeProduccion = $this->find($ODP);
        if (!count($ordenDeProduccion))
            throw new Rad_Exception('No se encuentra la Orden de Produccion');
        $ordenDeProduccion = $ordenDeProduccion->current();

        // Marco los Mmis asigandos temporalmente a una Orden de Produccion
        $mmisAsignadosTemporal = array();
        foreach ($session->MmisAsignadosTemporal as $odp)
            foreach ($odp as $idODPDetalle => $mmis)
                foreach ($mmis as $mmi => $true)
                    $mmisAsignadosTemporal[$mmi] = true;

        $modelODPDetalles = new Produccion_Model_DbTable_OrdenesDeProduccionesDetalles();
        $modelMmis = new Almacenes_Model_DbTable_Mmis();
        // Control de seguidad
        foreach ($idMmis as $idMmi) {
            if (!is_int((int)$idMmi)) {
                throw new Rad_Exception('Uno o mas de los Mmis es invalido ('.$idMmi.')');
            }
            if (in_array($idMmi, array_keys($mmisAsignadosTemporal))) {
                $asignado = $modelMmis->find($idMmi)->current();
                throw new Rad_Exception("El Mmi {$asignado->Identificador} ya se encuentra " .
                        "asignado a una Orden de Produccion");
            }
        }

        // Controla que exista la orden de produccion detalle
        $detalle = $modelODPDetalles->find($ODPDetalle);
        if (!count($detalle))
            throw new Rad_Exception('No se encuentra el detalle de la orden de produccion');
        $detalle = $detalle->current();

        // Controla que existan todos los mmis
        $idMmisConcat = implode(',', $idMmis);
        $mmis = $modelMmis->fetchAll('Id in (' . $idMmisConcat . ')');
        if (count($mmis) != count($idMmis))
            throw new Rad_Exception('No se encontraron algunos de los Mmis');

        // Suma las cantidades de todos los Mmis temporalmente asignados a este detalle
        $sumTempAsignados = 0;
        if (count($session->MmisAsignadosTemporal[$ODP][$ODPDetalle])) {
            $concatMmisAsignados = implode(',', array_keys($session->MmisAsignadosTemporal[$ODP][$ODPDetalle]));
            $mmisAsignados = $modelMmis->fetchAll('Id in (' . $concatMmisAsignados . ')');
            $sumTempAsignados = 0;
            foreach ($mmisAsignados as $mmi)
                $sumTempAsignados += $mmi->CantidadActual;
        }

        // Suma las cantidades de los Mmis que quiero asignar y controla que
        // ninguno este vacio (esto nunca deberia pasar, pero por las dudas)
        $sumAAsignar = 0;
        // Mmis temporales a asignar
        foreach ($mmis as $mmi) {
            if (!$mmi->CantidadActual)
                throw new Rad_Exception('Uno o mas Mmis se encuentran vacios');
            $sumAAsignar += $mmi->CantidadActual;
        }
        // Mmis ya asignados
        $select = $this->_db->select()->from($modelMmis->getName(), array('Suma' => 'SUM(Mmis.CantidadActual)'))
                ->join(array('ODPMmis' => 'OrdenesDeProduccionesMmis',), 'ODPMmis.Mmi = Mmis.Id', array())
                ->join(array('ODPD' => 'OrdenesDeProduccionesDetalles',), 'ODPMmis.OrdenDeProduccionDetalle = ODPD.Id', array())
                ->join(array('ODP' => 'OrdenesDeProducciones'), 'ODPD.OrdenDeProduccion = ODP.Id', array())
                ->where('ODP.Id = ?', $ODP)
                ->where('Mmis.ArticuloVersion = ?', $detalle->ArticuloVersion)
                ->group('ODP.Id');
        $cantYaAsignados = $this->_db->fetchAll($select);
        $sumYaAsignados = (count($cantYaAsignados)) ? $cantYaAsignados[0]['Suma'] : 0;

/* TODO: Control quitado PK

        // Controla que la cantidad que se quiere asignar no supere la cantidad
        // del detalle de la orden para ese articulo

        if (($sumTempAsignados + $sumYaAsignados + $sumAAsignar) > $detalle->Cantidad) {
            $modelArticulos = new Base_Model_DbTable_Articulos();
            $modelUDM = new Base_Model_DbTable_UnidadesDeMedidas();
            $articulo = $modelArticulos->find($detalle->Articulo)->current();
            $udm = $modelUDM->find($articulo->UnidadDeMedida)->current();

            throw new Rad_Exception('Los Mmis que intenta asignar exceden el ' .
                    'maximo para este articulo en ' . (($sumTempAsignados + $sumYaAsignados + $sumAAsignar) - $detalle->Cantidad) .
                    ' ' . $udm->Descripcion);
        }
        Rad_Log::debug($sumTempAsignados + $sumYaAsignados + $sumAAsignar);

*/

        // Guarda los Mmis por orden de produccion y detalle
        foreach ($mmis as $mmi)
            $session->MmisAsignadosTemporal[$ODP][$ODPDetalle][$mmi->Id] = true;
    }

    /**
     * Desasigna Mmis de un detalle de una orden de produccion (variable de sesion)
     *
     * @param int $ODP
     * @param int $ODPDetalle
     * @param array $idMmis
     */
    public function desasignarOrdenDeProduccionDetalleMmi_Temporal($ODP, $ODPDetalle, $idMmis)
    {
        $session = new Zend_Session_Namespace('OrdenesDeProducciones');

        foreach ($idMmis as $idMmi)
            unset($session->MmisAsignadosTemporal[$ODP][$ODPDetalle][$idMmi]);
    }

    /**
     * Mueve efectivamente todos los Mmis de una orden de produccion y sus
     * detalles al interdeposito asociado a la linea de produccion
     *
     * @param int $ODP
     */
    public function moverOrdenDeProduccionAInterdeposito($ODP)
    {
        $session          = new Zend_Session_Namespace('OrdenesDeProducciones');
        $modelODPDetalles = new Produccion_Model_DbTable_OrdenesDeProduccionesDetalles();
        $modelODPMmis     = new Produccion_Model_DbTable_OrdenesDeProduccionesMmis();
        $modelMmis        = new Almacenes_Model_DbTable_Mmis();

        $db = Zend_Registry::get('db');
        try {
            $db->beginTransaction();
            $ordenDeProduccion = $this->find($ODP);

            // Controles
            if (!$ordenDeProduccion) {
                throw new Rad_Exception('No se encuentra la Orden de Produccion');
            }
            $ordenDeProduccion = $ordenDeProduccion->current();
            if (!$session->MmisAsignadosTemporal[$ordenDeProduccion->Id])
                throw new Rad_Exception('La Orden de Produccion no tiene asignados Mmis');
            $rowsetODPDetalles = $ordenDeProduccion->findDependentRowset(
                            'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles', 'OrdenesDeProducciones');
            $lineaDeProduccion = $ordenDeProduccion->findParentRow(
                            'Produccion_Model_DbTable_LineasDeProducciones', 'LineasDeProducciones');
            $interdeposito = $lineaDeProduccion->findParentRow(
                            'Almacenes_Model_DbTable_Almacenes', 'Interdeposito');

            // Insercion efectiva en base de todos los Mmis desde la variable de
            // sesion asignados previamente, por orden de produccion detalle
            foreach ($rowsetODPDetalles as $rowODPDetalle) {

                //se saco el control porque pueden llevar por tandas las materias primas de la orden a produccion
                // Controla que se hayan asignado Mmis al detalle
                // if (!count($session->MmisAsignadosTemporal[$ordenDeProduccion->Id][$rowODPDetalle->Id])) {
                //     $art = $rowODPDetalle->findParentRow('Base_Model_DbTable_ArticulosGenericos', 'Articulos');
                //     throw new Rad_Exception("No se han asignado Mmis para el articulo '{$art->Descripcion}'");
                // }

                $mmis = $session->MmisAsignadosTemporal[$ordenDeProduccion->Id][$rowODPDetalle->Id];
                $mmis = $modelMmis->find(array_keys($mmis));

                //se saco el control porque pueden llevar parte o de mas de las materias primas de la orden a produccion
                // Controla que se hayan asignado las cantidades correspondientes
                // a todos los detalles de la orden de produccion
                // $sumCantidadesMmi = Rad_CustomFunctions::array_sum_column($mmis, 'CantidadActual');
                // if (abs($sumCantidadesMmi - $rowODPDetalle->Cantidad) > 0.001) {
                //     $art = $rowODPDetalle->findParentRow('Base_Model_DbTable_ArticulosGenericos', 'Articulos');
                //     //$udm = $art->findParentRow('Base_Model_DbTable_UnidadesDeMedidas', 'UnidadesDeMedidas');

                //     throw new Rad_Exception("La Orden de Produccion requiere {$rowODPDetalle->Cantidad} " .
                //             " de '{$art->Descripcion}', pero solo se han asigando " .
                //             " {$sumCantidadesMmi}");
                // }

                // Insercion OrdenesDePedidosMmis y update ubicacion de los Mmis
                foreach ($mmis as $mmi) {
                    $modelODPMmis->createRow(array(
                        'OrdenDeProduccionDetalle' => $rowODPDetalle->Id,
                        'Mmi' => $mmi->Id,
                        'CantidadActual' => $mmi->CantidadActual
                    ))->save();

                    $mmi->Almacen = $interdeposito->Id;
                    $mmi->Ubicacion = null;
                    $mmi->save();
                }
            }
            $db->commit();
            unset($session->MmisAsignadosTemporal[$ordenDeProduccion->Id]);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite anular una Orden de Produccion
     *
     * @param int $idComprobante identificador del comprobante a anular
     */
    public function anular($idOrdenDeProduccion)
    {
        // Anulo la Orden de Produccion
        $data = array('Estado' => 3);
        parent::update($data, 'Id =' . $idOrdenDeProduccion);
    }

    public function salirSi_noExiste($idOrdenDeProduccion)
    {
        if (!$this->find($idOrdenDeProduccion)->current()) {
            throw new Rad_Db_Table_Exception("No se localiza la Orden de produccion.");
        }
        return $this;
    }

    /**
     * Devuelve true si la Orden de Produccion esta fianlizada (en un estado final)
     *
     * @param int $idOrdenDeProduccion Identificador de la Orden de Produccion
     */
    public function estaFinalizada($idOrdenDeProduccion)
    {
        $R_OP  = $this->find($idOrdenDeProduccion)->current();
        if (!$R_OP) throw new Rad_Db_Table_Exception("No se localiza la orden de produccion.");

        $M_OPE = new Produccion_Model_DbTable_OrdenesDeProduccionesEstados();
        $R_OPE = $M_OPE->find($R_OP->Estado)->current();

        if ($R_OPE->EsFinal == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sale si la Orden de Produccion no es modificable
     * OJO una orden de Prod no es modificable cuando su estado es 1 (ingresada)
     *
     * @param int $idOrdenDeProduccion Identificador de la Orden de Produccion
     */
    public function salirSi_estaCerrado($idOrdenDeProduccion)
    {
        $R_ODP = $this->find($idOrdenDeProduccion)->current();

        if (!$R_ODP)
            throw new Rad_Db_Table_Exception("No se localiza la orden de produccion.");

        if ($R_ODP->Estado != 1) {
            throw new Rad_Db_Table_Exception("La Orden de Produccion se encuentra cerrada y no puede modificarse.");
        }
        return $this;
    }

    public function devolverMmisMateriaPrima($idOrdenDeProduccion, $limit = null, $order = null)
    {
        $M_M =  Service_TableManager::get('Almacenes_Model_DbTable_Mmis');
        $select = $M_M->select()->setIntegrityCheck(false) // Sin esto no se puede hacer joins (Martin)
                ->from('Mmis', '*')
                ->join("OrdenesDeProduccionesMmis", "Mmis.Id = OrdenesDeProduccionesMmis.Mmi", array())
                ->join("OrdenesDeProduccionesDetalles", "OrdenesDeProduccionesMmis.OrdenDeProduccionDetalle = OrdenesDeProduccionesDetalles.Id", array())
                ->where("OrdenesDeProduccionesDetalles.OrdenDeProduccion = ?", $idOrdenDeProduccion);

        if ($limit) {
            $select->limit($limit, 0);
        }

        if ($order) {
            $select->order($order);
        }


        $R_M = $this->_db->fetchAll($select);

        return $R_M;
    }

    /**
     * Retorna los Mmis generados en la orden de prod $idOrdenDeProduccion
     */
    public function devolverMmisGenerados($idOrdenDeProduccion, $limit = null, $order = null)
    {
        $M_M =  Service_TableManager::get('Almacenes_Model_DbTable_Mmis');

        $select = $M_M->select()->setIntegrityCheck(false) // Sin esto no se puede hacer joins (Martin)
                ->from('Mmis', '*')
                ->join("ProduccionesMmis", "Mmis.Id = ProduccionesMmis.Mmi", array())
                ->join("Producciones", "ProduccionesMmis.Produccion = Producciones.Id", array())
                ->where("Producciones.OrdenDeProduccion = ?", $idOrdenDeProduccion);

        if ($limit) {
            $select->limit($limit, 0);
        }

        if ($order) {
            $select->order($order);
        }

        $R_M = $M_M->_db->fetchAll($select);

        return $R_M;
    }

    /**
     * Retorna la cantidad total producida
     */
    public function devolverCantidadProducida($idOrdenDeProduccion)
    {
         $select = $this->select()->setIntegrityCheck(false) // Sin esto no se puede hacer joins (Martin)
                ->from('Mmis', 'Sum(CantidadOriginal)')
                ->join("ProduccionesMmis", "Mmis.Id = ProduccionesMmis.Mmi", array())
                ->join("Producciones", "ProduccionesMmis.Produccion = Producciones.Id", array())
                ->where("Producciones.OrdenDeProduccion = ?", $idOrdenDeProduccion);
         return $this->_db->fetchOne($select);
    }

    /**
     *
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchModificables($where = null, $order = null, $count = null, $offset = null)
    {
        // Aceptada / Produccion / Detenida
        $condicion = "OrdenesDeProducciones.Estado IN (2,4,6)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    /**
     *
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchProduccion($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "OrdenesDeProducciones.Estado <> 1");
        return parent::fetchAll($where, $order, $count, $offset);
    }


}