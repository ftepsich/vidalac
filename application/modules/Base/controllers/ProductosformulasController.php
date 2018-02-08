<?php

/**
 * Base_ProductosFormulasController
 *
 * Administrar las Formulas de los productos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_ProductosFormulasController
 * @extends Rad_Window_Controller_Action
 */
class Base_ProductosFormulasController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Formulas';

    public function initWindow()
    {
        /**
         * Grilla de Variables Generales Detalle
         */
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridAV_DetalleFormulas';
        $this->view->grid_AVDF   = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosVersionesDetallesFormulas',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Variables Generales
         */
        $detailGrid->id             = $this->getName() . '_GridAV_DetalleFormulas';
        $detailGrid->remotefield    = 'ArticuloVersionPadre';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;
        $config->fetch              = 'EsProducto';        

        $this->view->grid           = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosVersiones',
            $config,
            ''
        );
        unset($config);
    }

}
