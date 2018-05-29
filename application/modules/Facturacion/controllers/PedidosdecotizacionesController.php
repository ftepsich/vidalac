<?php

/**
 *
 * Facturacion_PedidosdecotizacionesController
 *
 * Controlador Pedidos de Cotizaciones
 *
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_PedidosdecotizacionesController
 * @extends     Rad_Window_Controller_Action
 * @copyright   SmartSoftware Argentina 2010
 */
class Facturacion_PedidosdecotizacionesController extends Rad_Window_Controller_Action
{

    protected $title = 'Pedidos de Cotizaciones';

    public function initWindow ()
    {

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA HIJA
        // ----------------------------------------------------------------------------------------------------------
        $parametrosAdc->withPaginator = false;
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->id = 'PdCAGrillaHija';
        //$parametrosAdc->abmForm         = new Zend_Json_Expr($abmForm);

        $this->view->gridArt = $this->view->radGrid(
                        'Facturacion_Model_DbTable_PedidosDeCotizacionesArticulos',
                        $parametrosAdc,
                        'abmeditor'
        );

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------

        $detailGrid->id = 'PdCAGrillaHija';
        $detailGrid->remotefield = 'Comprobante';
        $detailGrid->localfield = 'Id';

        $this->view->form = $this->view->radForm(
                        'Facturacion_Model_DbTable_PedidosDeCotizaciones',
                        'datagateway'
        );

        $parametrosAdc = null;
        $parametrosAdc->abmWindowTitle = 'Pedido de Cotizacion';
        $parametrosAdc->abmWindowWidth = 800;
        $parametrosAdc->abmWindowHeight = 580;
        $parametrosAdc->sm = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect: true})');
        $parametrosAdc->detailGrid = array($detailGrid);

        $this->view->grid = $this->view->radGrid(
                        'Facturacion_Model_DbTable_PedidosDeCotizaciones',
                        $parametrosAdc,
                        'abmeditor'
        );
    }

}
