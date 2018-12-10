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
     
     
        $report->renderFromFile($path, $formato, array(
            'TEXTO' => $texto,
         
        ));
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."_".date('YmdHis');        
        $report->sendStream($NombreReporte);
    }
}
