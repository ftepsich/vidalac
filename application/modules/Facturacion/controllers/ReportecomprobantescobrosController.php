<?php

/**
 * Facturacion_ReporteComprobantesCobrosController
 *
 * Reporte con filtros de Ventas o Compras en monto
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Facturacion_ReporteComprobantesCobrosController
 * @extends Rad_Window_Controller_Action
 */
class Facturacion_ReportecomprobantescobrosController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Recibos';

    public function initWindow()
    {
    }
    
    protected function buildWhere($param)
    {
        $where = array();
        if ($param['desde'])   $where[] = "C.FechaEmision >= {$param['desde']}";
        if ($param['hasta'])   $where[] = "C.FechaEmision <= {$param['hasta']}";
        if ($param['persona']) $where[] = "P.Id = {$param['persona']}";
        
        // OP: 9
        // Recibo: 11
        $where[] = 'T.Grupo in (11) and ifnull(C.Anulado,0) <> 1 and C.Cerrado = 1';

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

        if ($rq->desde) {   $param['desde'] = $db->quote($rq->desde);         }
        else {              $param['desde'] = "'1900/01/01'";      } 

        if ($rq->hasta) {   $param['hasta'] = $db->quote($rq->hasta);         }
        else {              $param['hasta'] = $db->quote(date('Y-m-d'));      } 

        $param['persona']       = $db->quote($rq->persona , 'INTEGER');
        $param['cabecera']      = $db->quote($rq->cabecera, 'INTEGER');
        $param['orden']         = $db->quote($rq->orden, 'INTEGER');

        if ($param['cabecera']) {
            switch ($param['cabecera']) {
                case 1: 
                    $cabeceraUsar   = 'CabeceraInterna';
                break;
                case 2: 
                    $cabeceraUsar   = 'Cabecera';
                break;
            }
        } else { $cabeceraUsar = 'CabeceraInterna'; }

        if ($param['persona'])  {   $texto .= " de una persona en particular";                      }
        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        if ($rq->desde)         {   $texto .= " desde ".date('d/m/Y',strtotime($rq->desde));        }
        if ($rq->hasta)         {   $texto .= " hasta ".date('d/m/Y',strtotime($rq->hasta));        }

        $texto = 'Reporte de Recibos'. $texto;
        
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Comprobantes_CobrosyPagos.rptdesign';
        
        if ( $param['orden'] ) {
            switch ( $param['orden'] ) {
                case 1: $orden = 'P.RazonSocial, C.Numero';
                break;
                case 2: $orden = 'P.RazonSocial, C.FechaEmision';
                break;
                case 3: $orden = 'C.FechaEmision, P.RazonSocial, C.Numero';
                break;
                case 4: $orden = 'C.FechaEmision, C.Numero';
                break;
            }
        } else { $orden = 'C.FechaEmision, P.RazonSocial, C.Numero'; }

        //$orden = 'C.FechaEmision, C.Numero, P.RazonSocial ';

        $where = $this->buildWhere($param);

        Rad_Log::debug($where);
        Rad_Log::debug($orden);

        $report->renderFromFile($file, $formato, array(
            'WHERE' => " WHERE ".$where,
            'TEXTO' =>  $texto,
            'ORDEN' =>  " ORDER BY ".$orden,
            'CABECERA' => $cabeceraUsar
        ));
        
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = $nombreRep."___".date('YmdHis');
        
        $report->sendStream($NombreReporte);
    }
}
