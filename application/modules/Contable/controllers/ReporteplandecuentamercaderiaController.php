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

    protected function buildWhere($param)
    {

    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();
        $idLibro = $param['libro'];
        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        $texto = "Reporte Plan de Cuenta Mercadería".$texto;
        $path = '/../birt/Reports/Reporte_PlanDeCuentaMercaderia.rptdesign';
        $where = " WHERE " . $this->buildWhere($param);
        $report->renderFromFile($file, $formato, array(
            'TEXTO' => $texto,
            'WHERE' => $where,
            'IDLIBRO' => $idLibro
        
        ));
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."_".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }
}