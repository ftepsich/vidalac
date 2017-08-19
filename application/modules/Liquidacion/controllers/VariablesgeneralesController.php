<?php

/**
 * Liquidacion_VariablesGeneralesController
 *
 * Administrar las Variables Generales
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Liquidacion_VariablesGeneralesController
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_VariablesGeneralesController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Variables Generales';

    public function initWindow()
    {
        /**
         * Grilla de Variables Generales Detalle
         */
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVG_Detalle';
        $this->view->gridVG_Detalle   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_VariablesDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Variables Generales
         */
        $detailGrid->id             = $this->getName() . '_GridVG_Detalle';
        $detailGrid->remotefield    = 'Variable';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;

        $this->view->grid           = $this->view->radGrid(
            'Liquidacion_Model_DbTable_Variables_Variables',
            $config,
            'abmeditor'
        );
        unset($config);
    }

}
