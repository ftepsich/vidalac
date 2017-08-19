<?php

/**
 * Facturacion_RecibosController
 *
 * Controlador de Recibos
 *
 * @package Aplicacion
 * @subpackage Facturacion
 * @class Facturacion_FacturasVentasController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Facturacion_RecibosController extends Rad_Window_Controller_Action
{

    protected $title = "Recibos";

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow() {
        /**
         * Grilla principal
         */
        $parametrosAdc->abmWindowTitle = 'Carga de Recibos';
        $parametrosAdc->abmWindowWidth = 900;
        $parametrosAdc->loadAuto       = false;
        $parametrosAdc->abmWindowHeight = 500;
        $parametrosAdc->abmForm = null; // Hacemos q no cree automaticamente el formulario

        $this->view->grid = $this->view->radGrid(
            "Facturacion_Model_DbTable_Recibos",
            $parametrosAdc,
            'abmeditor'
        );

        /**
         * Formulario orden de pago (Paso 1)
         */
        $abmForm = new Zend_Json_Expr($this->view->radForm(
            'Facturacion_Model_DbTable_Recibos', // Nombre del Modelo
            'datagateway',
            'wizard'
        ));
        $this->view->form = $abmForm;

        /**
         * Grilla Asignar facturas a Recibos (Paso 2)
         */

        $grillaAFV = $this->view->RadGridManyToMany(
            "Facturacion_Model_DbTable_Facturas",
            "Facturacion_Model_DbTable_RecibosFacturas",
            "Facturacion_Model_DbTable_Recibos",
            array(
                'xtype' => 'radformmanytomanyeditorgridpanel',
                'withPaginator' => false,
                'buildToolbar' => new Zend_Json_Expr('function(){}'), // saco la toolbar para no tener el boton
                'fetch' => 'AsociadosYFaltantesDeCobrar',
                'title' => 'Comprobantes'
            ),
            'wizardR'
        );

        $this->view->gridAFV = $grillaAFV;

        /**
         * Grilla Conceptos Impositivos (Paso 3)
         */
        $config->abmWindowTitle = 'Concepto Impositivo';
        //$config->id = 'IdGrillaOdePC_lsakdfioerua';
        $config->abmWindowWidth = 550;
        $config->abmWindowHeight = 180;
        $config->withPaginator = false;
        $config->title = 'Conceptos Impositivos';
        $config->loadAuto = false;
        $config->iniSection = 'wizard';
        $config->autoSave = true;

        $grillaRC = $this->view->radGrid(
            'Facturacion_Model_DbTable_RecibosConceptos',
            $config,
            'abmeditor',
            'wizard'
        );

        $this->view->gridCI = $grillaRC;
        unset($config);

        /**
         *  Recibos Detalles (cobros)
         */
        $config->withPaginator = false;
        $config->loadAuto = false;
        $config->ddGroup = 'cobros';
        $config->ddText = '{0} Cobro(s) seleccionado(s)';
        $config->height = 300;
        $config->layout = 'fit';
        $config->autoSave = true;

        $grillaRD = $this->view->radGrid(
            'Facturacion_Model_DbTable_RecibosDetalles',
            $config,
            ''
        );
        $this->view->gridRD = $grillaRD;

    }

    /**
     * Agrega cobros con cheques
     */
    public function agregarchequeAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');
        $idDeCheques = $request->getParam('ids');

        try {
            $cobros = new Facturacion_Model_DbTable_RecibosDetalles(array(), false);
            $respuesta = $cobros->insertPagosCheques($idRecibo, $idDeCheques);
            $respuesta = json_encode($respuesta);
            echo "{success: true, cobros: $respuesta}";
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    /**
     * Agrega cobros con Notas de Credito
     */
    public function agregarnotaAction() {

        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');
        $idDeNotas = $request->getParam('ids');

        try {
            $cobros = new Facturacion_Model_DbTable_RecibosDetalles(array(), false);
            $respuesta = $cobros->insertPagosNotas($idRecibo, $idDeNotas);
            $respuesta = json_encode($respuesta);
            echo "{success: true, cobros: $respuesta}";
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    /**
     * Agrega cobros efectivo
     */
    public function agregarefectivoAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');
        $monto = $request->getParam('monto');
        $caja  = $request->getParam('caja');
        //$divisa      = $request->getParam('divisa');

        try {
            $cobros = new Facturacion_Model_DbTable_RecibosDetalles(array(), false);
            $respuesta = json_encode($cobros->insertPagoEfectivo($idRecibo, $monto, $caja));
            echo "{success: true, cobros: $respuesta}";
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    /**
     * Agrega cobros con transferencia bancaria
     */
    public function agregartransaccionAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');
        $idTransacciones = $request->getParam('ids');

        try {
            $cobros = new Facturacion_Model_DbTable_RecibosDetalles(array(), false);
            $respuesta = $cobros->insertPagosTransacciones($idRecibo, $idTransacciones);
            $respuesta = json_encode($respuesta);
            echo "{success: true, cobros: $respuesta}";
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------------------

    public function paso2Action()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');
        if (!$idRecibo) {
            echo "{success: false, msg: 'Falta el parametro id recibo'}";
            return;
        }
        try {
            //controlo que el monto a cobrar sea mayor a 0

            // ahora pueden venir valores negativos
            // $this->controlarMontoAPagar($idOrdenDePago);
            $db = Zend_Registry::get('db');

            $Comp_qSuma     = $db->fetchOne("SELECT fCompPago_Monto_qSuma($idRecibo)");

            if ($Comp_qSuma < 0.01) {
                // Error no puede darse que nada sume
                throw new Rad_Db_Table_Exception("No se ha asignado ningun comprobante a pagar o ninguno de los comprobantes asignados suma.");
            } else {
                $Comp_qResta    = 0;
                $Comp_qResta    = $db->fetchOne("SELECT fCompPago_Monto_qResta($idRecibo)");

                if ($Comp_qSuma > $Comp_qResta){
                    // Debo calcular el impuesto
                    $M_R = new Facturacion_Model_DbTable_Recibos(array(), false);
                    $M_R->insertarConceptosDesdeControlador($idRecibo);
                }
            }
            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }
    // ---------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     *  No se usa mas (contiene una vista que se elimino)
     *  Controla que no permita seleccionar facturas quedando el monto a pagar en negativo
     *
     *  @param $idRecibo    Id del comprobante
     *
     */
    protected function controlarMontoACobrar($idRecibo)
    {   $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);
        // busco los comprobantes hijos del comprobante padre.
        $R_CR = $M_CR->fetchAll("ComprobantePadre = $idRecibo");

        $suma = 0;
        $resta = 0;
        //si tiene comprobante hijos los recorro uno a uno y calculo su monto
        if(!empty($R_CR)){
            foreach ($R_CR as $row) {
                $sql = "SELECT v.MontoTotal, v.TipoDeComprobante FROM
                        vComprobanteTotalPagado v where Id = ".$row->ComprobanteHijo;

                $db = $M_CR->getAdapter();
                $R_CRHMontos = $db->fetchRow($sql);

                //acumulo el monto del comprobante hijo en caso q sumo o reste
                if(in_array($R_CRHMontos["TipoDeComprobante"], array(19,20,21,22,23,29,30,31,32,41,42,43,44))){
                    $resta += $R_CRHMontos["MontoTotal"];
                } else {
                    $suma += $R_CRHMontos["MontoTotal"];
                }
            }
            //pregunto que si lo q suma es menor o igual a lo q resta tiro una excepcion
            if($suma < $resta){
                throw new Rad_Db_Table_Exception("El monto a cobrar debe ser mayor o igual a 0 (cero).");
            }
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------------------
    public function agregarfacturaAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');
        $idDeFacturas = $request->getParam('ids');

        try {
            $cobros = new Facturacion_Model_DbTable_RecibosDetalles(array(), false);
            $respuesta = $cobros->insertPagosFacturas($idRecibo, $idDeFacturas);
            $respuesta = json_encode($respuesta);
            echo "{success: true, cobros: $respuesta}";
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------------------

    public function getmontototalAction() {

        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');

        try {
            $ordenes = new Facturacion_Model_DbTable_Recibos(array(), false);
            $orden = $ordenes->find($idRecibo)->current();
            if (!$orden) {
                throw new Rad_Exception('No se encontro el Recibo');
            }
            echo "{success: true, monto: '$orden->MontoTotal'}";
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

// es lo mismo que el paso2Action() y no se sabe si se usa en algun lado
/*
    public function insertarconceptosasociadosAction() {

        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');

        try {
            $M_RC = new Facturacion_Model_DbTable_RecibosConceptos(array(), false);
            $M_RC->insertarConceptosDesdeControlador($idRecibo);
            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }
*/
    public function cobrarrecibosAction() {
        //ini_set("display_errors",1);
        $this->_helper->viewRenderer->setNoRender(true);
        $M_Recibos = new Facturacion_Model_DbTable_Recibos(array(), false);
        $request = $this->getRequest();
        $idRecibo = $request->getParam('idRecibo');

        try {

            $M_Recibos->cerrar($idRecibo);

            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------------------
}

