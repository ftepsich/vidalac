<?php

/**
 * Produccion_OrdenesDeProduccionesController
 *
 * Controlador de Ordenes de Producciones
 *
 * @package Aplicacion
 * @subpackage Produccion
 * @class Produccion_OrdenesDeProduccionesController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Produccion_ProduccionController extends Rad_Window_Controller_Action
{

    protected $title = 'Producción';

    public function initWindow()
    {
        /**
         * Ordenes de produccion Grid
         */
        $parametrosAdc = null;
        $parametrosAdc->abmWindowTitle   = 'Orden de Produccion';
        $parametrosAdc->abmWindowWidth   = 900;
        $parametrosAdc->abmWindowHeight  = 560;
        $parametrosAdc->fetch            = 'Produccion';
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        $parametrosAdc->viewConfig = new Zend_Json_Expr("{
            forceFit: true,
            // enableRowBody: true,
            // showArticulo: true,
            // getRowClass: function(record, rowIndex, p, store) {
            //     if (this.showArticulo) {
            //         p.body = '<p><b>Articulo:</b> '+record.data.Articulo_cdisplay+'</p> ';
            //         return 'x-grid3-row-expanded';
            //     }
            //     return 'x-grid3-row-collapsed';
            // }
        }");

        $this->view->grid = $this->view->radGrid(
            'Produccion_Model_DbTable_OrdenesDeProducciones',
            $parametrosAdc,
            '',
            'produccion'
        );

        /**
         * Log movimientos Mmi Grid
         */
        $parametrosAdc = null;
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->title    = 'Actividad';
        $parametrosAdc->view     = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,

                groupTextTpl: '<span style=\'font-size:15px;\'>Turno: {[Ext.util.Format.date(values.rs[0].data[\"ProduccionesComienzo\"],\"d/m/Y H:i\")]} a {[Ext.util.Format.date(values.rs[0].data[\"ProduccionesFinal\"],\"d/m/Y H:i\")]}</span>',
            })
        ");

        $parametrosAdc->sm       = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({ singleSelect: true })");

        $this->view->gridLog = $this->view->radGrid(
            'Produccion_Model_DbTable_ProduccionesMmisMovimientos',
            $parametrosAdc,
            ''
        );

        /**
         * Mmis producidos Grid
         */
        $parametrosAdc = null;
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->title    = 'Producido';
        $parametrosAdc->tbar     = new Zend_Json_Expr("{}");
        $parametrosAdc->sm       = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        $parametrosAdc->view     = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,
                groupTextTpl: '<span style=\'font-size:15px;\'>Turno: {[Ext.util.Format.date(values.rs[0].data[\"ProduccionesComienzo\"],\"d/m/Y H:i\")]} a {[Ext.util.Format.date(values.rs[0].data[\"ProduccionesFinal\"],\"d/m/Y H:i\")]}</span>',
            })
        ");

        $this->view->produccionesMmis = $this->view->radGrid(
            'Produccion_Model_DbTable_ProduccionesMmis',
            $parametrosAdc,
            ''
        );

        /**
         * Actividades Grid
         */
        $parametrosAdc = null;
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        $parametrosAdc->region = 'center';
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->tbar =  new Zend_Json_Expr("{}");

        $parametrosAdc->viewConfig  = new Zend_Json_Expr("{
            enableRowBody: true,
            forceFit: true,
            getRowClass: function(rec, idx, p, store) {
                p.body = '<div class=\"actividades-target\"></div>';
            }
        }");


        $this->view->actividadesGrid = $this->view->radGrid(
            'Produccion_Model_DbTable_Actividades',
            $parametrosAdc,
            ''
        );

        /**
         * Ordenes de Producciones MMi
         */
        $parametrosAdc = null;
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: false})");
        $parametrosAdc->region = 'center';
        $parametrosAdc->title  = 'Materia Prima';
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->tbar =  new Zend_Json_Expr("{}");
        $parametrosAdc->view     = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,
                hideGroupedColumn: true,
                groupTextTpl: '<span style=\'font-size:15px;\'>{text}</span>'
            })
        ");
        $this->view->ordenesProduccionMmiGrid = $this->view->radGrid(
            'Produccion_Model_DbTable_OrdenesDeProduccionesMmis',
            $parametrosAdc,
            ''
        );
    }
}
