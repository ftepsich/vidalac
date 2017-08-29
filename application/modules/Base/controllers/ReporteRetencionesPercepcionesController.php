<?php
use Rad\Util\FileExport;


class Base_ReporteRetencionesPercepcionesController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Retenciones y Percepciones';

    public function initWindow()
    {

    }



   public function verreporteAction ()
    {
       
        $this->_helper->viewRenderer->setNoRender(true);
        $report     = new Rad_BirtEngine();
        $rq         = $this->getRequest();
        $db         = Zend_Registry::get('db');
        $param['modelo'] = $db->quote($rq->modelo, 'INTEGER');
        
        // SIAGER Retenciones / Percepciones
     if ($param['modelo'] == 1 || $param['modelo'] == 2) { // SIAGER Retenciones / Percepciones       
        $modelo     = new Base_Model_DbTable_ConceptosImpositivos();
        $datos = $modelo->exportadorSIAGER();
        $exportador     = new FileExport(FileExport::MODE_SEPARATOR);
        $exportador->setLineEnd("\r\n");
        $exportador->addAll($datos);
        $contenido  = str_replace("\n\n","",$exportador->getContent());
        $reporte = $modelo->current();
        $nombre = $reporte->Anio."-".str_pad($R->Mes,2,'0',STR_PAD_LEFT). "_SIAGER_".( ($param['modelo'] == 10) ? "Retenciones" : "Percepciones")."_".date('YmdHis').".txt";
        header("Content-disposition: attachment; filename=$nombre");
        header("Content-type: text/csv");
        echo $contenido;
    }else{
        throw new Exception ("No se pudo generar el reporte");
    }
   }
}