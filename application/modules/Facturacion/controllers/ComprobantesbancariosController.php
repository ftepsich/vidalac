<?php
/**
 * Facturacion_ComprobantesBancariosController
 * 
 * Controlador Comprobantes Bancarios
 *
 *
 * @package Aplicacion
 * @subpackage Facturacion
 * @class Facturacion_ComprobantesBancariosController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Facturacion_ComprobantesBancariosController extends Rad_Window_Controller_Action
{
    protected $title = 'Comprobantes Bancarios';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow()
    {
        /**
         * Formulario de Comprobante Bancario (Paso 0)
         */
        $this->view->form = $this->view->radForm(
            'Facturacion_Model_DbTable_ComprobantesBancarios',
            'datagateway',
            'wizard'
        );

        /**
         * Asociar Cheques a Comprobante Bancario (Paso 1)
         */
        $grillaCH = $this->view->RadGridManyToMany(
                        'Base_Model_DbTable_Cheques',
                        'Facturacion_Model_DbTable_ComprobantesBancariosCheques',
                        'Facturacion_Model_DbTable_ComprobantesBancarios',
                        array (
                            'title'           => 'Cheques',
                            'xtype'           => 'radformmanytomanyeditorgridpanel',
                            'detailGrid'      => $detailGrid,
                            'fetch'           => 'AsociadosYFaltantesDeCobrar',
                            'abmWindowTitle'  => 'Agregar cheque',
                            'abmWindowWidth'  => 940,
                            'abmWindowHeight' => 620,
                            'withPaginator'   => false,
                            'buildToolbar'    => new Zend_Json_Expr('function() {}')
                        ),
                        'reducido'
        );
        $this->view->gridCheque = $grillaCH;

        /**
         * Articulos del Comprobante Bancario (Paso 2)
         */
        // $dg->id          = $this->getName() . '_GridArticulosRel';
        // $dg->remotefield = 'FAID';
        // $dg->localfield  = 'Id';

        $config->abmWindowTitle    = 'Artículo';
        $config->abmWindowWidth    = 840;
        $config->abmWindowHeight   = 260;
        $config->withPaginator     = false;
        $config->title             = 'Artículo';
        $config->loadAuto          = false;
        $config->autoSave          = true;
        $config->iniSection        = 'wizard';
        // $config->detailGrid        = array($dg);
        $this->view->gridArticulos = $this->view->radGrid(
            'Facturacion_Model_DbTable_ComprobantesBancariosArticulos',
            $config,
            'abmeditor',
            'wizard'
        );
        unset($config);

        /**
         * Articulos Relacionados (Paso 2 -/ subgrilla)
         */
        // $config->withPaginator        = false;
        // $config->loadAuto             = false;
        // $config->id                   = $this->getName() . '_GridArticulosRel';
        // $this->view->gridArticulosRel = $this->view->radGrid(
        //                 'Facturacion_Model_DbTable_vRelFacturasArticulosOrdenesArticulos',
        //                 $config
        // );
        // unset($config);

        /**
         * Asociar Conceptos impositivos a Comprobante Bancario (Paso 3)
         */
        $config->abmWindowTitle  = 'Concepto Impositivo';
        $config->abmWindowWidth  = 500;
        $config->abmWindowHeight = 180;
        $config->withPaginator   = false;     
        $config->title           = 'Conceptos Impositivos';
        $config->loadAuto        = false;
        $config->autoSave        = true;
        $config->iniSection      = 'wizard';
        $config->buildToolbar    = new Zend_Json_Expr("
            function() {
                var id = this.getId();
                this.tbar = new Ext.Toolbar(
                    {
                        items:[
                            {
                                text:       'Agregar',
                                id:         id+'-Agregar',
                                iconCls:    'add',
                                handler:    this.createRow,
                                scope:      this,
                                hidden:     (this.topButtons && this.topButtons.add == false) ? true : false,
                                disabled:   !this.loadAuto
                            },
                            '-',
                            {
                                text:       'Borrar',
                                id:         id+'-Borrar',
                                iconCls:    'remove',
                                handler:    function(){
                                    this.deleteRows();
                                },
                                scope:      this,
                                hidden:     (this.topButtons && this.topButtons.del == false) ? true : false,
                                disabled:   !this.loadAuto
                            }
                        ]
                    }
                );
            }");

        $grillaFCC = $this->view->radGrid(
            'Facturacion_Model_DbTable_ComprobantesBancariosConceptos',
            $config,
            'abmeditor',
            'wizard'
        );
        $this->view->gridCI = $grillaFCC;
        unset($config);

        /**
         * Grilla Comprobante Bancarios
         */
        $config->abmForm = null;
        $config->viewConfig = new Zend_Json_Expr("
        {
            forceFit: true,
            enableRowBody: false,
            getRowClass: function(record, rowIndex, p, store) {
                var tc = record.get('Cerrado');
                return (tc == 0)? 'x-grid3-row-red' : '';
            }
        }");
        $config->id       = $this->getName() . '_Grid';
        $config->loadAuto = false;
        $this->view->grid = $this->view->radGrid(
            'Facturacion_Model_DbTable_ComprobantesBancarios',
            $config,
            'abmeditor'
        );
        unset($config);
    }

}
