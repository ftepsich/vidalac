<?php

/**
 * Contable_Reporteconceptosimpositivoscontroller
 *
 *
 * @class Contable_reporteconceptosimpositivosController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReporteConceptosImpositivosController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Conceptos Impositivos';

    public function initWindow()
    {
        
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        $modelo             = 0;
        $conceptoRetencion  = ($rq->conceptoretencion) ? $rq->conceptoretencion : '';
        $conceptoPercepcion = ($rq->conceptopercepcion) ? $rq->conceptopercepcion : '';
        $tipodeconcepto     = ($rq->tipodeconcepto) ? $rq->tipodeconcepto : '';
        $jurisdiccion       = ($rq->jurisdiccion)  ? $rq->jurisdiccion : '';
        $fechaDesde         = ($rq->fechadesde) ? $rq->fechadesde : '';
        $fechaHasta         = ($rq->fechahasta) ? $rq->fechahasta : '';
        $formato            = ($rq->formato) ? $rq->formato : 'pdf';
        $texto              = '';
        $where              = '';
        $reporteSufijo      = '';
        
        // modelo = 1 ( Solo Retenciones )
        // modelo = 2 ( Solo Percepciones )
        // modelo = 3 ( Retenciones y Percepciones )

        if ($conceptoRetencion <> '') {
           if ($conceptoPercepcion <> '') {
              $modelo = 3;
           } else {
              $modelo = 1;
           } 
        } else {
           if ($conceptoPercepcion <> '') {
              $modelo = 2;
           }
        }

        $report->setParameter('modelo', $modelo, 'Int');

        $path  = APPLICATION_PATH . "/../birt/Reports/Reporte_ConceptosImpositivos.rptdesign";

        switch ($modelo) {
            case 1:
                $texto = "Reporte de Retenciones";
                $where = "WHERE Tipo = 'RETENCION'";
                $reporteSufijo .= "_retenciones";
                break;
            case 2:
                $texto = "Reporte de Percepciones";
                $where = "WHERE Tipo = 'PERCEPCION'";
                $reporteSufijo .= "_percepciones";
                break;
            case 3:
                $texto = "Reporte de Retenciones y Percepciones";
                $where = "WHERE Tipo IN ('RETENCION', 'PERCEPCION')";
                $reporteSufijo .= "_retenciones_percepciones";
                break;
        }

        if ( $tipodeconcepto <> '' ) {
           $where .= " AND Impuesto = ".$tipodeconcepto;
           $row  = $db->fetchOne("SELECT descripcion FROM tiposdeconceptos WHERE id = ".$tipodeconcepto);
           if ($row) {
             $texto .= " de ".$row;
             $reporteSufijo .= "_".strtolower(str_replace(' ','_', $row));
           }

        }

        if ( $jurisdiccion <> '' ) {
           $where .= " AND Jurisdiccion = ".$jurisdiccion;
        }

        if ( $fechaDesde <> '' && $fechaHasta <> '' ) {
            $where .= " AND Fecha BETWEEN '".$fechaDesde."' AND '".$fechaHasta."'";
            $texto .= " Desde : ".$fechaDesde." Hasta : ".$fechaHasta;
            $reporteSufijo .= "_FechaDesde_".$fechaDesde."_FechaHasta_".$fechaHasta;
        }

        $where .= " AND Importe <> 0 ";

        $report->renderFromFile($path, $formato, array(
            'TEXTO' => $texto,
            'WHERE' => $where
        ));
        $NombreReporte  = "reporte_".$reporteSufijo;
        $report->sendStream($NombreReporte);
    }
}
