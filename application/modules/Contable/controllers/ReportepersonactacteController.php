<?php

/**
 * Contable_ReportepersonactacteController
 *
 * Reporte de Clientes con filtros 
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Contable_ReportepersonactacteController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReportePersonaCtaCteController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Cta Cte de una persona';

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

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        $param['persona']      = $db->quote($rq->persona, 'INTEGER');
        $param['cabecera']     = $db->quote($rq->cabecera, 'INTEGER');
        $param['tipoPersona']  = $db->quote($rq->tipoPersona, 'INTEGER');
        
        /*si no se indica es consolidado*/
        $param['tipoPersona'] = ($param['tipoPersona']) ? $param['tipoPersona'] : 3;

        if ($rq->desde) {   
            $param['desde'] = $rq->desde;
            $texto = 'Desde '. date('d/m/Y',strtotime ($rq->desde)); // No quotear
        }
        else {              
            $param['desde'] = "1900-01-01";
        } 
        $report->setParameter('FechaDesde', $param['desde'],    'Datetime');  

        if ($rq->hasta) {
            $param['hasta'] = $rq->hasta;
            if ($texto) $texto = $texto . ' y ';
            $texto = $texto .'Hasta '. date('d/m/Y',strtotime ($rq->hasta)); // No quotear
        } 
        else {
            $param['hasta'] = "2999-01-01";           
        } 
        $report->setParameter('FechaHasta', $param['hasta'],    'Datetime');

        if ($param['cabecera']) {
            switch ($param['cabecera']) {
                case 1: 
                    $cabeceraUsar   = 'CabeceraInterna';
                break;
                case 2: 
                    $cabeceraUsar   = 'Cabecera';
                break;
            }
        } else { $cabeceraUsar = 'CabeceraInterna'; }           
 

        $report->setParameter('Id',  $param['persona'],  'Int');
        $report->setParameter('tipo',  $param['tipoPersona'],  'Int');

        //Rad_Log::debug($param['persona']);

        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        $file = APPLICATION_PATH . '/../birt/Reports/Rep_CuentasCorrientes_Detalle.rptdesign';
        $where = '';

        $report->renderFromFile( $file, $formato, array(
            'TEXTO'     => $texto,
            'CABECERA'  => $cabeceraUsar
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = 'Reporte_de_cuenta_corriente_'.$nombreRep."___".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }
}