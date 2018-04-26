<?php

/**
 * Base_AdministrarClientesController
 *
 * Administrar Clientes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarClientesController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarClientesController extends Rad_Window_Controller_Action
{
    protected $title = 'Clientes';

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
        $config->id = $this->getName() . '_GridClientesDirecciones';
        $this->view->gridClientesDirecciones = $this->view->radGrid(
            'Base_Model_DbTable_Direcciones',
            $config,
            'abmeditor',
            'clientes');
        unset($config);

        /**
         * Grilla Generales -> Telefonos
         */
        $config->abmWindowTitle  = 'Telefonos';
        $config->abmWindowWidth  = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Telefonos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridClientesTelefonos';
        $this->view->gridClientesTelefonos = $this->view->radGrid(
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
        $config->id = $this->getName() . '_GridClientesEmails';
        $this->view->gridClientesEmails = $this->view->radGrid(
            'Base_Model_DbTable_Emails',
            $config,
            'abmeditor'
        );
        unset($config);

         /**
          * Grilla Impositivo -> Ingresos Brutos Clientes
         */
        $config->abmWindowTitle = 'Ingresos Brutos Clientes';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Ingresos Brutos Clientes';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridClientesIngresosBrutos';
        $config->fetch = 'ParaClientes';
        $config->iniSection = 'minimoCliente';

        $this->view->gridClientesIngresosBrutos = $this->view->radGrid(
            'Base_Model_DbTable_ClientesIngresosBrutos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Impositivos -> Conceptos Impositivos
         */
        $config->title = 'Cptos. Impositivos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridClientesConceptosImpositivos';
        $config->iniSection = 'minimoCliente';
        $config->fetch = 'ParaClientesSinPercepcionesR';
        $this->view->gridClientesConceptosImpositivos = $this->view->RadGridManyToMany(
            'Base_Model_DbTable_ConceptosImpositivos',
            'Base_Model_DbTable_ClientesConceptosImpositivos',
            'Base_Model_DbTable_Clientes',
            $config,
            'clientes'
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
        $config->id = $this->getName() . '_GridClientesValoresConceptosImpositivos';
        $config->iniSection = 'ClienteValor';
        $this->view->gridClientesValoresConceptosImpositivos = $this->view->radGrid(
            'Base_Model_DbTable_ClientesConceptosImpositivos',
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
        $config->id = $this->getName() . '_GridClientesCuentasBancarias';
        $config->iniSection = 'cliente';
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Base_Model_DbTable_CuentasBancarias',
            'datagateway'
        ));
        $this->view->gridClientesCuentasBancarias = $this->view->radGrid(
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
         * Grilla Cuentas Corrientes
         */
        $config->region = 'center';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridClientesCuentasCorrientes';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorriente';
        $this->view->gridClientesCuentasCorrientes = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config
        );
        unset($config);
        $config->loadAuto = false;
        $config->region = 'south';
        $config->height = 60;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridClientesCuentasCorrientesSaldo';
        $this->view->gridClientesCuentasCorrientesSaldo = $this->view->radGrid(
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
        $config->id = $this->getName() . '_GridClientesCuentasCorrientesComoCliente';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorrienteComoCliente';
        $this->view->gridClientesCuentasCorrientesComoCliente = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridClientesCuentasCorrientesComoClienteSaldo';
        $this->view->gridClientesCuentasCorrientesComoClienteSaldo = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesC',
            $config,
            'editor'
        );
        unset($config);
 
       /**
         * Grilla Cuentas Corrientes como Proveedor
         */
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridClientesCuentasCorrientesComoProveedor';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorrienteComoProveedor';
        $this->view->gridClientesCuentasCorrientesComoProveedor = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);
        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridClientesCuentasCorrientesComoProveedorSaldo';
        $this->view->gridClientesCuentasCorrientesComoProveedorSaldo = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesP',
            $config,
            'editor'
        );
        unset($config);
    

        /**
         * Grilla Zona de Ventas
        */
        $configHija4->abmWindowTitle = 'Clientes por Zona';
        $configHija4->abmWindowWidth = 550;
        $configHija4->abmWindowHeight = 230;
        $configHija4->title = 'Zonas de Ventas';
        $configHija4->loadAuto = false;
        $configHija4->id = $this->getName() . '_GridZonasDeVentasClientes';
         $this->view->gridZonasDeVentasClientes = $this->view->radGrid('Base_Model_DbTable_ZonasPorPersonas',$configHija4,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Modalidades de Pago
         */
        $config->abmWindowTitle = 'Modalidades de Pago';
        $config->abmWindowWidth = 550;
        $config->abmWindowHeight = 120;
        $config->title = 'Modalidades de Pago';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridModalidadesDePagoClientes';

        $this->view->gridModalidadesDePagoClientes = $this->view->radGrid(
            'Base_Model_DbTable_ClientesModalidadesDePagos',
            $config,
            'abmeditor'
        );
        unset($config);
        
        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- CLIENTES
         * ----------------------------------------------------------------------------------------------------------------
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridClientesTelefonos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesDirecciones';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesEmails';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesIngresosBrutos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesConceptosImpositivos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesValoresConceptosImpositivos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesCuentasBancarias';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesCuentasCorrientes';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesCuentasCorrientesComoCliente';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesCuentasCorrientesComoProveedor';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);        

        $dg->id = $this->getName() . '_GridClientesCuentasCorrientesSaldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesCuentasCorrientesComoClienteSaldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridClientesCuentasCorrientesComoProveedorSaldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridZonasDeVentasClientes';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridModalidadesDePagoClientes';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->abmWindowTitle = 'Clientes';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        $config->iniSection = 'reducido';
        $config->loadAuto = false;

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Clientes',
            $config,
            'abmeditor'
        );
    }

}
