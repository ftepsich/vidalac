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
    protected $title = 'Reporte Plan de Cuenta MercaderÃ­a';

    public function initWindow()
    {
        
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();

	$idLibroIVADesde = $rq->libroivadesde;
        $idLibroIVAHasta = $rq->libroivahasta;
        $idProveedor     = $rq->proveedor;
        $M_L = new Contable_Model_DbTable_LibrosIVA();
            $R_L = $M_L->find($param['libro'])->current();
            if ($R_L) {
                $tPeriodo = $R_L->Descripcion;
            } else {
                $tPeriodo = 'Desconocido';
            }


        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        $texto = "Reporte Plan de Cuenta MercaderÃ­a ".date("Y-m-d H:i:s")." -> Desde : ".$tPeriodo." Hasta : ".$idLibroIVAHasta." Proveedor : ".$idProveedor;
        $path = APPLICATION_PATH.'/../birt/Reports/Reporte_PlanDeCuentaMercaderia.rptdesign';
        $report->setParameter('idLibroIVADesde', $idLibroIVADesde, 'Int');
        $report->setParameter('idLibroIVAHasta', $idLibroIVAHasta, 'Int');
        if ( $idProveedor <> 0 ) {
            $report->setParameter('idProveedor', $idProveedor, 'Int');
        } 
        $report->renderFromFile($path, $formato, array(
            'TEXTO' => $texto
        ));
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."_".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }
}