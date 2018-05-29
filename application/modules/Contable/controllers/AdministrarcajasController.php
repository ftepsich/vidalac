<?php

/**
 * Base_AdministrarCajasController
 *
 * Administrador de Caja
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarCajaController
 * @extends Rad_Window_Controller_Action
 */
class Contable_AdministrarCajasController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Cajas';

    public function initWindow()
    {
        /**
         * ---------------------------------------------------------------------
         * -- DATOS DE LOS MOVIMIENTOS DE CAJA
         * ---------------------------------------------------------------------
         */
        $config->abmWindowTitle = 'Cajas Movimientos';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 400;
        // $config->title = 'Cajas Movimientos';
        $config->loadAuto = false;
        $config->tbar = array();
        //$config->iniSection = 'reducido';
        $config->id = $this->getName() . '_GridCajasMovimientos';
        $config->abmFormEntradas = new Zend_Json_Expr($this->view->radForm(
            'Contable_Model_DbTable_CajasMovimientosDeEntradas',
            'datagateway',
            null
        ));
        $config->abmFormSalidas = new Zend_Json_Expr($this->view->radForm(
            'Contable_Model_DbTable_CajasMovimientosDeSalidas',
            'datagateway',
            null
        ));

        $this->view->gridCajasMovimientos = $this->view->radGrid(
            'Contable_Model_DbTable_CajasMovimientos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * ---------------------------------------------------------------------
         * -- Cajas
         * ---------------------------------------------------------------------
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridCajasMovimientos';
        $dg->remotefield = 'Caja';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->abmWindowTitle = 'Cajas';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        //$config->iniSection = 'reducido';

        $this->view->grid = $this->view->radGrid(
            'Contable_Model_DbTable_Cajas',
            $config,
            'abmeditor'
        );
    }

}