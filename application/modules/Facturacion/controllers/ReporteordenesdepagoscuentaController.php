<?php

/**
 * Facturacion_ReporteOrdenesDePagosCuentaController
 *
 * 
 *
 * 
 * @package Aplicacion
 * 
 * @class Facturacion_ReporteOrdenesDePagosCuentaController
 * @extends Rad_Window_Controller_Action
 */
class Facturacion_ReporteOrdenesDePagosCuentaController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Ordenes de Pagos (Plan de Cuenta)';

    public function initWindow()
    {
    }
    
    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $texto = 'Reporte de Ordenes de Pagos'. $texto;

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        if ($rq->cuenta) {
           $report->setParameter('cuenta', $rq->cuenta, 'Int');
            $row  = $db->fetchOne("SELECT descripcion FROM vplanesdecuentasarbol WHERE id = ".$rq->cuenta);
            if ($row) {
              $texto .= " para el Plan de Cuenta '".$row."'";
            }
        }
        
        if ($rq->libroivadesde) {
           $report->setParameter('libroivadesde',$rq->libroivadesde, 'Int');
            $row  = $db->fetchOne("SELECT descripcion FROM librosiva WHERE id = ".$rq->libroivadesde);
            if ($row) {
              $texto .= " desde ".$row;
            }
        }

        if ($rq->libroivahasta) {
           $report->setParameter('libroivahasta',$rq->libroivahasta, 'Int');
            $row  = $db->fetchOne("SELECT descripcion FROM librosiva WHERE id = ".$rq->libroivahasta);
            if ($row) {
              $texto .= " hasta ".$row;
            }
        }

        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        $file  = APPLICATION_PATH . '/../birt/Reports/Reporte_OrdenesDePagosCuenta.rptdesign';

        $report->renderFromFile($file, $formato, array(
            'TEXTO' =>  $texto
        ));
        
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = $nombreRep."___".date('YmdHis');
        
        $report->sendStream($NombreReporte);
    }
}
