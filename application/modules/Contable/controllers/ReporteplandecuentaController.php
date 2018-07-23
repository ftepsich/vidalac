<?php

/**
 * Contable_ReporteplandecuentaController
 *
 *
 * @class Contable_ReporteplandecuentaController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReportePlanDeCuentaController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Plan de Cuenta';

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
        $M_L = new Contable_Model_DbTable_LibrosIVA();
            $R_L = $M_L->find($param['libro'])->current();
            if ($R_L) {
                $tPeriodo = $R_L->Descripcion;
            }
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        switch ($param['modelo']) {
            case 1:
                $texto  = "Reporte Plan de cuenta General";   
                $formato = 'xls';
                $file = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaGeneral.rptdesign";
                break;
            case 2:
            $texto  = "Reporte Plan de cuenta General";   
                $formato = 'xls';
                $file = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaDetallado.rptdesign";
                break;
            case 3:
            $texto  = "Reporte Plan de cuenta General";   

                $formato = 'xls';
                $file = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaPorGrupo.rptdesign";
                break;
        }

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
        $report->renderFromFile($texto,$path, $formato, array(
            'TEXTO' => $texto,
            'WHERE' => $where
        ));
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."_".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }
}
