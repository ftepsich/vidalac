<?php

/**
 * Base_AdministrarListasDePreciosController
 *
 * Administrar Listas de Precios
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarListasDePreciosController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarListasDePreciosController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Listas de Precios';

    public function initWindow()
    {
        /**
         * Grilla Productos Listas de Preciso Detalle
         */
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridPLDP_Detalle';
        $this->view->gridPLDP_Detalle   = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosListasDePreciosDetalle',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Productos Listas de Preciso
         */
        $detailGrid->id             = $this->getName() . '_GridPLDP_Detalle';
        $detailGrid->remotefield    = 'ListaDePrecio';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;
        $config->iniSection         = 'reducido';

        $this->view->grid           = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosListasDePrecios',
            $config,
            'abmeditor'
        );
        unset($config);
    }

}
