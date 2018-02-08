<?php
/**
 * Produccion_ViewProduccionController
 *
 * Controlador de Ordenes de Producciones
 *
 * @package Aplicacion
 * @subpackage Produccion
 * @class Produccion_ViewProduccionController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Produccion_ViewProduccionController extends Rad_Window_Controller_Action
{

    protected $title = 'Ver Actividad Producción';

    public function initWindow()
    {
        /**
         * Ordenes de produccion Grid
         */
        $parametrosAdc = null;
        $parametrosAdc->fetch = 'Produccion';
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
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
                groupTextTpl: '{[Ext.util.Format.date(values.rs[0].data[\"ProduccionesComienzo\"],\"d/m/Y H:i:s\")]} a {[Ext.util.Format.date(values.rs[0].data[\"ProduccionesFinal\"],\"d/m/Y H:i:s\")]}',
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
        $parametrosAdc->sm       = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        $parametrosAdc->view     = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,
                groupTextTpl: '{[Ext.util.Format.date(values.rs[0].data[\"ProduccionesComienzo\"],\"d/m/Y H:i:s\")]} a {[Ext.util.Format.date(values.rs[0].data[\"ProduccionesFinal\"],\"d/m/Y H:i:s\")]}',
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
        $parametrosAdc->title  = 'Empleados';
        $parametrosAdc->loadAuto = false;
        
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
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        $parametrosAdc->region = 'center';
        $parametrosAdc->title  = 'Materia Prima';
        $parametrosAdc->loadAuto = false;
        $this->view->ordenesProduccionMmiGrid = $this->view->radGrid(
            'Produccion_Model_DbTable_OrdenesDeProduccionesMmis',
            $parametrosAdc,
            ''
        );
        
        /**
         * Turnos
         */    
        $parametrosAdc = null;
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->layout = 'fit';
        $this->view->turnosGrid = $this->view->radGrid(
            'Produccion_Model_DbTable_Producciones',
            $parametrosAdc,
            ''
        );
    }
    
    
    public function getproduccionporturnoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = $this->getRequest()->id;
        
        $model = new Produccion_Model_ProduccionesEstadisticas();
        $data = $model->getCantidadProduccionPorTurno($id, 2);
        $rtn['rows']    = $data;
        $rtn['count']   = count($data);
        $rtn['success'] = true;
        
        $this->_sendJsonResponse($rtn);
    }
}
