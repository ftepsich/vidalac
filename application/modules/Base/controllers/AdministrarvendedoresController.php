<?php

/**
 * Base_AdministrarVendedoresController
 *
 * Administrador de Vendedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarVendedoresController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarVendedoresController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Vendedores';

    public function initWindow()
    {
        /**
         * ---------------------------------------------------------------------
         * -- GENERALES
         * ---------------------------------------------------------------------
         */
        
        /**
         * Grilla Direcciones
         */
        $config->abmWindowTitle = 'Direcciones';
        $config->abmWindowWidth = 650;
        $config->abmWindowHeight = 300;
        $config->title = 'Direcciones';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridDirecciones';

        $this->view->gridDirecciones = $this->view->radGrid(
            'Base_Model_DbTable_Direcciones',
            $config,
            'abmeditor','vendedores'
        );
        unset($config);

        /**
         * Grilla de Telefonos
         */
        $config->abmWindowTitle = 'Telefonos';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Telefonos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridTelefonos';

        $this->view->gridTelefonos = $this->view->radGrid(
            'Base_Model_DbTable_Telefonos',
            $config,
            'abmeditor'
        );
        unset($config);

		
        /**
         * Grilla Emails
         */
        $config->abmWindowTitle = 'Emails';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Emails';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridEmails';

        $this->view->gridEmails = $this->view->radGrid(
            'Base_Model_DbTable_Emails',
            $config,
            'abmeditor'
        );
        unset($config);


        /**
         * ---------------------------------------------------------------------
         * -- CUENTAS BANCARIAS
         * ---------------------------------------------------------------------
         */
		
        /**
         * Grilla Cuentas Bancarias
         */
        $config->abmWindowTitle = 'Cuentas Bancarias';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Cuentas Bancarias';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCtasBancarias';
        $config->iniSection = 'vendedor';
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Base_Model_DbTable_CuentasBancarias',
            'datagateway',
            'vendedor'
        ));

        $this->view->gridCtasBancarias = $this->view->radGrid(
            'Base_Model_DbTable_CuentasBancarias',
            $config,
            'abmeditor'
        );
        unset($config);
        
         /**
         * ---------------------------------------------------------------------
         * -- CUENTAS ZonasPorVendedores
         * ---------------------------------------------------------------------
         */
		
        /**
         * Grilla Zonas Por Vendedores
         */
        $config->abmWindowTitle = 'Zonas Por Vendedores';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 250;
        $config->title = 'Zonas Por Vendedores';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridZonasPorVendedores';
        
   
        $this->view->gridZonasPorVendedores = $this->view->radGrid(
            'Base_Model_DbTable_ZonasPorVendedores',
            $config,
            'abmeditor'
        );
        unset($config);	
		/**
         * ---------------------------------------------------------------------
         * -- Vendedores
         * ---------------------------------------------------------------------
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridTelefonos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridDirecciones';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridEmails';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtasBancarias';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridZonasPorVendedores';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);
				
        $config->abmWindowTitle = 'Vendedores';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        $config->iniSection = 'reducido';

		
		
        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Vendedores',
            $config,
            'abmeditor'
        );
    }

}