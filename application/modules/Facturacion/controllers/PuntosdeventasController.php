<?php

/**
 * Gestion de Puntos de Ventas
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @extends     Rad_Window_Controller_Action
 * @copyright   SmartSoftware Argentina 2010
 */
class Facturacion_PuntosDeVentasController extends Rad_Window_Controller_Action
{
    protected $title = 'Puntos de Ventas';

    public function initWindow ()
    {
        $parametrosAdc = new StdClass;
        $parametrosAdc->abmWindowTitle  = 'Puntos de Venta';
        $parametrosAdc->abmWindowWidth  = 400;
        $parametrosAdc->flex            = 2;
        $parametrosAdc->abmWindowHeight = 350;
        $parametrosAdc->sm              = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect: true})');

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_PuntosDeVentas',
            $parametrosAdc,
            'abmeditor'
        );
    }

}
