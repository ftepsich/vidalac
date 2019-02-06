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
    
    public function getdetalleestado2Action()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->Id;

        $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados;

        $sql = "SELECT c_oc.numero as numero,
                       a_oc.descripcion as concepto,
                       cd_oc.cantidad as cantidad ,
                       cd_oc.preciounitario as precio,
                       round(cd_oc.cantidad * cd_oc.preciounitario,4) as monto,
                       c_oc.observacionesimpresas as observaciones
                FROM comprobantes c_oc
                JOIN comprobantesdetalles cd_oc ON cd_oc.comprobante = c_oc.id
                JOIN articulos a_oc             ON a_oc.id = cd_oc.articulo
                JOIN tiposdecomprobantes tc_oc  ON tc_oc.id = c_oc.tipodecomprobante
                WHERE tc_oc.grupo = 5
                  AND EXISTS (  SELECT 1
                                FROM comprobantes c_r
                                JOIN tiposdecomprobantes tc_r  ON tc_r.id = c_r.tipodecomprobante
                                JOIN comprobantesrelacionados cr_oc_r ON cr_oc_r.ComprobantePadre = c_r.id
                                WHERE cr_oc_r.ComprobanteHijo = c_oc.id
                                  AND tc_r.grupo IN (4,10)
                                  AND EXISTS (  SELECT 1
                                                FROM comprobantes c_fc
                                                JOIN tiposdecomprobantes tc_fc        ON tc_fc.id = c_fc.tipodecomprobante
                                                JOIN comprobantesrelacionados cr_r_fc ON cr_r_fc.ComprobantePadre = c_fc.id
                                                WHERE cr_r_fc.ComprobanteHijo  = c_r.id
                                                  AND tc_fc.grupo IN (1) AND c_fc.Id = '".$id."') )";
        
        $db = $M_CR->getAdapter();
        $R_CR = $db->fetchAll($sql);

        if(!empty($R_CR)){
            $count = 1;
            foreach ($R_CR as $row) {
                if ($count == 1) {
                   $html = '<div class="panelDeDetalles panelDetalleAzul"><h3>Detalle Orden de Compra Nro. '.$row['numero'].'</h3><div class="detalle"><div class="col3 bborder">Cantidad</div><div class="col3 bborder">Precio Unitario</div><div class="col3 bborder">Monto</div>';
                }
                $html .= '<div class="listrow">'.$row['concepto'].'<div class="col3 bborder"><span>'.$row['cantidad'].'</span></div><div class="col3 bborder"><span>'.$row['precio'].'</span></div><div class="col3 bborder"><span>'.$row['monto'].'</span></div></div><div class="listrow">'.$row['observaciones'].'</div>';
                $count += 1;
            }
            $html .= '</div></div>';
        } else {
            $html = '<div class="panelDeDetalles panelDetalleAzul"><h3>Sin Orden de Compra Asociada</h3></div>';
        }
        echo $html;
    }

}
