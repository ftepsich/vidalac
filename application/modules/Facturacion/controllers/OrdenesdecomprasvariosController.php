<?php

/**
 * Facturacion_OrdenesDeComprasController
 *
 * Controlador de Ordenes de Compras
 *
 * @package Aplicacion
 * @subpackage Facturacion
 * @class Facturacion_OrdenesDeComprasControllerVarios
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Facturacion_OrdenesDeComprasVariosController extends Rad_Window_Controller_Action
{

    protected $title = 'Ordenes de Compras Varios';

    public function initWindow()
    {

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA HIJA
        // ----------------------------------------------------------------------------------------------------------

        $parametrosAdc->withPaginator = false;
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->id = 'OdCAGrillaHijaVarios';

        $this->view->gridArt = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDeComprasArticulosVarios',
            $parametrosAdc,
            'abmeditor',
            'wizard'
        );

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------

        $this->view->form = $this->view->radForm(
            'Facturacion_Model_DbTable_OrdenesDeComprasVarios', // Nombre del Modelo
            'datagateway'
        );

        $parametrosAdc = null;
        $parametrosAdc->abmWindowTitle = 'Orden de Compra';
        $parametrosAdc->abmWindowWidth = 900;
        $parametrosAdc->abmWindowHeight = 560;
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");

        $this->view->grid = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDeComprasVarios',
            $parametrosAdc,
            'abmeditor'
        );
    }

}
