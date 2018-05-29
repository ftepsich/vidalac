<?php
/**
 * Almacenes_ControlStockController
 *
 * Control de Stock
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @author Martin A. Santangelo
 * @subpackage Almacenes
 * @class Almacenes_ControlStockController
 * @extends Rad_Window_Controller_Action
 */
class Almacenes_ControlStockController extends Rad_Window_Controller_Action
{
    protected $title = 'Control Stock';

    public function initWindow()
    {
        $detailGrids = array();

        $dg->id = 'gridhistorico30dias';
        $dg->remotefield = 'Articulo';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);

        $dg->id = 'gridfuturo30dias';
        $dg->remotefield = 'Articulo';
        $dg->localfield = 'Id';
        $detailGrids[] = $dg;
        unset($dg);


        $config->abmWindowTitle  = 'Articulos';
        $config->abmWindowWidth  = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid = $detailGrids;
        $config->iniSection      = 'reducido';
        $config->title           = 'Stock Actual';

        $this->view->grid = $this->view->radGrid(
            'Almacenes_Model_DbTable_ArticulosStockAlmacen',
            $config,
            ''
        );
    }

    public function stockhistoricoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        $db->query("call p30dias('".date('Y-m-d')."')");

        $articulo = current($this->getRequest()->pfilter);

        $articulo = $db->quote($articulo['data']['value'], 'INTEGER');

        if (!$articulo) {
            //throw new Rad_Exception('No se envio el articulo');
             $this->_sendJsonResponse(array('success' => false, 'msg' => 'No se envio el articulo'));
             return;
        }

        $modelArticulo = new Base_Model_DbTable_Articulos;

        $artRow = $modelArticulo->fetchRow('Id = '.$articulo);

        switch ($artRow->TipoDeControlDeStock) {
            case 1:
                $funcion = 'fStockArticuloFecha';
                break;
            case 2:
                $funcion = 'fStockArticuloFechaXCantidad';
                break;

            case 3:
                $function = null;
        }

//        $rtn = Rad_Util_SqlGrid::fetch("select DATE_ADD(fecha,INTERVAL 1 DAY)as Fecha , fStockArticuloFecha($articulo,fecha) as Stock from tUltimos30Dias order by num desc",$_POST['limit'],$_POST['start']);

        // Cuando es para ver el pasado hay que asegurarse que mire ese dia a las 23:59
        if ($funcion) {
            $rtn = Rad_Util_SqlGrid::fetch("select fecha, $funcion($articulo,ADDTIME(fecha ,'23:59:50')) as Stock from tUltimos30Dias order by num desc",$_POST['limit'],$_POST['start']);
        } else {
            $rtn = Rad_Util_SqlGrid::fetch("select fecha, 2 as Stock from tUltimos30Dias order by num desc",$_POST['limit'],$_POST['start']);
        }


        $rtn->metaData->fields[1]['align'] = 'right';

        $this->_sendJsonResponse($rtn);
    }

    public function stockfuturoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');

        //
        $db->query("call p30dias('".date('Y-m-d',strtotime('+30 days'))."')");

        $articulo = current($this->getRequest()->pfilter);

        if (!$articulo) {
            //throw new Rad_Exception('No se envio el articulo');
            $this->_sendJsonResponse(array('success'=>false, 'msg'=>'No se envio el articulo'));
            exit;
        }

        // Escapo los parametros
        $articulo = $db->quote($articulo['data']['value'], 'INTEGER');

        $modelArticulo = new Base_Model_DbTable_Articulos;

        $artRow = $modelArticulo->fetchRow('Id = '.$articulo);

        // Traigo el stock actual
        switch ($artRow->TipoDeControlDeStock) {
            case 1:
                $stockActual = $db->fetchOne("select fStockArticuloEsInsumo($articulo)");
                break;
            case 2:
                $stockActual = $db->fetchOne("select fStockArticuloFechaXCantidad($articulo,now())");
                break;

            case 3:
                $function = null;
        }

        $rtn = Rad_Util_SqlGrid::fetch(
            "select fecha,
                @F := fFaltanteRecibirAFechaPorArticulo(fecha,$articulo) as Pedido,
                @R := (fArticulosRequeridosProduccionAFecha($articulo, fecha)*-1) as Utilizado,
                @P := fProducidoAFechaPorArticulo(fecha,$articulo) as Producido,
                @F+@R+@P+$stockActual as total
            from tUltimos30Dias order by num desc",
            $_POST['limit'],$_POST['start']
        );

        $rtn->metaData->fields[4]['type']  = 'float';
        $rtn->metaData->fields[4]['align'] = 'right';

        $this->_sendJsonResponse($rtn);
    }

}