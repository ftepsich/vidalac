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
class Base_AdministrarEmpleadosLaboralesController extends Rad_Window_Controller_Action
{
    protected $title = 'Datos Laborales Empleados';

    public function initWindow()
    {
        /**
         * Grilla de Horas Extras del servicio del agente
         */
        $config->abmWindowTitle  = 'Horas Extras';
        $config->abmWindowWidth  = 760;
        $config->abmWindowHeight = 350;
        $config->title           = 'Horas Extras';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridServiciosHorasExtras';

        $this->view->gridHsExtras = $this->view->radGrid(
            'Rrhh_Model_DbTable_ServiciosHorasExtras',
            $config,
            'abmeditor'
        );
        unset($config);

        $detailGridSL = array();

        $dg->id          = $this->getName() . '_GridServiciosHorasExtras';
        $dg->remotefield = 'Servicio';
        $dg->localfield  = 'Id';
        $detailGridSL[]  = $dg;
        unset($dg);

        /**
         * Grilla de Horas Trabajadas del servicio del agente
         */
        $config->abmWindowTitle  = 'Horas Trabajadas';
        $config->abmWindowWidth  = 760;
        $config->abmWindowHeight = 350;
        $config->title           = 'Horas Trabajadas';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridServiciosHorasTrabajadas';

        $this->view->gridHsTrabajadas = $this->view->radGrid(
            'Rrhh_Model_DbTable_ServiciosHorasTrabajadas',
            $config,
            'abmeditor'
        );
        unset($config);

        $dg->id          = $this->getName() . '_GridServiciosHorasTrabajadas';
        $dg->remotefield = 'Servicio';
        $dg->localfield  = 'Id';
        $detailGridSL[]  = $dg;
        unset($dg);

        /**
         * Grilla de Feriados del Servicios del agente
         */

        $config = array(
            'title' => 'Feriados Trabajados',
            'iniSection' => 'default',
            'id' =>  $this->getName() . '_GridFeriadosTrabajados'
        );
        $this->view->gridFeriadosTrabajados = $this->view->RadGridManyToMany(
            'Rrhh_Model_DbTable_Feriados',
            'Rrhh_Model_DbTable_ServiciosFeriados',
            'Rrhh_Model_DbTable_Servicios',
            $config,
            'default'
        );
        unset($config);

        $dg->id          = $this->getName() . '_GridFeriadosTrabajados';
        $dg->remotefield = 'Servicio';
        $dg->localfield  = 'Id';
        $detailGridSL[]  = $dg;
        unset($dg);

        /**
         * Grilla de Servicios en licencias del agente
         */
        $config->abmWindowTitle  = 'Situacion de revista del agente';
        $config->abmWindowWidth  = 760;
        $config->abmWindowHeight = 350;
        $config->title           = 'Situacion de revista del agente';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridServiciosSituacionesDeRevistas';

        $this->view->gridServiciosSituacionesDeRevistas = $this->view->radGrid(
            'Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas',
            $config,
            'abmeditor'
        );
        unset($config);

        $dg->id          = $this->getName() . '_GridServiciosSituacionesDeRevistas';
        $dg->remotefield = 'Servicio';
        $dg->localfield  = 'Id';
        $detailGridSL[]  = $dg;
        unset($dg);

        /**
         * Grilla Servicios del agente
         */
        $config->abmWindowTitle  = 'Servicios del agente';
        $config->abmWindowWidth  = 650;
        $config->abmWindowHeight = 200;
        $config->detailGrid      = $detailGridSL;
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridServicios';

        $this->view->gridServicios = $this->view->radGrid(
            'Rrhh_Model_DbTable_Servicios',
            $config,
            'abmeditor'
        );
        unset($config);
        unset($detailGridSL);

        /**
         * Grilla Cuentas Bancarias
         */
        $config->abmWindowTitle  = 'Cuentas Bancarias';
        $config->abmWindowWidth  = 450;
        $config->abmWindowHeight = 320;
        $config->title           = 'Cuentas Bancarias';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridCtasBancarias';
        $config->iniSection      = 'empleado';
        $config->abmForm         = new Zend_Json_Expr(
            $this->view->radForm(
                'Base_Model_DbTable_CuentasBancarias',
                'datagateway',
                'empleado'
            )
        );

        $this->view->gridCtasBancarias = $this->view->radGrid(
            'Base_Model_DbTable_CuentasBancarias',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Areas de Trabajo
         */
        $config = array(
            'title'      => 'Areas De Trabajos',
            'iniSection' => 'default',
            'id'         =>  $this->getName() . '_GridAreasDeTrabajos'
        );
        $this->view->gridAreasDeTrabajos = $this->view->RadGridManyToMany(
            'Base_Model_DbTable_AreasDeTrabajos',
            'Base_Model_DbTable_AreasDeTrabajosPersonas',
            'Base_Model_DbTable_Empleados',
            $config,
            'default'
        );
        unset($config);

        /**
         * Grilla Ganancias
         */
        $config->abmWindowTitle  = 'Ganancias';
        $config->abmWindowWidth  = 450;
        $config->abmWindowHeight = 120;
        $config->title           = 'Ganancias';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridGanancias';
        $config->abmForm         = new Zend_Json_Expr(
            $this->view->radForm(
                'Rrhh_Model_DbTable_PersonasGananciasDeducciones',
                'datagateway',
                ''
            )
        );

        $this->view->gridGanancias = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasGananciasDeducciones',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Zonas Afip
         */
        $config->abmWindowTitle  = 'Zonas';
        $config->abmWindowWidth  = 450;
        $config->abmWindowHeight = 320;
        $config->title           = 'Zonas';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridZonas';
        $config->abmForm         = new Zend_Json_Expr(
            $this->view->radForm(
                'Liquidacion_Model_DbTable_PersonasZonas',
                'datagateway',
                ''
            )
        );

        $this->view->gridZonas = $this->view->radGrid(
            'Liquidacion_Model_DbTable_PersonasZonas',
            $config,
            'abmeditor'
        );
        unset($config);


        /**
         * ---------------------------------------------------------------------
         * -- Empleados
         * ---------------------------------------------------------------------
         */
        $detailGrids = array();

        $dg->id          = $this->getName() . '_GridServicios';
        $dg->remotefield = 'Persona';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id          = $this->getName() . '_GridGanancias';
        $dg->remotefield = 'Persona';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id          = $this->getName() . '_GridCtasBancarias';
        $dg->remotefield = 'Persona';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id          = $this->getName() . '_GridAreasDeTrabajos';
        $dg->remotefield = 'Persona';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id          = $this->getName() . '_GridZonas';
        $dg->remotefield = 'Persona';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);        

        $config->abmWindowTitle  = 'Empleados';
        $config->abmWindowWidth  = 720;
        $config->abmWindowHeight = 530;
        $config->detailGrid      = $detailGrids;
        $config->iniSection      = 'reducido';
        $config->loadAuto        = false;

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Empleados',
            $config,
            'abmeditor'
        );

        $this->view->caracteristicasServicios = json_encode(Model_DbTable_CaracteristicasModelos::getCaracteristicas('Rrhh_Model_DbTable_Servicios'));
    }

}