<?php

/**
 * Contable_ReporteplandecuentamercaderiaController
 *
 *
 * @class Contable_ReporteplandecuentamercaderiaController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReportePlanDeCuentaMercaderiaController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Plan de Cuenta Mercadería';

    public function initWindow()
    {
        
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();
     
        $fechaDesde      = ($rq->fechadesde) ? $rq->fechadesde : '';
        $fechaHasta      = ($rq->fechahasta) ? $rq->fechahasta : '';
	    $idLibroIVADesde = ($rq->libroivadesde) ? $rq->libroivadesde : 0;
        $idLibroIVAHasta = ($rq->libroivahasta) ? $rq->libroivahasta : 0;
        $idProveedor     = ($rq->proveedor) ? $rq->proveedor : 0;
 
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        $path = APPLICATION_PATH.'/../birt/Reports/Reporte_PlanDeCuentaMercaderia.rptdesign';

        $where = "WHERE PlanDeCuenta_Id = 399";
        if ( $fechaDesde <> '' ) {
            $report->setParameter('fechaDesde', $fechaDesde, 'String');
            //$where .= " AND Comprobante_FechaEmision >= STR_TO_DATE('".$fechaDesde."','%Y-%m-%d')";
            $where .= " AND Comprobante_FechaEmision >= '".$fechaDesde."'";
        }
        if ( $fechaHasta <> '' ) {
            $report->setParameter('fechaHasta', $fechaHasta, 'String');
            //$where .= " AND Comprobante_FechaEmision <= STR_TO_DATE('".$fechaHasta."','%Y-%m-%d')";
            $where .= " AND Comprobante_FechaEmision <= '".$fechaHasta."'";
        }
        if ( $idLibroIVADesde <> 0 ) {
            $report->setParameter('idLibroIVADesde', $idLibroIVADesde, 'Int');
            $where .= " AND Comprobante_LibroIva >= ".$idLibroIVADesde;
        }
        if ( $idLibroIVAHasta <> 0 ) {
            $report->setParameter('idLibroIVAHasta', $idLibroIVAHasta, 'Int');
            $where .= " AND Comprobante_LibroIva <= ".$idLibroIVAHasta;
        }
        if ( $idProveedor <> 0 ) {
            $report->setParameter('idProveedor', $idProveedor, 'Int');
            $where .= " AND Persona_Id = ".$idProveedor;
        } 
        $report->renderFromFile($path, $formato, array(
            'WHERE' => $where
        ));
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte Plan de Cuenta Mercadería".$nombreRep."_".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }
}
