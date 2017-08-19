<?php

/**
 * Contable_ReportePercepcionesyRetencionesController
 *
 * Reporte con filtros de Ventas o Compras en monto
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Contable_ReportePercepcionesyRetencionesController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReportePercepcionesyRetencionesController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Percepciones y retenciones sobre Compras';

    public function initWindow()
    {
    }
    
    protected function buildWhere($param)
    {
        $where = array();
        if ($param['desde'])   $where[] = "C.FechaEmision >= {$param['desde']}";
        if ($param['hasta'])   $where[] = "C.FechaEmision <= {$param['hasta']}";
        if ($param['persona']) $where[] = "C.Persona = {$param['persona']}";
        if ($param['libroiva']) {
            foreach ($param['libroiva'] as $i => $li)
                if (!ctype_digit($li))
                    unset($param['libroiva'][$i]);

            $where[] = "L.LibroIVA IN ({$param['libroiva']})";
        }
        if ($param['jurisdiccion']) $where[] = "ER.Id = {$param['jurisdiccion']}";
        
    
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

        //$param['desde']    = $db->quote($rq->desde);
        //$param['hasta']    = $db->quote($rq->hasta);
        $param['persona']  = $db->quote($rq->persona , 'INTEGER');
        $param['libroiva'] = $db->quote($rq->libroiva, 'INTEGER');
        $param['cabecera'] = $db->quote($rq->cabecera, 'INTEGER');        
        $param['jurisdiccion'] = $db->quote($rq->jurisdiccion, 'INTEGER');        
        
        

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

        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        if ($rq->desde)   {     $texto .= " desde ".date('d/m/Y',strtotime($rq->desde));         } 
        if ($rq->hasta)   {     $texto .= " hasta ".date('d/m/Y',strtotime($rq->hasta));         } 
        
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        $texto = 'Reporte de '.$texto;
        $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Ventas_y_Compras_Detallado.rptdesign';
        $orden = 'C.FechaEmision, C.Numero, P.RazonSocial ';
        
        $where = $this->buildWhere($param);
        
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
