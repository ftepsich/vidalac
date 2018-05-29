<?php

/**
 * Base_AdministrarBancosController
 *
 * Administrador de Bancos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarBancosController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarChequerasController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Chequeras';

    public function initWindow ()
    {
        /**
         * Cheques
         */
        $config->title = 'Cheques';
        $config->loadAuto = false;
        $config->detailGrid = $detailGrids;
        $config->id = $this->getName() . '_GridChequesPropios';

        $this->view->gridChequesPropios = $this->view->radGrid(
                        'Base_Model_DbTable_ChequesPropios',
                        $config,
                        'abmeditor'
        );
        unset($config);

        /**
         * Chequeras
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridChequesPropios';
        $dg->remotefield = 'Chequera';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->abmWindowTitle = 'Chequeras';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        //$config->iniSection = 'reducido';

        $this->view->grid = $this->view->radGrid(
                        'Base_Model_DbTable_Chequeras',
                        $config,
                        'abmeditor'
        );
    }

}