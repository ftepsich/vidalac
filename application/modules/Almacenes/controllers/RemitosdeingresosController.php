<?php

/**
 * 	Controlador de Remitos de Ingreso
 */
class Almacenes_RemitosDeIngresosController extends Rad_Window_Controller_Action
{

    protected $title = 'Remitos de Ingresos';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow()
    {
        /**
         * Formulario Principal Remitos de Ingreso (Paso 1)
         */
        $this->view->form = $this->view->radForm(
            'Almacenes_Model_DbTable_RemitosDeIngresos',
            'datagateway',
            'wizard'
        );

        /**
         * Ordenes de Compras Remitos (Paso 2 - Padre)
         */
        $detailGrid->id          = $this->getName() . '_OrdenesDeComprasArticulos';
        $detailGrid->remotefield = 'Comprobante';
        $detailGrid->localfield  = 'Id';

        $this->view->gridOrdenesDeCompra = $this->view->RadGridManyToMany(
            'Facturacion_Model_DbTable_OrdenesDeCompras',
            'Facturacion_Model_DbTable_OrdenesDeComprasRemitos',
            'Almacenes_Almacenes_Model_DbTable_Remitos',
            array (
                'title'         => 'Orden de Compra',
                'withPaginator' => false,
                'fetch'         => 'AsociadosYFaltantesDeRecibir',
                'withToolbar'   => false,
                'loadAuto'      => false,
                'iniSection'    => 'reducido',
                'detailGrid'    => $detailGrid,
                'id'            => 'OrdenesDeComprasRelacionadas'
            )
        );
        unset($detailGrid);

        /**
         * Ordenes de Compras Articulos (Paso 2 - Hija)
         */
        $config->loadAuto       = false;
        $config->title          = null;
        $config->withPaginator  = false;
        $config->id             = $this->getName() . '_OrdenesDeComprasArticulos';

        $this->view->gridOrdenesDeCompraArticulos = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDeComprasArticulos',
            $config,
            '',
            'reducido'
        );
        unset($config);
        /**
         * Articulos del Remito (Paso 3)
         */
        $config->abmWindowTitle  = 'Artículo';
        $config->abmWindowWidth  = 650;
        $config->abmWindowHeight = 200;
        $config->withPaginator   = false;
        $config->title           = 'Artículo';
        $config->loadAuto        = false;
        $config->autoSave        = true;

        $this->view->gridRemitosArticulos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosArticulosDeIngresos',
            $config,
            'abmeditor',
            'wizard'
        );
        unset($config);

        /**
         * Grilla Remitos de Ingresos
         */
        $this->view->grid = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosDeIngresos',
            array(
                'abmForm' => '',
                'loadAuto' => false,
                'viewConfig' => new Zend_Json_Expr("
                    {
                    forceFit:true,
                    enableRowBody:false,
                    getRowClass: function(record, rowIndex, p, store) {
                        var tc = record.get('TipoDeComprobante');
                        return (tc == 17)? 'x-grid3-row-red' : '';
                        }
                    }")
            ), // Evitamos que radGrid cree automaticamente el formulario al no tenerlo
            'abmeditor'
        );
    }

    public function cerrarremitoAction()
    {
        //ini_set("display_errors",1);
        $this->_helper->viewRenderer->setNoRender(true);

        $request    = $this->getRequest();
        $idRemito   = $request->getParam('id');

        $db         = Zend_Registry::get('db');
        $idRemito   = $db->quote($idRemito, 'INTEGER');

        try {
            $M_RE = new Almacenes_Model_DbTable_RemitosDeIngresos(array(), false);
            $M_RE->cerrar($idRemito);

            echo '{success: true}';
        } catch (Rad_Db_Table_Exception $e) {
            //error_log($e->getMessage());
            echo "{success: false, msg: '" . addslashes($e->getMessage()) . "'}";
        }
    }

}