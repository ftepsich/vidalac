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
class Rrhh_PluriempleoController extends Rad_Window_Controller_Action
{
    protected $title = 'Pluriempleo';

    public function initWindow()
    {
        /**
         * Grilla Tablas Secundarias
         */
        $config->abmWindowTitle = 'Servicios';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Servicios';
        $config->loadAuto = false;
        $config->detailGrid = $detailGridTD;
        $config->id = $this->getName() . '_gridDetalle';

        $this->view->gridDetalles = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasGananciasPluriempleoDetalle',
            $config,
            'abmeditor'
        );
        unset($config);

        $config->abmWindowTitle = 'Periodos';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Periodos';
        $config->loadAuto = false;
        $config->detailGrid = $detailGridTD;
        $config->id = $this->getName() . '_gridPeriodos';

        $this->view->gridPeriodos = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasGananciasPluriempleoPeriodos',
            $config,
            'abmeditor'
        );

        unset($config);

        $detailGrids = array();

        $dg->id = $this->getName() . '_gridPeriodos';
        $dg->remotefield = 'PersonaGananciaPluriempleo';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);     

        $dg->id = $this->getName() . '_gridDetalle';
        $dg->remotefield = 'PersonaGananciaPluriempleo';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg); 

        /**
         * ---------------------------------------------------------------------
         * -- Principal
         * ---------------------------------------------------------------------
         */
        $config->abmWindowTitle = 'Pluriempleo';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 350;
        $config->detailGrid = $detailGrids;

        $this->view->grid = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasGananciasPluriempleo',
            $config,
            'abmeditor'
        );
    }

}