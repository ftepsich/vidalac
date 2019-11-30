<?php

/**
 * Contable_ReporteComprobantesSinIvaController
 *
 *
 * @class Contable_ReporteComprobantesSinIvaController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReporteComprobantesSinIvaController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Comprobantes Sin IVA';

    public function initWindow()
    {
        
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        $idPeriodoImputacionDesde   = ($rq->periodoimputaciondesde) ? $rq->periodoimputaciondesde : 0;
        $idPeriodoImputacionHasta   = ($rq->periodoimputacionhasta) ? $rq->periodoimputacionhasta : 0;
        $incluirPeriodoImputacion00 = ($rq->periodoimputacion00)  ? $rq->periodoimputacion00 : 0;
        $formato    = ($rq->formato) ? $rq->formato : 'pdf';
        $fromQuery  = "vPeriodosImputacionSinIVA";
        $whereQuery = " WHERE C.NumeroSinIVA IS NOT NULL";
        $texto = "Otros gastos sin IVA";
        $path  = APPLICATION_PATH . "/../birt/Reports/Reporte_ComprobantesSinIVA.rptdesign";

        if ( $idPeriodoImputacionDesde <> 0 ) {
            $report->setParameter('idPeriodoImputacionDesde', $idPeriodoImputacionDesde, 'Int');
            $whereQuery .= " AND C.PeriodoImputacionSinIva >= ".$idPeriodoImputacionDesde;
            $row  = $db->fetchOne("SELECT descripcion FROM PeriodosImputacionSinIVA WHERE id = ".$idPeriodoImputacionDesde); 
            if ($row) {
              $reporteSufijo .= "_PeriodoDesde_".$row;
            }
        }
        if ( $idPeriodoImputacionHasta <> 0 ) {
            $report->setParameter('idPeriodoImputacionHasta', $idPeriodoImputacionHasta, 'Int');
            $whereQuery .= " AND C.PeriodoImputacionSinIva <= ".$idPeriodoImputacionHasta;
            $row  = $db->fetchOne("SELECT descripcion FROM PeriodosImputacionSinIVA WHERE id = ".$idPeriodoImputacionHasta); 
            if ($row) {
              $reporteSufijo .= "_PeriodoHasta_".$row;
            }
        }
        if ( $incluirPeriodoImputacion00 <> 0 ) {
          $fromQuery = "PeriodosImputacionSinIva";
        } 
        
        $report->renderFromFile($path, $formato, array(
            'TEXTO'      => $texto,
            'FROMQUERY'  => $fromQuery,
            'WHEREQUERY' => $whereQuery
        ));
        Rad_Log::debug($texto.$reporteSufijo);
        $NombreReporte      = "Reporte_".str_replace(  array(" ","/"), array("_","-") , $texto.$reporteSufijo );     
        $report->sendStream($NombreReporte);
    }
}
