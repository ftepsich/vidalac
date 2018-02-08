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
         * Grilla de telefonos
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
         * Grilla de Actividades afip
         */
        $config->abmWindowTitle = 'Proveedores Actividades';
        $config->abmWindowWidth = 760;
        $config->abmWindowHeight = 400;
        $config->title = 'Proveedores Actividades';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresActividades';

        $this->view->gridActiv = $this->view->radGrid(
            'Base_Model_DbTable_PersonasActividades',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla de Cuentas bancarias
         */
        $config->abmWindowTitle = 'Cuentas Bancarias';
        $config->abmWindowWidth = 550;
        $config->abmWindowHeight = 250;
        $config->title = 'Ctas. Bancarias';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridPCB';
        $config->iniSection = 'proveedor';
        
                
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
         * Grilla de Modalidades de Pagos
         */
        $config->abmWindowWidth = 600;
        $config->abmWindowHeight = 200;
        $config->title = 'Mod. de Pagos';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridPMP';

        $this->view->gridModPag = $this->view->radGrid(
            'Base_Model_DbTable_ProveedoresModalidadesDePagos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla de Registros de Precios
         */
        $config->abmWindowTitle = 'Registro de Precios';
        $config->abmWindowWidth = 600;
        $config->abmWindowHeight = 200;
        $config->title = 'Registro de Precios';
        $config->loadAuto = false;
        $config->iniSection = 'listaprecio';
        $config->fetch = 'ComprasyVentas';
        $config->id = $this->getName() . '_GridLDP';

        $this->view->gridListaPrecios = $this->view->radGrid(
            'Base_Model_DbTable_PersonasRegistrosDePrecios',
            $config,
            ''
        );
        unset($config);

        /**
         * Grilla de Listas de Precios informados
         */
        $config->abmWindowTitle = 'Precios Informados';
        $config->abmWindowWidth = 700;
        $config->abmWindowHeight = 300;
        $config->title = 'Precios Informados';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridLPInformados';
        //$config->iniSection = 'informados';

        $this->view->gridListaPreciosInf = $this->view->radGrid(
            'Base_Model_DbTable_PersonasRegistrosDePreciosInformados',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla de conceptos impositivos
         */
        $config = array(
            'title'    => 'Conc. Impositivos',
            'iniSection' => 'minimoProveedor',
            'fetch'    => 'ParaProveedores',
            'id'       => $this->getName() . '_GridPCIProveedores'
        );
        $this->view->gridCI = $this->view->RadGridManyToMany(
            'Base_Model_DbTable_ConceptosImpositivos',
            'Base_Model_DbTable_ProveedoresConceptosImpositivos',
            'Base_Model_DbTable_Proveedores',
            $config,
            'proveedores'
        );
        unset($config);

        /**
         * Grilla Cuentas Corrientes
         */
        $config->region = 'center';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCtaCteProveedores';
        $config->iniSection = 'conbarra';
        $config->fetch = 'CuentaCorriente';

        $this->view->gridCtaCte = $this->view->radGrid(
            'Contable_Model_DbTable_CuentasCorrientes',
            $config
        );
        unset($config);

        $config->loadAuto = false;
        $config->region = 'south';
        $config->height = 60;
        $config->withPaginator = false;
        $config->id = $this->getName() . '_GridCtaCteProveedores_Saldo';

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
        $config->region = 'center';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridCtaCteProveedoresC';
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
        $config->id = $this->getName() . '_GridCtaCteProveedores_SaldoC';

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
        $config->id = $this->getName() . '_GridCtaCteProveedoresP';
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
        $config->id = $this->getName() . '_GridCtaCteProveedores_SaldoP';

        $this->view->gridCtaCte_SaldoP = $this->view->radGrid(
            'Contable_Model_DbTable_VSaldoCuentasCorrientesP',
            $config,
            'editor'
        );
        unset($config);


        /**
         * Grillas Conceptos Impositovos del proveedor
         */
        $config->abmWindowTitle = 'Valor Conc.Imp.';
        $config->abmWindowWidth = 700;
        $config->abmWindowHeight = 250;
        $config->title = 'Valor Conc.Imp.';
        $config->loadAuto = false;
        $config->autoSave = true;
        $config->id = $this->getName() . '_GridPCICProveedoresValor';
        $config->iniSection = 'ProveedorValor';

        $this->view->gridCIP = $this->view->radGrid(
            'Base_Model_DbTable_ProveedoresConceptosImpositivos',
            $config,
            'fasteditor'
        );
        unset($config);

        /**
         * Grilla email
         */
        $config->abmWindowTitle = 'Emails';
        $config->title = 'Emails';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridProveedoresEmails';

        $this->view->gridEmail = $this->view->radGrid(
            'Base_Model_DbTable_Emails',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Direcciones
         */
        $config->abmWindowTitle = 'Direcciones';
        $config->abmWindowWidth = 650;
        $config->abmWindowHeight = 300;
        $config->title = 'Direcciones';
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridDirecciones';

        $this->view->gridDirecciones = $this->view->radGrid('Base_Model_DbTable_Direcciones',$config,'abmeditor','proveedores');
        unset($config);
        
        /**
         * Grilla Proveedores
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridTelefonos';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresActividades';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridPCB';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridPMP';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridLDP';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridLPInformados';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridPCIProveedores';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridPCICProveedoresValor';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteProveedores';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteProveedoresC';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteProveedoresP';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteProveedores_Saldo';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteProveedores_SaldoC';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridCtaCteProveedores_SaldoP';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridDirecciones';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridProveedoresEmails';
        $dg->remotefield = 'Persona';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $config->iniSection = 'reducido';
        $config->abmWindowTitle = 'Proveedores';
        $config->abmWindowWidth = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        $config->id = $this->getName() . '_GridPGrillaPadre';
        $config->iniSection = 'reducido';
        $config->loadAuto = false;

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_Proveedores',
             $config,
             'abmeditor'
        );
    }
}
