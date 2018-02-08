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
         * ----------------------------------------------------------------------------------------------------------------
         * -- GENERALES
         * ----------------------------------------------------------------------------------------------------------------
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

        $this->view->gridDirecciones = $this->view->radGrid('Base_Model_DbTable_Direcciones', $config, 'abmeditor', 'clientes');
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

        $this->view->gridTelefonos = $this->view->radGrid('Base_Model_DbTable_Telefonos', $config, 'abmeditor');
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

        $this->view->gridEmails = $this->view->radGrid('Base_Model_DbTable_Emails', $config, 'abmeditor');
        unset($config);

        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- IMPOSITIVO
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Conceptos Impositivos
         */
        $config = array(
            'title' => 'Conc.Imp.Generales',
            'iniSection' => 'minimoCliente',
            'fetch'    => 'ParaCliente',
            'id' =>  $this->getName() . '_GridConceptosImpositivos'
        );
        $this->view->gridConceptosImpositivos = $this->view->RadGridManyToMany(
            'Base_Model_DbTable_ConceptosImpositivos',
            'Base_Model_DbTable_ClientesConceptosImpositivos',
            'Base_Model_DbTable_Clientes',
            $config,
            'NoEsIVA'
        );
        unset($config);

        /**
         * Grilla Conceptos Impositivos Modificable
         */
        $config->abmWindowTitle = 'Conc.Imp.Propios';
        $config->abmWindowWidth = 700;
        $config->abmWindowHeight = 250;
        $config->title = 'Conc.Imp.Propios';
        $config->loadAuto = false;
        $config->iniSection = 'ClienteValor';
        $config->id = $this->getName() . '_GridConceptosImpositivosE';

        $this->view->gridConceptosImpositivosE = $this->view->radGrid(
            'Base_Model_DbTable_ClientesConceptosImpositivos',
            $config,
            'editor'
        );
        unset($config);

        /**
         * Grilla Declaracion de Actividades
         */
        $config->abmWindowTitle = 'Descripcion de Actividades';
        $config->abmWindowWidth = 600;
        $config->abmWindowHeight = 200;
        $config->title = 'Desc. de Actividades';
        $config->id = $this->getName() . '_GridDeclaracionActividades';
        $config->loadAuto = false;

        $this->view->gridActividades = $this->view->radGrid(
            'Base_Model_DbTable_PersonasActividades',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- CUENTAS BANCARIAS
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Cuentas Bancarias
         */
        
        $config->abmWindowTitle = 'Cuenta Bancaria';
        $config->abmWindowWidth = 550;
        $config->abmWindowHeight = 250;
        $config->title = 'Cuentas Bancarias';
        $config->loadAuto = false;
        $config->iniSection = 'cliente';
        $config->id = $this->getName() . '_GridCuentaBancaria';
        
        $config->abmForm = new Zend_Json_Expr($this->view->radForm(
            'Base_Model_DbTable_CuentasBancarias',
            'datagateway'
        ));

        $this->view->gridCuentasBancarias = $this->view->radGrid(
            'Base_Model_DbTable_CuentasBancarias',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- CUENTA CORRIENTE
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Cuentas Corrientes
         */
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCtaCte';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorriente';

        $this->view->gridCtaCte = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);

        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridCtaCte_Saldo';

        $this->view->gridCtaCte_Saldo = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientes',
            $config,
            'editor'
        );
        unset($config);

        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- CUENTA CORRIENTE COMO CLIENTE
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Cuentas Corrientes
         */
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCtaCteC';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorrienteComoCliente';

        $this->view->gridCtaCteC = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);

        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridCtaCte_SaldoC';

        $this->view->gridCtaCte_SaldoC = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesC',
            $config,
            'editor'
        );
        unset($config);

        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- CUENTA CORRIENTE COMO PROVEEDOR
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Cuentas Corrientes
         */
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCtaCteP';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorrienteComoProveedor';

        $this->view->gridCtaCteP = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config,
            null
        );
        unset($config);

        $config->loadAuto = false;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridCtaCte_SaldoP';

        $this->view->gridCtaCte_SaldoP = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesP',
            $config,
            'editor'
        );
        unset($config);


        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- ZONAS DE VENTAS
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Zonas de Venta
         */
        $configHija4->abmWindowTitle = 'Clientes por Zona';
        $configHija4->abmWindowWidth = 550;
        $configHija4->abmWindowHeight = 230;
        $configHija4->title = 'Zonas de Ventas';
        $configHija4->loadAuto = false;
        $configHija4->id = $this->getName() . '_GridZonasDeVentas';

        $this->view->gridZonasDeVentas = $this->view->radGrid(
            'Base_Model_DbTable_ZonasPorPersonas',
            $configHija4,
            'abmeditor'
        );
        unset($config);

        /**
         * ----------------------------------------------------------------------------------------------------------------
         * -- MODALIDADES DE PAGO
         * ----------------------------------------------------------------------------------------------------------------
         */
        /**
         * Grilla Modalidades de Pago
         */
        $config->abmWindowTitle = 'Modalidades de Pago';
        $config->abmWindowWidth = 550;
        $config->abmWindowHeight = 120;
        $config->title = 'Modalidades de Pago';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridModalidadesDePago';

        $this->view->gridModalidadesDePago = $this->view->radGrid(
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

        $dg->id = $this->getName() . '_GridDeclaracionActividades';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCuentaBancaria';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridZonasDeVentas';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridConceptosImpositivos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridConceptosImpositivosE';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCte';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteC';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteP';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);        

        $dg->id = $this->getName() . '_GridCtaCte_Saldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCte_SaldoC';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCte_SaldoP';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridModalidadesDePago';
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