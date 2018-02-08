<?php

/**
 * Liquidacion_VariablesParametrosController
 *
 * Administrar las Variables de Parametros
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Liquidacion_VariablesParametrosController
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_VariablesParametrosController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Parametros';

    public function initWindow()
    {
        /**
         * Grilla de Variables Generales Detalle
         */
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVP_Detalle';
        $this->view->gridVP_Detalle   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ParametrosDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Variables Generales
         */
        $detailGrid->id             = $this->getName() . '_GridVP_Detalle';
        $detailGrid->remotefield    = 'Variable';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;

        $this->view->grid           = $this->view->radGrid(
            'Liquidacion_Model_DbTable_Variables_Parametros',
            $config,
            'abmeditor'
        );
        unset($config);
    }

}
