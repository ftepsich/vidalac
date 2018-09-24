<?php
require_once 'Rad/Window/Controller/Action.php';

class Almacenes_AlmacenesController extends Rad_Window_Controller_Action
{

    protected $title = 'Gestion de Almacenes';

    protected $sessionData;

    public function init ()
    {
        $this->sessionData = new Zend_Session_Namespace('Almacenes_AlmacenesController');
        parent::init();
    }

    public function initWindow ()
    {
        // Traigo el Id del almacen que voy a mostrar
        $idAlmacen = $this->getRequest()->getParam('almacen');

        if (!$idAlmacen) {
            $almacenes = new Almacenes_Model_DbTable_Almacenes(array(), false);
            $rowset = $almacenes->fetchAll(null, null, 1);
            $idAlmacen = $rowset->current()->Id;
        }


        /**
         * Grillas remitos articulos de salidas
         */
        $configGrillaremitos = array(
            'id' => $this->getName() . 'remitosartright_Grid',
            'withPaginator' => false,
            'border' => false,
            'loadAuto' => false,
            'sm' => new Zend_Json_Expr("
                 new Ext.grid.RowSelectionModel({
                    singleSelect: true
                 })")
        );

        $this->view->gridRemitosArticulos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosArticulosDeSalidas',
            $configGrillaremitos,
            null,
            'reducido'
        );

        /**
         * Grillas remitos articulos de ingresos
         */
        $configGrillaremitos = array(
            'id'            => $this->getName() . 'remitosartingresos_Grid',
            'withPaginator' => false,
            'loadAuto'      => false,
            'border'        => false,
            'ddGroup'       => 'ubicacion',
            'fetch'         => 'StockAlmacen',
            'sm'            => new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})")
        );

        $this->view->gridRemitosArticulosIngresos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosArticulosDeIngresos',
            $configGrillaremitos,
            null,
            'reducido'
        );

        /**
         * Grilla remitos de salidas
         */
        $detailGrid1->id = $this->getName() . 'remitosartright_Grid';
        $detailGrid1->remotefield = 'Comprobante';
        $detailGrid1->localfield  = 'Id';

        $configGrillaremitos = array(
            'detailGrid' => $detailGrid1,
            'fetch' => 'NoEnviados',
            'border' => false,
            'loadAuto' => false,
            'sm' => new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})"),
            /*'viewConfig' => new Zend_Json_Expr("{
                    forceFit: true,
                    enableRowBody: true,
                    showPreview: true,
                    getRowClass: function(record, rowIndex, p, store) {
                        var fecha;
                        if (record.data.FechaEntrega) fecha = record.data.FechaEntrega.dateFormat('d/m/Y h:i');
                        else fecha= ' ninguna ';
                        if (this.showPreview) {
                            p.body = '<p><b>Entrega:</b> '+fecha+'</p> ';
                            return 'x-grid3-row-expanded';
                        }
                        return 'x-grid3-row-collapsed';
                    }
                }")*/
        );

        $this->view->gridRemitos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosDeSalidasYBajas',
            $configGrillaremitos,
            null,
            'salidasbajas'
        );

        /**
         * Grilla remitos de ingresos
         */
        $detailGrid1->id = $this->getName() . 'remitosartingresos_Grid';
        $detailGrid1->remotefield = 'Comprobante';
        $detailGrid1->localfield  = 'Id';

        $configGrillaremitos = array(
            'detailGrid' => $detailGrid1,
            'loadAuto' => false,
            'fetch' => 'CerradosAPartirDePuestaEnMarchaAlmacenes',
            'border' => false,
            'sm' => new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})"),
            /*'viewConfig' => new Zend_Json_Expr("{
                forceFit: true,
                enableRowBody: true,
                showPreview: true,
                getRowClass: function(record, rowIndex, p, store) {
                    var fecha;
                    if (record.data.FechaEntrega) fecha = record.data.FechaEntrega.dateFormat('d/m/Y h:i');
                    else fecha = ' ninguna ';
                    if (this.showPreview) {
                        p.body = '<p><b>Entrega:</b> '+fecha+'</p> ';
                        return 'x-grid3-row-expanded';
                    }
                    return 'x-grid3-row-collapsed';
                }
            }")*/
        );

        $this->view->gridRemitosIngresos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosDeIngresos',
            $configGrillaremitos,
            null,
            'reducidoalmacenes'
        );

        // =====================================================================
        // Tab Produccion
        // =====================================================================

        /**
         * Grillas Ordenes de Producciones Detalles
         */
        $configODProduccionesDetalles = array(
            'id' => $this->getName() . 'odproddetalles_Grid',
            'withPaginator' => false,
            'loadAuto' => false,
            'border' => false,
            //'ddGroup' => 'ubicacion',
            //'fetch' => 'StockAlmacen',
            'sm' => new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})")
        );

        $this->view->gridODProduccionesDetalles = $this->view->radGrid(
            'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles',
            $configODProduccionesDetalles,
            null,
            'reducido'
        );

        /**
         * Grillas Ordenes de Producciones
         */
        $detailGrid2->id = $this->getName() . 'odproddetalles_Grid';
        $detailGrid2->remotefield = 'OrdenDeProduccion';
        $detailGrid2->localfield = 'Id';

        $configGrillaODProducciones = array(
            'detailGrid' => $detailGrid2,
            'border' => false,
            'loadAuto' => false,
            'fetch' => 'Modificables',
            'sm' => new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})"),
            'viewConfig' => new Zend_Json_Expr("{
                forceFit: true,
                enableRowBody: true,
                showPreview: true,
                getRowClass: function(record, rowIndex, p, store) {
                    if (this.showPreview) {
                        p.body = '<p>';
                        if (record.data.TipoDePrioridad) {
                            var color = 'black';
                            switch (record.data.TipoDePrioridad) {
                                case '1':
                                    color = 'red';
                                    break;
                                case '3':
                                    color = 'green';
                                    break;
                            }
                            p.body += '<b>Prioridad:</b> <font color=\"'+color+'\">'+
                                record.data.TipoDePrioridad_cdisplay+'</font><br/>';
                        }
                        if (record.data.LineaDeProduccion)
                            p.body += '<b>Linea:</b> '+record.data.LineaDeProduccion_cdisplay+'<br/>';
                        if (record.data.Persona)
                            p.body += '<b>Cliente:</b> '+record.data.Persona_cdisplay+'<br/>';
                        p.body += '</p>';
                        return 'x-grid3-row-expanded';
                    }
                    return 'x-grid3-row-collapsed';
                }
            }")
        );

        $this->view->gridODProducciones = $this->view->radGrid(
            'Produccion_Model_DbTable_OrdenesDeProducciones',
            $configGrillaODProducciones,
            null,
            'reducido'
        );

        $configGrillaMmis = array(
            'border'        => false,
            'fetch'         => 'MmisAbiertos',
            'loadAuto'      => false
        );

    }

    /**
     * Devuelve el grafico de ubicaciones de un almacen
     *
     */
    public function getcellsAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        // Si hay request viene de afuera
        $request = $this->getRequest();
        if ($request->almacen)
            $idAlmacen = $request->almacen;
        else
            throw new Rad_Exception('Falta el parametro requerido almacen');

        $perspectiva = $request->perspectiva;

        $almacenes = new Almacenes_Model_DbTable_Almacenes(array(), false);

        $almacen = $almacenes->find($idAlmacen)->current();
        if (!$almacenes) throw new Rad_Exception('No existe el almacen solicitado.');

        $db = Zend_Registry::get('db');

        // Marco los Mmis asigandos temporalmente a una Orden de Produccion
        $session = new Zend_Session_Namespace('OrdenesDeProducciones');
        $mmisAsignadosTemporal = array();



        foreach ($session->MmisAsignadosTemporal as $odp) {
            foreach ($odp as $idODPDetalle => $mmis) {
                foreach ($mmis as $mmi => $true) {
                    $mmisAsignadosTemporal[$mmi] = $idODPDetalle;
                }
            }
        }

        if ($almacen->TieneRack) {
            // ========================================================================================================
            // Si es rackeable (Almacenes comunes)
            // ========================================================================================================


            // Por default la perspectiva que se cargo en el Almacen
            if (!$perspectiva) $perspectiva = $almacen->Perspectiva;

            $select =  "call Almacenes_Rack($idAlmacen,$perspectiva);";
            $stmt = $db->query($select);

            $rowset = $stmt->fetchAll();
            /*
            Tengo que usar la que tiene cambiado el sistema de coordenadas

            $cells = Array(
                'rows'              => array(),
                'cantFila'          => $almacen->RackCantFila,
                'cantProfundidad'   => $almacen->RackCantProfundidad,
                'cantAltura'        => $almacen->RackCantAltura
            );
            */
            // =====================================================================================================================
            // Itera sobre los registros
            foreach ($rowset as &$row) {
                $row['AsignadoODPDetalleTemporal'] = $mmisAsignadosTemporal[$row['Mmi_Id']];

                if (!count($cells)) {
                    $cells = Array(
                        'rows'              => array(),
                        'cantFila'          => $row['CantFila'],
                        'cantAltura'        => $row['CantAltura'],
                        'cantProfundidad'   => $row['CantProfundidad']
                    );
                // Rad_Log::debug('$cells'); Rad_Log::debug($cells);
                }

                // Itera sobre los campos del registro
                foreach ($row as $k => $v) {
                    if (strstr($k, '_') !== false) {
                        // Separa los campos en un array por tabla relacionada
                        $subkey = explode('_', $k);
                        $row[$subkey[0]][$subkey[1]] = $v;
                        unset($row[$k]);
                    }
                }
                $cells['rows'][] = $row;
            }

            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $this->getResponse()->setBody(json_encode($cells));
        } else {
            // ========================================================================================================
            // Si NO es rackeable (Predepositos e interdepositos)
            // ========================================================================================================

            $select = $db->select()
                            ->from(
                                array('M' => 'Mmis'),
                                array(
                                    '*',
                                    'Orden' =>'(select opd.Id from OrdenesDeProducciones op join OrdenesDeProduccionesEstados ope on ope.Id = op.Estado and ope.Id>1 and ope.EsFinal = 0 join OrdenesDeProduccionesDetalles opd on opd.OrdenDeProduccion = op.Id join OrdenesDeProduccionesMmis opm on opd.Id = opm.OrdenDeProduccionDetalle where  opm.Mmi = M.Id limit 1)'
                                )
                            )
                            ->joinLeft(array('CD' => 'ComprobantesDetalles'),
                                'M.RemitoArticulo = CD.Id',
                                array()
                            )->joinLeft(array('CDS' => 'ComprobantesDetalles'),
                                'M.RemitoArticuloSalida = CDS.Id',
                                array()
                            )->joinLeft(array('CE' => 'Comprobantes'),
                                'CE.Id = CD.Comprobante',
                                array(
                                    'CE.Numero' => 'Numero',
                                    'CE.Punto'  => 'Punto'
                                )
                            )->joinLeft(array('CS' => 'Comprobantes'),
                                'CS.Id = CDS.Comprobante',
                                array(
                                    'CS.Numero' => 'Numero',
                                    'CS.Punto'  => 'Punto'
                                )
                            )
                            ->joinLeft(
                                array('A' => 'Articulos'),
                                'M.Articulo = A.Id',
                                array(
                                    'A.Id' => 'Id',
                                    'A.Descripcion' => 'Descripcion'
                                )
                            )->joinLeft(array('Lote' => 'Lotes'),
                                'Lote.Id = M.Lote',
                                array(
                                    'Lote.Numero' => 'Numero',
                                    'Lote.FechaVencimiento'  => 'FechaVencimiento',
                                    'Lote.FechaElaboracion'  => 'FechaElaboracion'
                                )
                            )
                            ->where('M.FechaCierre IS NULL AND M.Almacen = ?', $idAlmacen);

            $stmt = $db->query($select);
            $rowset = $stmt->fetchAll();

            $cells = Array(
                'cantFila' => 40,
                'cantProfundidad' => 5,
                'cantAltura' => 17,
                'rows' => Array()
            );

            $fila = 1;
            $altura = 0;
            foreach ($rowset as $row) {
                $altura++;
                $fila++;
                if ($altura > 17) {
                    $altura = 1;
                    $fila = 1;
                }
                $tmp = array();
                $tmp['Fila'] = $fila;
                $tmp['Profundidad'] = 1;
                $tmp['Altura'] = $altura;
                $tmp['Existente'] = 1;
                $tmp['Id'] = $row['Id'];
                $tmp['Descripcion'] = $row['Identificador'];
                $tmp['A']['Id'] = $row['A.Id'];
                $tmp['A']['Descripcion'] = $row['A.Descripcion'];
                // Asigando temporalmente a una Orden de Produccion Detalle
                $tmp['AsignadoODPDetalleTemporal'] = $mmisAsignadosTemporal[$row['Id']];
                $tmp['CE']['Numero'] = $row['CE.Numero'];
                $tmp['CE']['Punto']  = $row['CE.Numero'];
                $tmp['CS']['Numero'] = $row['CS.Numero'];
                $tmp['CS']['Punto']  = $row['CS.Punto'];
                $tmp['Lote']['Numero'] = $row['Lote.Numero'];
                $tmp['Lote']['FechaVencimiento'] = $row['Lote.FechaVencimiento'];
                $tmp['Lote']['FechaElaboracion'] = $row['Lote.FechaElaboracion'];
                unset($row['A.Id']);
                unset($row['A.Descripcion']);
                $tmp['Mmi'] = $row;
                $cells['rows'][] = $tmp;
            }

            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $this->getResponse()->setBody(json_encode($cells));
        }
    }

    public function gettemporalAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $db = $this->getInvokeArg('bootstrap')->getResource('db');

        $deposito = $this->getRequest()->deposito;

        $deposito = $db->quote($deposito, 'INTEGER');

        $select = $db->select()
            ->from(array('M' => 'Mmis'),
                    array('*')
            )->joinLeft(array('CD' => 'ComprobantesDetalles'),
                    'M.RemitoArticulo = CD.Id',
                    array()
                )->joinLeft(array('CDS' => 'ComprobantesDetalles'),
                    'M.RemitoArticuloSalida = CDS.Id',
                    array()
                )->joinLeft(array('CE' => 'Comprobantes'),
                    'CE.Id = CD.Comprobante',
                    array(
                        'CE.Numero' => 'Numero',
                        'CE.Punto'  => 'Punto'
                    )
                )->joinLeft(array('CS' => 'Comprobantes'),
                    'CS.Id = CDS.Comprobante',
                    array(
                        'CS.Numero' => 'Numero',
                        'CS.Punto'  => 'Punto'
                    )
                )
            ->joinLeft(
                    array('A' => 'Articulos'),
                    'M.Articulo = A.Id',
                    array(
                        'A.Id' => 'Id',
                        'A.Descripcion' => 'Descripcion'
                    )
                )
            ->joinLeft(
                array('Lote' => 'Lotes'),
                'Lote.Id = M.Lote',
                array(
                    'Lote.Numero' => 'Numero',
                    'Lote.FechaVencimiento'  => 'FechaVencimiento',
                    'Lote.FechaElaboracion'  => 'FechaElaboracion'
                )
            )
            ->where('Almacen IS NULL')
            ->where('Deposito = '.$deposito)
            ->where('Ubicacion IS NULL')
            ->where('M.FechaCierre IS NULL');

        $stmt = $db->query($select);
        $rowset = $stmt->fetchAll();

        $cells = Array(
            'cantFila' => 40,
            'cantProfundidad' => 5,
            'cantAltura' => 18,
            'rows' => Array()
        );

        foreach ($row as $k => $v) {
            if (strstr($k, '.') !== false) {
                // Separa los campos en un array por tabla relacionada
                $subkey = explode('.', $k);
                $row[$subkey[0]][$subkey[1]] = $v;
                unset($row[$k]);
            }
        }

        $fila = 1;
        $altura = 0;
        foreach ($rowset as $row) {
            $altura++;
            $fila++;
            if ($altura > 18) {
                $altura = 1;
                $fila = 1;
            }

            $tmp = array();
            $tmp['Id']                  = $row['Id'];
            $tmp['Fila']                = $fila;
            $tmp['A']['Id']             = $row['A.Id'];
            $tmp['A']['Descripcion']    = $row['A.Descripcion'];
            $tmp['Profundidad']         = 1;
            $tmp['ArticuloDescripcion'] = $row['Descripcion'];
            $tmp['Descripcion']         = $row['Identificador'];
            $tmp['Altura']              = $altura;
            $tmp['CE']['Numero']        = $row['CE.Numero'];
            $tmp['CE']['Punto']         = $row['CE.Numero'];
            $tmp['CS']['Numero']        = $row['CS.Numero'];
            $tmp['CS']['Punto']         = $row['CS.Punto'];
            $tmp['Lote']['Numero'] = $row['Lote.Numero'];
            $tmp['Lote']['FechaVencimiento'] = $row['Lote.FechaVencimiento'];
            $tmp['Lote']['FechaElaboracion'] = $row['Lote.FechaElaboracion'];
            $tmp['Ocupado']             = 1;
            $tmp['Existente']           = 1;
            $tmp['Mmi']                 = $row;
            $cells['rows'][]            = $tmp;
        }

        $this->getResponse()->setHeader('Content-Type', 'text/javascript');
        $this->getResponse()->setBody(json_encode($cells));
    }

    /**
     * Mueve un Mmi de una ubicacion a otra
     *
     */
    public function moveAction ()
    {
        // $origen      -> Id de ubicacion origen ?
        // $destino     -> Id de ubicacion destino ?
        // $almacen     -> almacen ORIGEN
        $this->_helper->viewRenderer->setNoRender(true);

        // Si viene como peticion ajax seteo los parametros
        $request = $this->getRequest();

        $origenArray = $request->getParam('from');
        $destinoArray = $request->getParam('to');
        $almArray = $request->getParam('almacen');

        $almacenes = new Almacenes_Model_DbTable_Almacenes();

        try {
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->beginTransaction();

            foreach ($almArray as $k => $alm) {
                $almacen = $almacenes->find($alm)->current();

                $destino = $destinoArray[$k];
                $origen = $origenArray[$k];
                if (!$origen || !$destino)
                    throw new Exception('Faltan los parametros requeridos');

                // si es rackeable, 'origen' es la ubicacion
//                if (count($almacen) && !$almacen->TipoDeAlmacen != 2) {
                if (count($almacen) && !(in_array($almacen->TipoDeAlmacen, array(2, 3)))) {
                    // Rad_Log::info('Almacen origen rackeable');
                    // Rad_Log::debug($almacen);
                    $this->_moveFromAlmacen($origen, $destino);
                    // si no es rackeable, 'origen' es el mmi
                } else {
                    // Rad_Log::info('Almacen origen NO es rackeable');
                    // Rad_Log::debug("origen: $origen / destino: $destino, alm: $alm");
                    $this->_moveFromNoRackeable($origen, $destino, $alm);
                    throw new Rad_Exception("Oleeeeeeeeeeeeee");
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $this->getResponse()->setBody("{success:false,msg:'" . addslashes($e->getMessage()) . "'}");
            throw $e;
        }
    }

    /**
     * 	Retorna la traza del Mmi identificado por el parametro Id del Request
     * 	en formato json
     */
    public function listtrazammiAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $mmis = new Almacenes_Model_DbTable_Mmis(array(), false);
        $request = $this->getRequest();
        $id = $request->getParam('id');
        try {
            $rt['rows'] = $mmis->getTrazabilidad($id);
            $rt['count'] = count($rt['rows']);

            $json = Zend_Json::encode($rt, false, array('enableJsonExprFinder' => true));
            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $this->getResponse()->setBody($json);
        } catch (Rad_Db_Table_Exception $e) {
            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $this->getResponse()->setBody("{success:false,msg:'" . $e->getMessage() . "'}");
        }
    }

    /**
     *  devuelve un array con los subarticulos de un articulo version dado
     */
    public function filtrosubarticuloAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $id = $request->getParam('id');

        $modelMmis = new Almacenes_Model_DbTable_Mmis(array(), true);
        $modelArticulos = new Base_Model_DbTable_Articulos(array(), true);

        $mmi = $modelMmis->find($id)->current();

        $reg = $modelArticulos->getEstructuraArbol($mmi->ArticuloVersion);

        $rows = array();

        foreach($reg["desglose"] as $row){
            $rows[] = array(
                'Id'          =>$row["ArticuloVersionId"],
                'Descripcion' =>$row["ArticuloDesc"]
            );
        }
        $r = array(
            'rows'    =>$rows,
            'count'   =>count($rows),
            'success' =>true
        );

        echo json_encode($r);
    }

    /**
     *  Cambia el articulo del Mmi indetificado
     */
    public function cambiararticulommiAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        // Si viene como peticion ajax seteo los parametros
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getParam('id');
        $articuloVersion = $request->getParam('articuloversion');

        if (!$articuloVersion) {
            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:false,msg:'No viene el parametro articulo version'}");
        } else {
            $mmi = new Almacenes_Model_DbTable_Mmis(array(), false);
            try {
                $mmi->cambiarArticuloAMmi($id, $articuloVersion);
                $response->setHeader('Content-Type', 'text/javascript');
                $response->setBody("{success:true}");
            } catch (Rad_Db_Table_Exception $e) {
                $response->setHeader('Content-Type', 'text/javascript');
                $response->setBody("{success:false,msg:'" . addslashes($e->getMessage()) . "'}");
            }
        }
    }

    /**
     *  Cambia la cantidad actual del Mmi indetificado por el parametro id del request
     */
    public function cambiarcantidadmmiAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        // Si viene como peticion ajax seteo los parametros
        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getParam('id');
        $cantidad = $request->getParam('cantidad');

        if (!is_numeric($cantidad) || $cantidad < 0) {
            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:false,msg:'La cantidad debe ser numerica y positiva'}");
        } else {
            $mmi = new Almacenes_Model_DbTable_Mmis(array(), false);
            try {
                $mmi->cambiarCantidadMmi($id, $cantidad);
                $response->setHeader('Content-Type', 'text/javascript');
                $response->setBody("{success:true}");
            } catch (Rad_Db_Table_Exception $e) {
                $response->setHeader('Content-Type', 'text/javascript');
                $response->setBody("{success:false,msg:'" . addslashes($e->getMessage()) . "'}");
            }
        }
    }

    /**
     * 	Parte el Mmi indetificado por el parametro id del request
     */
    public function partirmmiAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        // Si viene como peticion ajax seteo los parametros
        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getParam('id');
        $cantidad = $request->getParam('cantidad');

        if (!is_numeric($cantidad) || $cantidad <= 0) {
            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:false,msg:'La cantidad debe ser numerica y positiva'}");
        } else {
            $mmi = new Almacenes_Model_DbTable_Mmis(array(), false);
            try {
                $mmi->partirMmi($id, $cantidad);
                $response->setHeader('Content-Type', 'text/javascript');
                $response->setBody("{success:true}");
            } catch (Rad_Db_Table_Exception $e) {
                $response->setHeader('Content-Type', 'text/javascript');
                $response->setBody("{success:false,msg:'" . addslashes($e->getMessage()) . "'}");
            }
        }
    }

    /**
     *  Parte el Mmi indetificado por el parametro id del request
     */
    public function eliminarmmiAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        // Si viene como peticion ajax seteo los parametros
        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getParam('id');

        $mmi = new Almacenes_Model_DbTable_Mmis(array(), false);
        try {
            $mmi->eliminarMmi($id);
            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:true}");
        } catch (Rad_Db_Table_Exception $e) {
            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:false,msg:'" . addslashes($e->getMessage()) . "'}");
        }

    }

    public function despacharremitoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $request    = $this->getRequest();
        $response   = $this->getResponse();

        $remitos = new Almacenes_Model_DbTable_RemitosDeSalidas(array(), false);
        try {
            $idRemito = $request->getParam('id');
            $remitos->despachar($idRemito);

            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:true}");
        } catch (Rad_Db_Table_Exception $e) {
            $response->setHeader('Content-Type', 'text/javascript');
            $response->setBody("{success:false,msg:'" . addslashes($e->getMessage()) . "'}");
        }
    }

    public function reportAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $id     = $this->getRequest()->id;
        $report = new Rad_BirtEngine();

        $report->setParameter('Id', $id, 'String');

        //Rad_Log::debug('setParameter');
        //Rad_Log::debug($id);

        $texto          = 'Detalle de movimientos a realizar en Deposito';
        $cabeceraUsar   = 'CabeceraInterna';
        $file           = APPLICATION_PATH . '/../birt/Reports/MmiMovimientos.rptdesign';
        $where          = '';
        $formato        = 'pdf';

        $report->renderFromFile( $file, $formato, array(
            'TEXTO'     => $texto,
            'CABECERA'  => $cabeceraUsar
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = 'Reporte_'.$nombreRep."___".date('YmdHis');

        $report->sendStream($NombreReporte);

        /*
        $report->renderFromFile("/var/www/birt/Reports/MmiMovimientos.rptdesign", 'html');
        $report->sendStream();
        */
    }

    /**
     * Devuelve el html mostrando el contenido del MMI
     *
     */
    public function descripcionmmiAction ()
    {
        //$this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $id = $request->id;

        if (!$id) {
            throw new Rad_Exception('Falta el parametro requerido');
        }

        $v = $this->view;

        $modelMmis      = new Almacenes_Model_DbTable_Mmis(array(), true);
        $modelArticulos = new Base_Model_DbTable_Articulos(array(), true);

        $mmi = $modelMmis->find($id)->current();

        if (!$mmi) {
            throw new Rad_Exception('No se encontro el mmi');
        }

        $v->mmi = $mmi;

        $articulo = $modelArticulos->find($mmi->Articulo)->current();

        if (!$articulo) {
            throw new Rad_Exception('No se encontro el articulo ' . $mmi->Articulo);
        }

        $v->articulo = $articulo;


        $v->fechaIngreso = ($mmi->FechaIngreso) ? date('d/m/Y', strtotime($mmi->FechaIngreso)) : null;
        $v->fechaVencimiento = ($mmi->FechaVencimiento) ? date('d/m/Y', strtotime($mmi->FechaVencimiento)) : null;
    }

}
