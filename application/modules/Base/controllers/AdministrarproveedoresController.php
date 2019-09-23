<?php

/**
 * Base_AdministrarProveedoresController
 *
 * Administrar Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarProveedoresController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarProveedoresController extends Rad_Window_Controller_Action
{

    protected $title = 'Proveedores';

    public function initWindow()
    {
        /**
         * Grilla Generales -> Direcciones
         */
        $config->abmWindowTitle  = 'Direcciones';
        $config->abmWindowWidth  = 650;
        $config->abmWindowHeight = 300;
        $config->title = 'Direcciones';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresDirecciones';
        $this->view->gridProveedoresDirecciones = $this->view->radGrid(
            'Base_Model_DbTable_Direcciones',
            $config,
            'abmeditor',
            'proveedores');
        unset($config);

    

        /**
         * Grilla Generales -> Telefonos
         */
        $config->abmWindowTitle  = 'Telefonos';
        $config->abmWindowWidth  = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Telefonos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresTelefonos';
        $this->view->gridProveedoresTelefonos = $this->view->radGrid(
            'Base_Model_DbTable_Telefonos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Generales -> Email
         */
        $config->abmWindowTitle = 'Emails';
        $config->title = 'Emails';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresEmails';
        $this->view->gridProveedoresEmails = $this->view->radGrid(
            'Base_Model_DbTable_Emails',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Impositivos -> Ingresos Brutos Proveedores
         */
        $config->abmWindowTitle = 'Ingresos Brutos Proveedores';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Ingresos Brutos Proveedores';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresIngresosBrutos';
        $config->fetch = 'ParaProveedores';
        $config->iniSection = 'minimoProveedor';
        $this->view->gridProveedoresIngresosBrutos = $this->view->radGrid(
            'Base_Model_DbTable_ProveedoresIngresosBrutos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Impositivos -> Conceptos Impositivos
         */
        $config->title = 'Cptos. Impositivos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresConceptosImpositivos';
        $config->iniSection = 'minimoProveedor';
        $config->fetch = 'ParaProveedoresSinRetencionesR';
        $this->view->gridProveedoresConceptosImpositivos = $this->view->RadGridManyToMany(
            'Base_Model_DbTable_ConceptosImpositivos',
            'Base_Model_DbTable_ProveedoresConceptosImpositivos',
            'Base_Model_DbTable_Proveedores',
            $config,
            'proveedores'
        );
        unset($config);

         /**
         * Grilla Impositivos -> Valor Conceptos Impositivos
         */
        $config->abmWindowTitle  = 'Valor Cptos. Impositivos';
        $config->abmWindowWidth  = 700;
        $config->abmWindowHeight = 250;
        $config->title = 'Valor Cptos. Impositivos';
        $config->loadAuto = false;
        $config->autoSave = true;
        $config->id = $this->getName() . '_GridProveedoresValoresConceptosImpositivos';
        $config->iniSection = 'ProveedorValor';
        $this->view->gridProveedoresValoresConceptosImpositivos = $this->view->radGrid(
            'Base_Model_DbTable_ProveedoresConceptosImpositivos',
            $config,
            'fasteditor'
        );
        unset($config);

 

 /**
         * Grilla Cuentas Bancarias
         */
        $config->abmWindowTitle = 'Cuentas Bancarias';
        $config->abmWindowWidth = 550;
        $config->abmWindowHeight = 250;
        $config->title = 'Cuentas Bancarias';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasBancarias';
        $config->iniSection = 'proveedor';
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Base_Model_DbTable_CuentasBancarias',
            'datagateway'
        ));
        $this->view->gridProveedoresCuentasBancarias = $this->view->radGrid(
            'Base_Model_DbTable_CuentasBancarias',
            $config,
            'abmeditor'
        );
        unset($config);
                
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Base_Model_DbTable_CuentasBancarias',
            'datagateway'
        ));

        $this->view->gridCtaBan = $this->view->radGrid(
            'Base_Model_DbTable_CuentasBancarias',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Precios -> Modalidades de Pagos
         */
        $config->abmWindowWidth = 600;
        $config->abmWindowHeight = 200;
        $config->title = 'Mod. de Pagos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresModalidadesDePagos';
        $this->view->gridProveedoresModalidadesDePagos = $this->view->radGrid(
            'Base_Model_DbTable_ProveedoresModalidadesDePagos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Precios -> Registros de Precios
         */
        $config->abmWindowTitle = 'Registro de Precios';
        $config->abmWindowWidth = 600;
        $config->abmWindowHeight = 200;
        $config->title = 'Registro de Precios';
        $config->loadAuto = false;
        $config->iniSection = 'listaprecio';
        $config->fetch = 'ComprasyVentas';
        $config->id = $this->getName() . '_GridProveedoresRegistrosDePrecios';
        $this->view->gridProveedoresRegistrosDePrecios = $this->view->radGrid(
            'Base_Model_DbTable_PersonasRegistrosDePrecios',
            $config,
            ''
        );
        unset($config);

        /**
         * Grilla Precios -> Precios informados
         */
        $config->abmWindowTitle = 'Precios Informados';
        $config->abmWindowWidth = 700;
        $config->abmWindowHeight = 300;
        $config->title = 'Precios Informados';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresPreciosInformados';
        //$config->iniSection = 'informados';
        $this->view->gridProveedoresPreciosInformados = $this->view->radGrid(
            'Base_Model_DbTable_PersonasRegistrosDePreciosInformados',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Cuentas Corrientes
         */
        $config->region = 'center';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasCorrientes';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorriente';
        $this->view->gridProveedoresCuentasCorrientes = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config
        );
        unset($config);
        $config->loadAuto = false;
        $config->region = 'south';
        $config->height = 60;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasCorrientesSaldo';
        $this->view->gridProveedoresCuentasCorrientesSaldo = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientes',
            $config,
            'editor'
        );
        unset($config);

        /**
         * Grilla Cuentas Corrientes como Cliente
         */
        $config->region = 'center';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoCliente';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorrienteComoCliente';
        $this->view->gridProveedoresCuentasCorrientesComoCliente = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoClienteSaldo';
        $this->view->gridProveedoresCuentasCorrientesComoClienteSaldo = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesC',
            $config,
            'editor'
        );
        unset($config);

        /**
         * Grilla Cuentas Corrientes como Proveedor
         */
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoProveedor';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorrienteComoProveedor';
        $this->view->gridProveedoresCuentasCorrientesComoProveedor = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoProveedorSaldo';
        $this->view->gridProveedoresCuentasCorrientesComoProveedorSaldo = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesP',
            $config,
            'editor'
        );
        unset($config);

        /**
         * Grilla Proveedores
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridProveedoresDirecciones';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresPaises';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresTelefonos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresEmails';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresIngresosBrutos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresConceptosImpositivos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresValoresConceptosImpositivos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasBancarias';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresModalidadesDePagos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresRegistrosDePrecios';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresPreciosInformados';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasCorrientes';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasCorrientesSaldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoCliente';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoClienteSaldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoProveedor';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresCuentasCorrientesComoProveedorSaldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->abmWindowTitle = 'Proveedores';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        $config->id = $this->getName() . '_GridProveedores';
        $config->iniSection = 'reducido';
        $config->loadAuto = false;
        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Proveedores',
             $config,
             'abmeditor'
        );
    }
}
