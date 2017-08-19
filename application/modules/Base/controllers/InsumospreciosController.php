<?php

/**
 * Base_InsumosPreciosController
 *
 * Precios de los Insumos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_InsumosPreciosController
 * @extends Rad_Window_Controller_Action
 */
class Base_InsumosPreciosController extends Rad_Window_Controller_Action
{

    protected $title = 'Precio de los Insumos';

    public function initWindow()
    {
        /**
         * Grilla Lista de Precios Comprados
         */
        $config->abmWindowTitle = 'Lista de Precios Comprados';
        $config->abmWindowWidth = 900;
        $config->abmWindowHeight = 190;
        $config->iniSection = 'listaprecio';
        $config->title = '';
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridPLDP';
        $config->flex = 1;

        $this->view->gridPLDP = $this->view->radGrid(
            'Base_Model_DbTable_PersonasListasDePrecios',
            $config,
            null
        );
        unset($config);

        /**
         * Grilla Lista de Precios Informados
         */
        $config->abmWindowTitle = 'Lista de Precios Informados';
        $config->abmWindowWidth = 900;
        $config->abmWindowHeight = 190;
        $config->iniSection = 'listaprecio';
        $config->title = '';
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridPLDP_Informados';
        $config->flex = 1;

        $this->view->gridPLDP_Informados = $this->view->radGrid(
            'Base_Model_DbTable_PersonasListasDePreciosInformados',
            $config,
            null
        );
        unset($config);

        /**
         * Grilla Lista de Precios Historicos
         */
        /*
          $config->abmWindowTitle = 'Lista de Precios Historicos';
          $config->abmWindowWidth = 900;
          $config->abmWindowHeight = 190;
          $config->iniSection = 'listaprecio';
          $config->title = '';
          $config->loadAuto = false;
          $config->withPaginator = false;
          $config->id = $this->getName() . '_GridPLDP_Historicos';
          $config->flex = 1;

          $this->view->gridPLDP_Historicos = $this->view->radGrid(
          'Base_Model_DbTable_ProveedoresListasDePreciosHistoricos',
          $config,
          null
          );
          unset($config);
         */
        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------

        $detailGrid = array();

        $dg->id = $this->getName() . '_GridPLDP';
        $dg->remotefield = 'Articulo';
        $dg->localfield = 'Id';
        $detailGrid[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridPLDP_Informados';
        $dg->remotefield = 'Articulo';
        $dg->localfield = 'Id';
        $detailGrid[] = $dg;
        unset($dg);

        /*        $dg->id = $this->getName() . '_GridPLDP_Historicos';
          $dg->remotefield = 'Articulo';
          $dg->localfield = 'Id';
          $detailGrid[] = $dg;
          unset($dg);
         */
        $config->iniSection = 'reducido';
        $config->abmWindowTitle = 'Proveedores';
        $config->abmWindowWidth = 400;
        $config->abmWindowHeight = 400;
        $config->iniSection = 'reducido';
        $config->detailGrid = $detailGrid;
        $config->fetch = 'EsInsumo';

        $this->view->grid = $this->view->radGrid(
                        'Base_Model_DbTable_Articulos',
                        $config,
                        null
        );
        unset($config);
    }

}
