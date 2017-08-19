<?php

/**
 * Base_GruposDeArticulosController
 *
 * Crea grupos y Subgrupos de articulos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_GruposDeArticulosController
 * @extends Rad_Window_Controller_Action
 */
class Base_GruposDeArticulosController extends Rad_Window_Controller_Action
{
    protected $title = 'Grupos y Subgrupos de Articulos';

    public function initWindow()
    {

        /**
         * Grilla de SubGrupos de Articulos
         */
        $config->loadAuto                       = false;
        $config->id                             = $this->getName() . '_GridSGA';
        $this->view->gridSubGrupoDeArticulo     = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosSubGrupos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla de Grupos de Articulos
         */
        $detailGrid->id             = $this->getName() . '_GridSGA';
        $detailGrid->remotefield    = 'ArticuloGrupo';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;

        $this->view->grid           = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosGrupos',
            $config,
            'abmeditor'
        );
        unset($config);
   
    }

}