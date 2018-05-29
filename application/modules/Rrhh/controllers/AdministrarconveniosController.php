<?php

/**
 * Rrhh_AdministrarConveniosController
 *
 * Administrador de Convenios Colectivos de Trabajo
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Rrhh
 * @class Rrhh_AdministrarConveniosController
 * @extends Rad_Window_Controller_Action
 */
class Rrhh_AdministrarConveniosController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Convenios Colectivos de Trabajo';

    public function initWindow()
    {
        /**
         * ---------------------------------------------------------------------
         * -- GENERALES
         * ---------------------------------------------------------------------
         */
        


        /**
         * Grilla CategoriasDetalles
         */
        $config->abmWindowTitle = 'Valores';
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 180;
        $config->title = 'Categorias Valores';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCategoriasDetalles';

        $this->view->gridCategoriasDetalles = $this->view->radGrid(
            'Rrhh_Model_DbTable_ConveniosCategoriasDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        $detailGridCD = array();

        $dg->id             = $this->getName() . '_GridCategoriasDetalles';
        $dg->remotefield    = 'ConvenioCategoria';
        $dg->localfield     = 'Id';
        $detailGridCD[]      = $dg;
        unset($dg);  

        /**
         * Grilla Categorias
         */
        $config->abmWindowTitle = 'Categorias';
        $config->abmWindowWidth = 800;
        $config->abmWindowHeight = 400;
        $config->title = 'Categorias';
        $config->loadAuto = false;
        $config->detailGrid = $detailGridCD;
        $config->id = $this->getName() . '_GridCategorias';
        $config->iniSection = 'listado';

        $this->view->gridCategorias = $this->view->radGrid(
            'Rrhh_Model_DbTable_ConveniosCategorias',
            $config,
            'abmeditor'
        );
        unset($detailGridCD);
        unset($config);

        /**
         * Grilla de Licencias
         */
        $config->abmWindowTitle = 'Licencias';
        $config->abmWindowWidth = 800;
        $config->abmWindowHeight = 280;
        $config->title = 'Licencias';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridLicencias';

        $this->view->gridLicencias = $this->view->radGrid(
            'Rrhh_Model_DbTable_ConveniosLicencias',
            $config,
            'abmeditor'
        );
        unset($config);

        /* --------------------------------------------------------------------------- */

        /**
         * Grilla TablasDetalles por Rangos
         */
        $config->abmWindowTitle = 'Valores';
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 220;
        $config->title = 'Valores';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridTablasRangosDetalles';

        $this->view->gridTablasRangosDetalles = $this->view->radGrid(
            'Rrhh_Model_DbTable_LiquidacionesTablasRangosDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        $detailGridTD = array();

        $dg->id             = $this->getName() . '_GridTablasRangosDetalles';
        $dg->remotefield    = 'LiquidacionTabla';
        $dg->localfield     = 'Id';
        $detailGridTD[]      = $dg;
        unset($dg);  

        /**
         * Grilla Tablas Rangos
         */
        $config->abmWindowTitle = 'Tablas por Rango';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Tablas';
        $config->loadAuto = false;
        $config->detailGrid = $detailGridTD;
        $config->id = $this->getName() . '_GridTablasRangos';

        $this->view->gridTablasRangos = $this->view->radGrid(
            'Rrhh_Model_DbTable_LiquidacionesTablasRangos',
            $config,
            'abmeditor'
        );
        unset($detailGridTD);
        unset($config);

        /* --------------------------------------------------------------------------- */

        /**
         * Grilla TablasDetalles Escalares
         */
        $config->abmWindowTitle = 'Valores';
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 220;
        $config->title = 'Valores';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridTablasEscalaresDetalles';

        $this->view->gridTablasEscalaresDetalles = $this->view->radGrid(
            'Rrhh_Model_DbTable_LiquidacionesTablasEscalaresDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        $detailGridTD = array();

        $dg->id             = $this->getName() . '_GridTablasEscalaresDetalles';
        $dg->remotefield    = 'LiquidacionTabla';
        $dg->localfield     = 'Id';
        $detailGridTD[]      = $dg;
        unset($dg);  

        /**
         * Grilla Tablas Escalares
         */
        $config->abmWindowTitle = 'Tablas Escalares';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Tablas';
        $config->loadAuto = false;
        $config->detailGrid = $detailGridTD;
        $config->id = $this->getName() . '_GridTablasEscalares';

        $this->view->gridTablasEscalares = $this->view->radGrid(
            'Rrhh_Model_DbTable_LiquidacionesTablasEscalares',
            $config,
            'abmeditor'
        );
        unset($detailGridTD);
        unset($config);

        /* --------------------------------------------------------------------------- */

        /**
         * Grilla TablasDetalles Grupos
         */
        $config->abmWindowTitle = 'Valores';
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 220;
        $config->title = 'Valores';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridTablasGruposDetalles';

        $this->view->gridTablasGruposDetalles = $this->view->radGrid(
            'Rrhh_Model_DbTable_LiquidacionesTablasGruposDetalles',
            $config,
            'editor'
        );
        unset($config);

        $detailGridTD = array();

        $dg->id             = $this->getName() . '_GridTablasGruposDetalles';
        $dg->remotefield    = 'LiquidacionTabla';
        $dg->localfield     = 'Id';
        $detailGridTD[]      = $dg;
        unset($dg);  

        /**
         * Grilla Tablas Escalares
         */
        $config->abmWindowTitle = 'Tablas Categorias';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Tablas';
        $config->loadAuto = false;
        $config->detailGrid = $detailGridTD;
        $config->id = $this->getName() . '_GridTablasGrupos';

        $this->view->gridTablasGrupos = $this->view->radGrid(
            'Rrhh_Model_DbTable_LiquidacionesTablasGrupos',
            $config,
            'abmeditor'
        );
        unset($detailGridTD);
        unset($config);

        /**
         * ---------------------------------------------------------------------
         * -- Convenios
         * ---------------------------------------------------------------------
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridCategorias';
        $dg->remotefield = 'Convenio';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridLicencias';
        $dg->remotefield = 'Convenio';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridTablasRangos';
        $dg->remotefield = 'Convenio';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridTablasEscalares';
        $dg->remotefield = 'Convenio';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);     

        $dg->id = $this->getName() . '_GridTablasGrupos';
        $dg->remotefield = 'Convenio';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg); 

        /**
         * ---------------------------------------------------------------------
         * -- Convenios
         * ---------------------------------------------------------------------
         */
        $config->abmWindowTitle = 'Convenios';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 350;
        $config->detailGrid = $detailGrids;
        // $config->iniSection = 'reducido';

        $this->view->grid = $this->view->radGrid(
            'Rrhh_Model_DbTable_Convenios',
            $config,
            'abmeditor'
        );
    }

}