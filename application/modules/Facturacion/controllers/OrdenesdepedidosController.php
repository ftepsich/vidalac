<?php

class Facturacion_OrdenesDePedidosController extends Rad_Window_Controller_Action
{
    protected $title = 'Pedidos de Clientes';
    
    public function initWindow ()
    {
       
        // ----------------------------------------------------------------------------------------------------------
        // GRILLA HIJA
        // ----------------------------------------------------------------------------------------------------------
        $parametrosAdc->withPaginator   = false;
        $parametrosAdc->loadAuto        = false;
        $parametrosAdc->id              = 'OdPAGrillaHija';
        
        $this->view->gridArt = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDePedidosArticulos',
            $parametrosAdc ,
            'abmeditor',
            'wizard'
        );

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------
        $this->view->form = $this->view->radForm(   
            'Facturacion_Model_DbTable_OrdenesDePedidos',  // Nombre del Modelo
            'datagateway'
        );
        
        $parametrosAdc = null;
        $parametrosAdc->abmWindowTitle  = 'Orden de Pedido';
        $parametrosAdc->abmWindowWidth  = 900;
        $parametrosAdc->abmWindowHeight = 560;
        $parametrosAdc->sm              = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect: true})');
        
        $this->view->grid = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDePedidos',
            $parametrosAdc ,
            'abmeditor'
        );
    }

}