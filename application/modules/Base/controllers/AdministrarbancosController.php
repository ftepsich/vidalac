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
class Base_AdministrarBancosController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Bancos';

    public function initWindow()
    {
        /**
         * ---------------------------------------------------------------------
         * -- DATOS DE LAS SUCURSALES
         * ---------------------------------------------------------------------
         */

        /**
         * Cuentas Bancarias Propias
         */
        $config->abmWindowTitle = 'Cuentas Bancarias';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Cuentas Bancarias';
        $config->loadAuto = false;
        $config->iniSection = 'reducido';
        $config->id = $this->getName() . '_GridCtasBancarias';
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Base_Model_DbTable_CuentasBancariasPropias',
            'datagateway',
            'reducido'
        ));

        $this->view->gridCtasBancarias = $this->view->radGrid(
            'Base_Model_DbTable_CuentasBancariasPropias',
            $config,
            'abmeditor'
        );
        unset($config);
        
        /**
         * Grilla Telefonos de Sucursales
         */
        $config->abmWindowTitle = 'Telefonos de Sucursales';
        $config->abmWindowWidth = 720;
        $config->abmWindowHeight = 320;
        $config->title = 'Telefonos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridTelSucursales';

        $this->view->gridTelSucursales = $this->view->radGrid(
            'Base_Model_DbTable_Telefonos',
            $config,
            'abmeditor'
        );
        unset($config);
        
        
        /**
         * Sucursales Bancarias y relaciones
         */
        
        $detailGrids = array();
        
        $dg->id = $this->getName() . '_GridCtasBancarias';
        $dg->remotefield = 'BancoSucursal';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridTelSucursales';
        $dg->remotefield = 'BancoSucursal';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);
        
        $config->abmWindowTitle = 'Sucursales';
        $config->abmWindowWidth = 650;
        $config->abmWindowHeight = 300;
        $config->title = 'Sucursales';
        $config->loadAuto = false;
        $config->detailGrid = $detailGrids;
        $config->id = $this->getName() . '_GridSucursales';

        $this->view->gridSucursales = $this->view->radGrid(
            'Base_Model_DbTable_BancosSucursales',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * ---------------------------------------------------------------------
         * -- BANCOS
         * ---------------------------------------------------------------------
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridSucursales';
        $dg->remotefield = 'Banco';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->abmWindowTitle = 'Bancos';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        //$config->iniSection = 'reducido';

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Bancos',
            $config,
            'abmeditor'
        );
    }

}
