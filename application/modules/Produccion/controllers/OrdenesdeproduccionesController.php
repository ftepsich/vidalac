<?php

/**
 * Produccion_OrdenesDeProduccionesController
 *
 * Controlador de Ordenes de Producciones
 *
 * @package Aplicacion
 * @subpackage Produccion
 * @class Produccion_OrdenesDeProduccionesController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Produccion_OrdenesDeProduccionesController extends Rad_Window_Controller_Action
{

    protected $title = 'Ordenes de Producciones';

    public function initWindow()
    {
       
       $parametrosAdc = new stdClass();
       $parametrosAdc->id           = 'OdPDetallesHija';
       $parametrosAdc->loadAuto     = false;
       // Grilla detalles
       $this->view->gridDetalles = $this->view->radGrid(

           'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles',
           $parametrosAdc,
           'abmeditor'
        );


        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------

        $this->view->form = $this->view->radForm(
            'Produccion_Model_DbTable_OrdenesDeProducciones', // Nombre del Modelo
            'datagateway'
        );

        $parametrosAdc = null;
        $parametrosAdc->abmForm         = null;
        $parametrosAdc->loadAuto        = false;
        $parametrosAdc->abmWindowTitle  = 'Orden de Produccion';
        $parametrosAdc->abmWindowWidth  = 900;
        $parametrosAdc->abmWindowHeight = 560;
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");
        // $parametrosAdc->viewConfig = new Zend_Json_Expr("{
        //     forceFit: true,
        // }");

        $this->view->grid = $this->view->radGrid(
            'Produccion_Model_DbTable_OrdenesDeProducciones',
            $parametrosAdc,
            'abmeditor'
        );
    }

    public function getrequerimientosproductosAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->id;

        $model  = new Produccion_Model_DbTable_OrdenesDeProducciones;
        $result = $model->getRequerimientosProductos($id);

        $res = array(
            'rows' => $result,
            'success' => true,
            'count'   => count($result)
        );

        echo json_encode($res);
    }
}
