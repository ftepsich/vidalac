<?php

/**
 * Facturacion_OrdenesDeComprasController
 *
 * Controlador de Ordenes de Compras
 *
 * @package Aplicacion
 * @subpackage Facturacion
 * @class Facturacion_OrdenesDeComprasController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Facturacion_OrdenesDeComprasController extends Rad_Window_Controller_Action
{

    protected $title = 'Ordenes de Compras';

    public function initWindow()
    {

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA HIJA
        // ----------------------------------------------------------------------------------------------------------

        $parametrosAdc->withPaginator = false;
        $parametrosAdc->loadAuto = false;
        $parametrosAdc->id = 'OdCAGrillaHija';

        $this->view->gridArt = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDeComprasArticulos',
            $parametrosAdc,
            'abmeditor',
            'wizard'
        );

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------

        $this->view->form = $this->view->radForm(
            'Facturacion_Model_DbTable_OrdenesDeCompras', // Nombre del Modelo
            'datagateway'
        );

        $parametrosAdc = null;
        $parametrosAdc->abmWindowTitle = 'Orden de Compra';
        $parametrosAdc->abmWindowWidth = 900;
        $parametrosAdc->abmWindowHeight = 560;
        $parametrosAdc->sm = new Zend_Json_Expr("new Ext.grid.RowSelectionModel({singleSelect: true})");

        $this->view->grid = $this->view->radGrid(
            'Facturacion_Model_DbTable_OrdenesDeCompras',
            $parametrosAdc,
            'abmeditor'
        );
    }

    public function getdetalleestadoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->Id;

        $m = new Facturacion_Model_DbTable_ComprobantesRelacionados;
        $d = $m->getDetalleCantidadesRelacionadas($id);

        $html = '<div class="panelDeDetalles panelDetalleAzul"><h3>Detalle</h3><div class="detalle"><div class="col3 bborder">Pedido</div><div class="col3 bborder">Recibido</div><div class="col3 bborder">Pendiente</div>';

        foreach ($d as $key => $value) {
            $html .= '<div class="listrow"><b>'.$value['Descripcion'].'</b> <div class="col3 bborder"><span>'.$value['CantidadComp'].'</span></div><div class="col3 bborder"><span>'. $value['CantidadCompRel'] .'</span></div><div class="col3 bborder"><span>'. ($value['CantidadComp'] - $value['CantidadCompRel']).'</span></div></div>';
        }
        $html .= '</div></div>';
        echo $html;
    }

}
