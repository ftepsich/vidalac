<?php

/**
 * Base_AdministrarEmpleadosController
 *
 * Administrador de Empleados
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarEmpleadosController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarEmpleadosController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Empleados';

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
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 200;
        $config->title = 'Direcciones';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridDirecciones';

        $this->view->gridDirecciones = $this->view->radGrid(
            'Base_Model_DbTable_Direcciones',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla de Telefonos
         */
        $config->abmWindowTitle = 'Telefonos';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 350;
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
         * -- Titulos
         * ---------------------------------------------------------------------
         */
        /**
         * Grilla Titulos
         */
        $config->abmWindowTitle = 'Titulos';
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 120;
        $config->title = 'Titulos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridTitulos';
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Rrhh_Model_DbTable_PersonasTitulos',
            'datagateway',
            ''
        ));

        $this->view->gridTitulos = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasTitulos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * ---------------------------------------------------------------------
         * -- Familiares
         * ---------------------------------------------------------------------
         */
        /**
         * Grilla Familiares
         */
        $config->abmWindowTitle = 'Familiares';
        $config->abmWindowWidth = 450;
        $config->abmWindowHeight = 120;
        $config->title = 'Familiares';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridFamiliares';
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Rrhh_Model_DbTable_FamiliaresPersonas',
            'datagateway',
            ''
        ));

        $this->view->gridFamiliares = $this->view->radGrid(
            'Rrhh_Model_DbTable_FamiliaresPersonas',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * ---------------------------------------------------------------------
         * -- Afiliaciones del agente
         * ---------------------------------------------------------------------
         */

        /**
         * Grilla de adherentes a afiliaciones del agente
         */
        $config->abmWindowTitle = 'Adherentes';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 350;
        $config->title = 'Adherentes';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridAfiliacionesAdherentes';

        $this->view->gridAfiliacionesAdherentes = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasAfiliacionesAdherentes',
            $config,
            'abmeditor'
        );
        unset($config);


        $detailGridSL = array();

        $dg->id             = $this->getName() . '_GridAfiliacionesAdherentes';
        $dg->remotefield    = 'PersonaAfiliacion';
        $dg->localfield     = 'Id';
        $detailGridSL[]      = $dg;
        unset($dg);

        /**
         * Grilla Afiliaciones del agente
         */
        $config->abmWindowTitle = 'Afiliaciones';
        $config->abmWindowWidth = 650;
        $config->abmWindowHeight = 200;
        $config->detailGrid = $detailGridSL;
        $config->title = 'Afiliaciones';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridAfiliaciones';

        $this->view->gridAfiliaciones = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasAfiliaciones',
            $config,
            'abmeditor'
        );
        unset($config);
        unset($detailGridSL);

        /**
         * ---------------------------------------------------------------------
         * -- Empleados
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

        $dg->id = $this->getName() . '_GridTitulos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridFamiliares';
        $dg->remotefield = 'PersonaEmpleado';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridAfiliaciones';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->abmWindowTitle = 'Empleados';
        $config->abmWindowWidth = 720;
        $config->abmWindowHeight = 530;
        $config->detailGrid = $detailGrids;
        $config->iniSection = 'reducido';
        $config->loadAuto   = false;

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Empleados',
            $config,
            'abmeditor'
        );

        $this->view->caracteristicasEmpleados = json_encode(Model_DbTable_CaracteristicasModelos::getCaracteristicas('Base_Model_DbTable_Empleados'));
        $this->view->caracteristicasTitulos   = json_encode(Model_DbTable_CaracteristicasModelos::getCaracteristicas('Rrhh_Model_DbTable_PersonasTitulos'));
    }

}