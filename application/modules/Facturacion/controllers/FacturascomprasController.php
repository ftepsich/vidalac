<?php

/**
 * Facturacion_FacturasComprasController
 *
 * Controlador Facturas Compras
 *
 *
 * @package Aplicacion
 * @subpackage Facturacion
 * @class Facturacion_FacturasComprasController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Facturacion_FacturasComprasController extends Rad_Window_Controller_Action {

    protected $title = 'Ingreso de Comprobantes';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow() {
        /**
         * Formulario factura compra (Paso 1)
         */
        $abmFormCF = $this->view->radForm(
                'Facturacion_Model_DbTable_FacturasCompras', 'datagateway', 'wizard'
        );

        $this->view->form = $abmFormCF;

        /**
         * Asociar Remito a Factura (Paso 2 )
         */
        $configHijaR1->loadAuto = false;
        $configHijaR1->withPaginator = false;
        $configHijaR1->id = 'RA_FCGridHija123';
        $grillaHijaR1 = $this->view->radGrid(
                'Almacenes_Model_DbTable_RemitosArticulosDeEntradas', $configHijaR1
        );

        $this->view->gridRemitoArt = $grillaHijaR1;

        $detailGrid->id = 'RA_FCGridHija123';
        $detailGrid->remotefield = 'Comprobante';
        $detailGrid->localfield = 'Id';


        $grillaAR = $this->view->RadGridManyToMany(
                "Almacenes_Model_DbTable_RemitosDeEntradas", "Facturacion_Model_DbTable_FacturasComprasRemitos", "Facturacion_Model_DbTable_FacturasCompras", array(
            'title' => 'Remitos',
            'xtype' => 'radformmanytomanyeditorgridpanel',
            'detailGrid' => $detailGrid,
            'fetch' => 'AsociadosYFaltantesDeFacturar',
            'abmWindowTitle' => 'Agregar remito',
            'abmWindowWidth' => 940,
            'abmWindowHeight' => 620,
            'withPaginator' => false,
            'buildToolbar' => new Zend_Json_Expr("function() {}")
                )
                , "wizardfc"
        );
        $this->view->gridRemito = $grillaAR;

        /**
         * Asociar Cheques a Factura (Paso 3 )
         */
        // $grillaCH = $this->view->RadGridManyToMany(
        //                 "Base_Model_DbTable_Cheques",
        //                 "Facturacion_Model_DbTable_FacturasComprasCheques",
        //                 "Facturacion_Model_DbTable_FacturasCompras",
        //                 array (
        //                     'title'           => 'Cheques',
        //                     'xtype'           => 'radformmanytomanyeditorgridpanel',
        //                     'detailGrid'      => $detailGrid,
        //                     'fetch'           => 'AsociadosYFaltantesDeCobrar',
        //                     'abmWindowTitle'  => 'Agregar cheque',
        //                     'abmWindowWidth'  => 940,
        //                     'abmWindowHeight' => 620,
        //                     'withPaginator'   => false,
        //                     'buildToolbar'    => new Zend_Json_Expr("function() {}")
        //                 )
        //             ,"reducido"
        // );
        // $this->view->gridCheque = $grillaCH;


        /**
         * Articulos Relacionados (Paso 4 - 2)
         */
        $config->withPaginator = false;
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridArticulosRel';
        $this->view->gridArticulosRel = $this->view->radGrid(
                'Facturacion_Model_DbTable_vRelFacturasArticulosOrdenesArticulos', $config
        );
        unset($config);

        /**
         * Articulos de facturas (Paso 4 - 3)
         */
        $dg->id = $this->getName() . '_GridArticulosRel';
        $dg->remotefield = 'FAID';
        $dg->localfield = 'Id';

        $config->abmWindowTitle = 'Artículo';
        $config->abmWindowWidth = 840;
        $config->abmWindowHeight = 260;
        $config->withPaginator = false;
        $config->title = 'Artículo';
        $config->loadAuto = false;
        $config->autoSave = true;
        $config->iniSection = 'wizard';
        $config->detailGrid = array($dg);
        $this->view->gridArticulos = $this->view->radGrid(
                'Facturacion_Model_DbTable_FacturasComprasArticulos', $config, 'abmeditor', 'wizard'
        );
        unset($config);

        /**
         * Asociar Conceptos impositivos a Factura (Paso 5)
         */
        $config->abmWindowTitle = 'Concepto Impositivo';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 180;
        $config->withPaginator = false;
        $config->title = 'Conceptos Impositivos';
        $config->loadAuto = false;
        $config->autoSave = true;
        $config->iniSection = 'wizard';
        $config->buildToolbar = new Zend_Json_Expr("
            function() {
                var id = this.getId();
                this.tbar = new Ext.Toolbar(
                    {
                        items:[
                            {
                                text:     'Agregar',
                                id:        id+'-Agregar',
                                iconCls:  'add',
                                handler:  this.createRow,
                                scope:    this,
                                hidden: (this.topButtons && this.topButtons.add == false) ? true : false,
                                disabled: !this.loadAuto
                            },
                            '-',
                            {
                                text:    'Borrar',
                                id:      id+'-Borrar',
                                iconCls: 'remove',
                                handler: function(){
                                    this.deleteRows();
                                },
                                scope:   this,
                                hidden:  (this.topButtons && this.topButtons.del == false) ? true : false,
                                disabled: !this.loadAuto
                            }
                        ]
                });
            }");

        $grillaFCC = $this->view->radGrid(
                'Facturacion_Model_DbTable_FacturasComprasConceptos', $config, 'abmeditor', 'wizard'
        );
        $this->view->gridCI = $grillaFCC;
        unset($config);

        /**
         * Grilla Facturas Ventas
         */
        $config->abmForm = null;
        $config->loadAuto = false;
        $config->viewConfig = new Zend_Json_Expr("
        {
            forceFit:true,
            enableRowBody:false,
            getRowClass: function(record, rowIndex, p, store) {
                var tc = record.get('Cerrado');
                return (tc == 0)? 'x-grid3-row-red' : '';
            }
        }");
        $config->id = $this->getName() . '_Grid';
        $config->loadAuto = false;
        // $config->fetch = 'FacturasComprasNotasRecibidas';

        $this->view->grid = $this->view->radGrid(
                'Facturacion_Model_DbTable_FacturasCompras', $config, 'abmeditor'
        );
        unset($config);
    }

    public function cambiartipoAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $idFactura = $request->getParam('id');

        $db = Zend_Registry::get('db');
        $idFactura = $db->quote($idFactura, 'INTEGER');

        $modelFacturas = new Facturacion_Model_DbTable_FacturasCompras();

        $factura = $modelFacturas->find($idFactura)->current();

        if (!$factura) {
            throw new \Exception('No se encontro el comprobante');
        }

        $tipo = $factura->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        if (!in_array($tipo->Grupo, [8, 13])) {
            throw new \Exception('No se puede cambiar este tipo de comprobante.');
        }

        $map = [
            // grupo 8
            33 => 72,
            34 => 73,
            35 => 74,
            36 => 75,
            60 => 76,
            72 => 33,
            73 => 34,
            74 => 35,
            75 => 36,
            76 => 60,
            // grupo 13
            41 => 82,
            42 => 83,
            43 => 84,
            44 => 85,
            62 => 86,
            82 => 41,
            83 => 42,
            84 => 43,
            85 => 44,
            86 => 62,
        ];

        try {
            $modelFacturas->cambiarTipoComprobante($factura, $map[$factura->TipoDeComprobante]);
            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    /*
      public function paso3Action()
      {
      $this->_helper->viewRenderer->setNoRender(true);

      $request = $this->getRequest();
      $idFactura = $request->getParam('idFactura');

      try {
      $M_FC = new Facturacion_Model_DbTable_FacturasCompras();
      $M_FC->insertarConceptosDesdeControlador($idFactura);

      echo '{success: true}';
      } catch (Rad_Db_Table_Exception $e) {
      echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
      }
      }
     */
}
