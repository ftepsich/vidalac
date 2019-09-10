<?php

/**
 * Facturacion_ReporteRecibosController
 *
 * 
 *
 * 
 * @package Aplicacion
 * 
 * @class Facturacion_ReporteRecibosController
 * @extends Rad_Window_Controller_Action
 */
class Facturacion_ReporteRecibosController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Recibos';

    public function initWindow()
    {
    }
    
    protected function buildWhere($param)
    {
        $where = array();
        if ($param['fechadesde'])   $where[] = "ComprobantePadre_FechaEmision >= '".$param['fechadesde']."'";
        if ($param['fechahasta'])   $where[] = "ComprobantePadre_FechaEmision <= '".$param['fechahasta']."'";
        if ($param['persona']) $where[] = "Persona = ".$param['persona'];
        $where = implode (' and ', $where);
        return $where;
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $texto = 'Reporte de Recibos'. $texto;

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();

        if ($rq->persona && $rq->persona != "undefined") {
           $param['persona']  = $rq->persona;
           $texto .= " de una persona en particular";
        }
        
        if ($rq->fechadesde) {
           $param['fechadesde'] = $rq->fechadesde;
           $texto .= " desde ".date('d/m/Y',strtotime($rq->fechadesde));
        }

        if ($rq->fechahasta) {
           $param['fechahasta'] = $rq->fechahasta;
           $texto .= " hasta ".date('d/m/Y',strtotime($rq->fechahasta));
        }

        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        $file  = APPLICATION_PATH . '/../birt/Reports/Reporte_Recibos.rptdesign';

        $where = $this->buildWhere($param);

        //Rad_Log::debug($where);

        $report->renderFromFile($file, $formato, array(
            'WHERE' => " WHERE ".$where,
            'TEXTO' =>  $texto
        ));
        
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = $nombreRep."___".date('YmdHis');
        
        $report->sendStream($NombreReporte);
    }
}
