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
class Facturacion_FacturasVentasMinoristasController extends Rad_Window_Controller_Action
{
    protected $title = 'Facturacion Minorista';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow ()
    {
        /**
         * Formulario factura compra (Paso 1)
         */
        $this->view->form = $this->view->radForm(
            'Facturacion_Model_DbTable_TicketFacturas',
            'datagateway',
            'wizard'
        );

        /**
         * Articulos de facturas (Paso 2 - 1)
         */

        $config->abmWindowTitle     = 'Artículo';
        $config->abmWindowWidth     = 900;
        $config->abmWindowHeight    = 260;
        $config->withPaginator      = false;
        $config->title              = 'Artículo';
        $config->loadAuto           = false;
        $config->border             = false;
        $config->autoSave           = false;
        $config->iniSection         = 'wizard';
        $this->view->gridArticulos  = $this->view->radGrid(
            'Facturacion_Model_DbTable_TicketFacturasArticulos',
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
            'Facturacion_Model_DbTable_TicketFacturas',
            $config,
            'abmeditor'
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

        /**
         * Grilla de pagos
         */
        unset($config);
        $config->withPaginator = false;
        $config->loadAuto = false;
        $config->ddGroup = 'cobros';
        $config->ddText = '{0} Cobro(s) seleccionado(s)';
        $config->height = 300;
        $config->layout = 'fit';
        $config->autoSave = true;

        $grillaRD = $this->view->radGrid(
            'Facturacion_Model_DbTable_RecibosFicticiosDetalles',
            $config,
            ''
        );
        $this->view->gridRD = $grillaRD;
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
            $params['template'] = 'Comp_FacturaEmitida_Electronica';
        } else {
            // Es un comprobante no electronico... se imprime como una copia
            $params['output']   = 'pdf';
            $params['template'] = 'Comp_FacturaEmitida_Ver';
        }

        $this->_forward('report', 'BirtReporter', 'Window', $params);
    }

    public function verfacturaqrAction()
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
            $params['template'] = 'Comp_FacturaEmitida_Electronica_QR';
        } else {
            // Es un comprobante no electronico... se imprime como una copia
            $params['output']   = 'pdf';
            $params['template'] = 'Comp_FacturaEmitida_Ver';
        }

        $this->_forward('report', 'BirtReporter', 'Window', $params);
    }


    // public function cerrarfacturaAction ()
    // {
    //     $this->_helper->viewRenderer->setNoRender(true);

    //     $request    = $this->getRequest();
    //     $idFactura  = $request->getParam('id');

    //     $db         = Zend_Registry::get('db');
    //     $idFactura  = $db->quote($idFactura, 'INTEGER');

    //     try {
    //         $M_FC = new Facturacion_Model_DbTable_TicketFactura();
    //         $M_FC->cerrar($idFactura);

    //         echo '{success: true}';
    //     } catch (Rad_Db_Table_Exception $e) {
    //         echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
    //     }
    // }

}
