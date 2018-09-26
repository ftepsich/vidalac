<?php

/**
 * Almacenes_ReporteDeStockController
 *
 * Reporte con filtros de Stock
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Almacenes
 * @class Almacenes_ReporteDeStockController
 * @extends Rad_Window_Controller_Action
 */
class Almacenes_ReporteDeStockController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Stock';

    public function initWindow()
    {
    }

    protected function buildWhere($param)
    {
        $where = array();
        if ($param['deposito']) {
            $where[] = "AL.Deposito = {$param['deposito']}";
        }

        if ($param['articulo']) {
            $where[] = "M.Articulo = {$param['articulo']}";
        }

        $where = implode (' and ', $where);

        return $where;

    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();
        $report->setParameter('Id', 2, 'Int');

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        // if (Zend_Date::isDate($rq->fecha,'yyyy-MM-dd')) ---> ver control de formato

        // veo si es un reporte a hoy o a una fecha previa/posterior
        if ($rq->fecha) {
            if ($rq->fecha == date('Y-m-d')) {
                $FechaHoy       = true;
            } else {
                $FechaHoy       = false;
            }
            $param['fecha'] = $db->quote($rq->fecha);
        } else {
            $FechaHoy = true;
            $param['fecha'] = $db->quote(date('Y-m-d'));
        }

        $param['cabecera']  = $db->quote($rq->cabecera, 'INTEGER');
        $param['modelo']    = $db->quote($rq->modelo,   'INTEGER');
//        $param['persona']   = $db->quote($rq->persona,  'INTEGER');
        $param['deposito']  = $db->quote($rq->deposito,  'INTEGER');
        $param['articulo']  = $db->quote($rq->articulo,  'INTEGER');
        $param['orden']     = $db->quote($rq->orden,  'INTEGER');

        if ($FechaHoy) {
            switch ($param['modelo']) {
                case 1:
                    $texto  = 'Stock General';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_General.rptdesign';
                break;
                case 2:
                    $texto  = 'Stock por Deposito';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_PorDeposito.rptdesign';
                break;
                case 3:
                    $texto  = 'Stock detallado por MMI';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_DetalleMMI.rptdesign';
                break;
                case 4:
                    $texto  = 'Stock por Grupo de Articulos';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_PorGrupo.rptdesign';
                break;
                case 5:
                    $texto  = 'Stock Valorizado';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_Valorizado.rptdesign';
                break;
                case 6:
                    $texto  = 'Stock Por Cantidad';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_GeneralPorCantidad.rptdesign';
                break;
            }
        } else {
            switch ($param['modelo']) {
                case 1:
                    $texto  = 'Stock General';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_aFecha_General.rptdesign';
                break;
                case 2:
                    $texto  = 'Stock por Deposito';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_aFecha_PorDeposito.rptdesign';
                break;
                case 3:
                    $texto  = 'Stock detallado por MMI';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_aFecha_DetalleMMI.rptdesign';
                break;
                case 4:
                    $texto  = 'Stock por Grupo de Articulos';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_aFecha_PorGrupo.rptdesign';
                break;
                case 5:
                    $texto  = 'Stock Valorizado';
                    $file   = APPLICATION_PATH . '/../birt/Reports/Rep_Stock_Actual_Valorizado.rptdesign';
                break;
            }
        }

        if ($param['cabecera']) {
            switch ($param['cabecera']) {
                case 1:
                    $cabeceraUsar   = 'CabeceraInterna';
                break;
                case 2:
                    $cabeceraUsar   = 'Cabecera';
                break;
            }
        } else {    $cabeceraUsar   = 'CabeceraInterna'; }

        if ($param['deposito']) {
            $M_DP = new Base_Model_DbTable_DepositosPropios;
            $R_DP = $M_DP->find($param['deposito'])->current();

            $texto .= " del deposito en ".$R_DP['Direccion'];
        }

        if ($param['articulo']) {
            $M_A = new Base_Model_DbTable_Articulos;
            $R_A = $M_A->find($param['articulo'])->current();

            $texto .= " del articulo ".$R_A['Descripcion'];
        }


        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        if ($rq->fecha)   {     $texto .= " a fecha ".date('d/m/Y',strtotime($rq->fecha));         }
        else {                  $texto .= " a fecha ".date('d/m/Y',strtotime(date('Y-m-d')));      }

        $formato    = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        $orden  = 'A.Descripcion';

        switch ($param['orden']) {
                case 1:
                $orden  = 'A.Descripcion';
                break;
                case 2:
                $orden  = 'A.Codigo';
                break;
        }

        if ($FechaHoy) {
            $where = $this->buildWhere($param);
            if ($where) $where = ' and '.$where;
        }

        $report->renderFromFile( $file, $formato, array(
            'WHERE'     => $where,
            'TEXTO'     => $texto,
            'ORDEN'     => $orden,
            'FECHA'     => $param['fecha'],
            'CABECERA'  => $cabeceraUsar
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."___".date('YmdHis');

        $report->sendStream($NombreReporte);
    }

}
