<?php

/**
 *
 * Facturacion_FacturasVentasController
 *
 * Controlador Facturas Ventas
 *
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_FacturasVentasController
 * @extends     Rad_Window_Controller_Action
 * @copyright   SmartSoftware Argentina 2010
 */
class Facturacion_FacturasVentasController extends Rad_Window_Controller_Action
{

    protected $title = 'Emisión de Comprobantes';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow ()
    {
        /**
         * Formulario factura venta (Paso 1)
         */
        $this->view->form = $this->view->radForm(
            'Facturacion_Model_DbTable_FacturasVentas',
            'datagateway',
            'wizard'
        );

        /**
         * Formulario Exportacion (Paso 1.1)
         */
        $this->view->formExport = $this->view->radForm(
            'Facturacion_Model_DbTable_ComprobantesDeExportaciones',
            'datagateway',
            'wizard'
        );


        /**
         * Exportacion Grilla Permisos
         */

        $config->abmWindowTitle     = 'Permisos';
        $config->abmWindowWidth     = 900;
        $config->abmWindowHeight    = 260;
        $config->withPaginator      = false;
        $config->title              = 'Permisos';
        $config->loadAuto           = false;
        $config->border             = false;
        $config->region             = 'center';
        $config->autoSave           = true;
        $this->view->gridPermisos   = $this->view->radGrid(
            'Facturacion_Model_DbTable_PermisosExportaciones',
            $config,
            'abmeditor'
        );
        /**
         * Asociar Remito a Factura (Paso 2 )
         */
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridRAS'; // RA_FCGridHija123
        $this->view->gridRemitoArt = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosArticulosDeSalidas',
            $config
        );
        unset($config);

        $detailGrid->id = $this->getName() . '_GridRAS';
        $detailGrid->remotefield = 'Comprobante';
        $detailGrid->localfield = 'Id';

        $this->view->gridRemito = $this->view->RadGridManyToMany(
            'Almacenes_Model_DbTable_RemitosDeSalidas',
            'Facturacion_Model_DbTable_FacturasVentasRemitos',
            'Facturacion_Model_DbTable_FacturasVentas',
            array(
                'title'             => 'Remitos',
                'xtype'             => 'radformmanytomanyeditorgridpanel',
                'detailGrid'        => $detailGrid,
                'fetch'             => 'AsociadosYFaltantesDeFacturarV',
                'abmWindowTitle'    => 'Agregar remito',
                'abmWindowWidth'    => 940,
                'abmWindowHeight'   => 620,
                'withPaginator'     => false,
                'iniSection'        => 'wizard'
            )
            //,"RemitosRelacionadas"
        );

        /**
         * Articulos de facturas (Paso 3 - 1)
         */

        $config->abmWindowTitle     = 'Artículo';
        $config->abmWindowWidth     = 900;
        $config->abmWindowHeight    = 260;
        $config->withPaginator      = false;
        $config->title              = 'Artículo';
        $config->loadAuto           = false;
        $config->border             = false;
        $config->autoSave           = true;
        $config->iniSection         = 'wizard';
        $this->view->gridArticulos  = $this->view->radGrid(
            'Facturacion_Model_DbTable_FacturasVentasArticulos',
            $config,
            'abmeditor',
            'wizard'
        );
        unset($config);

