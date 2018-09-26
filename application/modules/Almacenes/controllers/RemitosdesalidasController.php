<?php

/**
 * Controlador de Remitos de Ingreso
 */
class Almacenes_RemitosDeSalidasController extends Rad_Window_Controller_Action
{

    protected $title = 'Remitos de Salidas';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow ()
    {
        /**
         * Formulario Principal Remitos de Ingreso (Paso 1)
         */
        $this->view->form = $this->view->radForm(
            'Almacenes_Model_DbTable_RemitosDeSalidas',
            'datagateway',
            'wizard'
        );

        /**
         * Ordenes de Compras Remitos (Paso 2 - Padre)
         */
        $detailGrid->id             = $this->getName() . '_OrdenesDePedidosArticulos';
        $detailGrid->remotefield    = 'Comprobante';
        $detailGrid->localfield     = 'Id';

        $this->view->gridOrdenesDePedido = $this->view->RadGridManyToMany(
            'Facturacion_Model_DbTable_OrdenesDePedidos',
            'Facturacion_Model_DbTable_OrdenesDePedidosRemitos',
            'Almacenes_Almacenes_Model_DbTable_Remitos',
            array(
                'title'         => 'Orden de Pedido',
                'withPaginator' => false,
                'withToolbar'   => false,
                'fetch'         => 'AsociadosYFaltantesDeEntregar',
                'loadAuto'      => false,
                'iniSection'    => 'reducido',
                'detailGrid'    => $detailGrid,
                'id'            => 'OrdenesDePedidosRelacionadas'
            )
        );
        unset($detailGrid);

        /**
         * Ordenes de Compras Articulos (Paso 2 - Hija)
         */
        $config->loadAuto       = false;
        $config->title          = null;
        $config->withPaginator  = false;
        $config->id             = $this->getName() . '_OrdenesDePedidosArticulos';

        $this->view->gridOrdenesDePedidoArticulos = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDePedidosArticulos',
            $config,
            null,
            'reducido'
        );
        unset($config);
        /**
         * Articulos del Remito (Paso 3)
         */
        $config->abmWindowTitle     = 'Artículo';
        $config->abmWindowWidth     = 650;
        $config->abmWindowHeight    = 200;
        $config->withPaginator      = false;
        $config->title              = 'Artículo';
        $config->loadAuto           = false;
        $config->autoSave           = true;

        $this->view->gridRemitosArticulos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosArticulosDeSalidas',
            $config,
            'abmeditor',
            'wizard'
        );
        unset($config);

        /**
         * Grilla Remitos de Ingresos
         */
        $this->view->grid = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosDeSalidas',
            array('abmForm' => '',loadAuto => false), // Evitamos que radGrid cree automaticamente el formulario al no tenerlo
            'abmeditor'
        );
    }

    public function cerrarremitoAction ()
    {
        //ini_set("display_errors",1);
        $this->_helper->viewRenderer->setNoRender(true);

        $request    = $this->getRequest();
        $idRemito   = $request->getParam('id');

        $db         = Zend_Registry::get('db');
        $idRemito   = $db->quote($idRemito, 'INTEGER');

        try {
            $M_RS = new Almacenes_Model_DbTable_RemitosDeSalidas(array(), false);
            $M_RS->cerrar($idRemito);

            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

}