<?php

/**
 * Base_ReporteventascomprasController
 *
 * Reporte con filtros de Ventas o Compras en monto
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_ReporteVentasComprasController
 * @extends Rad_Window_Controller_Action
 */
class Facturacion_ReporteComprobantesComprasController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Comrpbantes de Compras';

    public function initWindow()
    {
    }
    
    protected function buildWhere($param)
    {
        $where = array();
        if ($param['desde'])   $where[] = "C.FechaEmision >= {$param['desde']}";
        if ($param['hasta'])   $where[] = "C.FechaEmision <= {$param['hasta']}";
        if ($param['persona']) $where[] = "P.Id = {$param['persona']}";

        if ($param['condCompra']) {
            switch ($param['condCompra']) {
                case 1: 
                    $where[] = "TCP.Id = 1";
                break;
                case 2: 
                    $where[] = "TCP.Id = 2";
                break;
            }
        }

        if ($param['libroiva']) {
            foreach ($param['libroiva'] as $i => $li)
                if (!ctype_digit($li))
                    unset($param['libroiva'][$i]);

            $where[] = "LD.LibroIVA IN ({$param['libroiva']})";
        }
        
        // FV 6 - NDE 12 - NCR 8
        // FC 1 - NCE 7 - NDR 13
        $where[] = 'T.Grupo in (1,7,13) and ifnull(C.Anulado,0) <> 1 and C.Cerrado = 1';

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
        $param['tipo']          = $db->quote($rq->tipo, 'INTEGER');
        $param['libroiva']      = $db->quote($rq->libroiva, 'INTEGER');
        $param['condCompra']    = $db->quote($rq->condCompra, 'INTEGER');
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



        if ($param['condCompra']) {
            switch ($param['condCompra']) {
                case 1: 
                    $texto .= " en Cta. Cte.";
                break;
                case 2: 
                    $texto .= " al contado";
                break;
            }
        }

        if ($param['persona'])  {   $texto .= " de una persona en particular";                      }
        if ($param['libroiva']) {   $texto .= " filtrado por libro de IVA";                         }
        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        if ($rq->desde)         {   $texto .= " desde ".date('d/m/Y',strtotime($rq->desde));        }
        if ($rq->hasta)         {   $texto .= " hasta ".date('d/m/Y',strtotime($rq->hasta));        }

        $texto = 'Reporte de Comprobantes de Compra'. $texto;
        
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Comprobantes_ComprasyVentas.rptdesign';
        
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
                case 5: $orden = 'TCP.Codigo, C.FechaEmision, C.Numero';
                break;
                case 6: $orden = 'TCP.Codigo, C.Numero';
                break;
                case 7: $orden = 'L.Descripcion, C.FechaEmision, C.Numero';
                break;
                case 8: $orden = 'L.descripcion, C.Numero';
                break;
            }
        } else { $orden = 'C.FechaEmision, P.RazonSocial, C.Numero'; }

        //$orden = 'C.FechaEmision, C.Numero, P.RazonSocial ';

        $where = $this->buildWhere($param);

        //Rad_Log::debug($where);
        //Rad_Log::debug($orden);

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
