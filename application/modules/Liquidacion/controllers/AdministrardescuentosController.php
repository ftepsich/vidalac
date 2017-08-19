<?php

/**
 * Liquidacion_AdministrarDescuentosController
 *
 * Administrar las Formulas de los productos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_AdministrarDescuentosController
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_AdministrarDescuentosController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Adelantos, Prestamos, Embargos u otros';

    public function initWindow()
    {
        /**
         * Grilla de Variables Generales Detalle
         */
        $config->loadAuto                       = false;
        $config->id                             = $this->getName() . '_Grid_DetallesDescuentos';
        $this->view->grid_DetallesDescuentos    = $this->view->radGrid(
            'Liquidacion_Model_DbTable_DescuentosDetalles',
            $config,
            ''
        );
        unset($config);

        /**
         * Grilla Variables Generales
         */
        $detailGrid->id             = $this->getName() . '_Grid_DetallesDescuentos';
        $detailGrid->remotefield    = 'Descuento';
        $detailGrid->localfield     = 'Id';     

        $config->detailGrid         = $detailGrid;

        $this->view->grid           = $this->view->radGrid(
            'Liquidacion_Model_DbTable_Descuentos',
            $config,
            'abmeditor'
        );
        unset($config);
    }

}
