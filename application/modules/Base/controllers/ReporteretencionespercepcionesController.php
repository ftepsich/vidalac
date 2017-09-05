<?php

use Rad\Util\FileExport;

class Base_ReporteRetencionesPercepcionesController extends Rad_Window_Controller_Action {

    protected $title = 'Reporte Retenciones y Percepciones';

    public function initWindow() {
        
    }

    public function verreporteAction() {

        $this->_helper->viewRenderer->setNoRender(true);
        $report = new Rad_BirtEngine();
        $rq = $this->getRequest();
        $db = Zend_Registry::get('db');
        $param['modelo'] = $db->quote($rq->modelo, 'INTEGER');
        // Utilizo LibroIVA solo para obtener el periodo.
        $param['libro']  = $db->quote($rq->libro, 'INTEGER');
        // Obtener Formato seleccionado.
        $formato = ($rq->formato) ? $rq->formato : 'txt';
        switch ($formato) {
        case 'txt':
        if ($param['modelo'] == 1 || $param['modelo'] == 2) { // SIAGER Retenciones / Percepciones       
            $M_CI  = new Base_Model_DbTable_ConceptosImpositivos();
            $datos = $M_CI->exportadorSIAGER($param['modelo'],$param['libro']);
            $exportador = new FileExport(FileExport::MODE_SEPARATOR);
            $exportador->setLineEnd("\r\n");
            $exportador->addAll($datos);
            $contenido = str_replace("\n\n", "", $exportador->getContent());
            $M_LI   = new Contable_Model_DbTable_LibrosIVA();
            $R_LI   = $M_LI->find($param['libro'])->current();
            $nombre = $R_LI->Anio . "-" . str_pad($R_LI->Mes, 2, '0', STR_PAD_LEFT) . "_SIAGER_" . ( ($param['modelo'] == 1) ? "Retenciones" : "Percepciones") . "_" . date('YmdHis') . ".txt";
            header("Content-disposition: attachment; filename=$nombre");
            header("Content-type: text/csv");
            echo $contenido;
        } else {
            throw new Exception("No se pudo generar el reporte");
        }
        break;
        case 'pdf':
        case 'xls':
        if ($param['modelo'] == 1 || $param['modelo'] == 2) { // SIAGER Retenciones / Percepciones
            $M_LI  = new Contable_Model_DbTable_LibrosIVA();
            $R_LI = $M_LI->find($param['libro'])->current(); 
            if ($R_LI) {
               $periodo = $R_LI->Descripcion;
            } else {
               throw new Exception("No se pudo generar el reporte");
            }
            switch ($param['modelo']) {
            case 1:
                 $file = APPLICATION_PATH . "/../birt/Reports/Siager_Retenciones.rptdesign";
                 break;
            case 2:
                 $file = APPLICATION_PATH . "/../birt/Reports/Siager_Percepciones.rptdesign";
                 break;
            }
            $report->renderFromFile($file, $formato, array(
                'TEXTO'   => "SIAGER Retenciones para el Periodo $periodo",
                'PERIODO' => "$periodo"
            ));
            $NombreReporte = 'Reporte_' .  $R_LI->Anio . "-" . str_pad($R_LI->Mes, 2, '0', STR_PAD_LEFT) . '_SIAGER_' . ( ($param['modelo'] == 1) ? 'Retenciones' : 'Percepciones') . '_' . date('YmdHis');
            $report->sendStream($NombreReporte);
        } else {
            throw new Exception("No se pudo generar el reporte");
        }
        break;
        }
    }
}