        /**
         * Asociar Conceptos impositivos a Factura (Paso 4)
         */
        $config->abmWindowTitle     = 'Concepto Impositivo';
        $config->abmWindowWidth     = 500;
        $config->abmWindowHeight    = 180;
        $config->withPaginator      = false;
        $config->title              = 'Conceptos Impositivos';
        $config->loadAuto           = false;
        $config->border             = false;
        $config->autoSave           = true;
        $config->iniSection         = 'wizard';
        $config->buildToolbar       = new Zend_Json_Expr("
            function() {
                var id = this.getId();
                this.tbar = new Ext.Toolbar(
                    {
                        items:[
                            {
                                text:     'Agregar',
                                id:       id+'-Agregar',
                                iconCls:  'add',
                                handler:  this.createRow,
                                scope:    this,
                                hidden:   ( this.topButtons && this.topButtons.add == false ) ? true : false,
                                disabled: !this.loadAuto
                            },
                            '-',
                            {
                                text:    'Borrar',
                                id:      id+'-Borrar',
                                iconCls: 'remove',
                                handler: function() {
                                    this.deleteRows();
                                },
                                scope:   this,
                                hidden:  ( this.topButtons && this.topButtons.del == false ) ? true : false,
                                disabled: !this.loadAuto
                            }
                        ]
                });
            }");

        $this->view->gridCI = $this->view->radGrid(
            'Facturacion_Model_DbTable_FacturasVentasConceptos',
            $config,
            'abmeditor',
            'wizard'
        );
        unset($config);

        /**
         * Grilla Facturas Ventas
         */
        $config->abmForm = null;
        $config->viewConfig = new Zend_Json_Expr("
        {
            forceFit:true,
            enableRowBody:false,
            getRowClass: function(record, rowIndex, p, store) {
                var tc = record.get('Cerrado');
                return (tc == 0)? 'x-grid3-row-red' : '';
            }
        }");

        $config->loadAuto = false;
        $config->id = $this->getName() . '_Grid';
        $this->view->grid = $this->view->radGrid(
            'Facturacion_Model_DbTable_FacturasVentas',
            $config,
            'abmeditor'
        );
        unset($config);
    }

    public function refiscalizarAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $request        = $this->getRequest();
        $idFactura      = $request->getParam('id');

        $db             = Zend_Registry::get('db');
        $idFactura      = $db->quote($idFactura, 'INTEGER');

        $modelFacturas  = new Facturacion_Model_DbTable_FacturasVentas();
        $factura        = $modelFacturas->find($idFactura)->current();

        try {
            // Fiscalizamos la factura
            $fiscalizador = new Facturacion_Model_Fiscalizar();
            $fiscalizador->refiscalizar($factura);
            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    public function cambiartipoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $request        = $this->getRequest();
        $idFactura      = $request->getParam('id');

        $db             = Zend_Registry::get('db');
        $idFactura      = $db->quote($idFactura, 'INTEGER');

        $modelFacturas  = new Facturacion_Model_DbTable_FacturasVentas();

        $factura        = $modelFacturas->find($idFactura)->current();

        if (!$factura) {
            throw new \Exception('No se encontro el comprobante');
        }

        $tipo = $factura->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
        
        if (!in_array($tipo->Grupo,[7,12])) {
            throw new \Exception('No se puede cambiar este tipo de comprobante.');
        }

        $map = [
            // grupo 7
            29 => 67,
            30 => 68,
            31 => 69,
            32 => 70,
            59 => 71,
            67 => 29 ,
            68 => 30 ,
            69 => 31 ,
            70 => 32 ,
            71 => 59 ,
            // grupo 12
            37 => 77,
            38 => 78,
            39 => 79,
            40 => 80,
            61 => 81,
            77 => 37,
            78 => 38,
            79 => 39,
            80 => 40,
            81 => 61,
        ];

        try {
            $modelFacturas->cambiarTipoComprobante($factura, $map[$factura->TipoDeComprobante]);
            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    public function verfacturaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $modelFV    = new Facturacion_Model_DbTable_FacturasVentas();

        $rq         = $this->getRequest();
        $params     = $rq->getParams();

        $db         = $modelFV->getAdapter();

        $id         = $params['id'];

        $factura    = $modelFV->find($db->quote($id, 'INTEGER'))->current();

        if (!$factura) {
            echo "{success: false, msg: 'No se encontro el comprobante a imprimir'}";
            return;
        }

        $adaptador  = $modelFV->getAdaptadorPunto($factura->Punto);

        if ($adaptador->getRequiereImpresion()){
            // Es una factura electronica (Se imprime como un comp original)
            $params['output']   = 'pdf';
            $params['template'] = $adaptador->templateImpresion;
            // 'Comp_FacturaEmitida_Electronica';
        } else {
            // Es un comprobante no electronico... se imprime como una copia
            $params['output']   = 'pdf';
            $params['template'] = 'Comp_FacturaEmitida_Ver';
        }

        $this->_forward('report', 'BirtReporter', 'Window', $params);
    }

    public function cerrarfacturaAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $request    = $this->getRequest();
        $idFactura  = $request->getParam('id');

        $db         = Zend_Registry::get('db');
        $idFactura  = $db->quote($idFactura, 'INTEGER');

        try {
            $M_FC = new Facturacion_Model_DbTable_FacturasVentas();
            $M_FC->cerrar($idFactura);

            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

}