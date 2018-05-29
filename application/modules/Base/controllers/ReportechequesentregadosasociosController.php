<?php

/**
 * Base_ReportechequesentregadosasociosController
 *
 * Reporte con filtros de Ventas o Compras en monto
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_ReporteChequesEntregadosASociosController
 * @extends Rad_Window_Controller_Action
 */
class Base_ReporteChequesEntregadosASociosController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Cheques entregados a Socios';

    public function initWindow()
    {
    }
    
    protected function buildWhere($param)
    {
        $where = array();
        if ($param['cheques']) {
            foreach ($param['cheques'] as $i => $li)
                if (!ctype_digit($li))
                    unset($param['cheques'][$i]);

            $where = "C.Id IN ({$param['cheques']})";
        }
        return $where;
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $report = new Rad_BirtEngine();
        $report->setParameter('Id', 2, 'Int');

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        $param['cheques']       = $rq->cheques;

        // marco como TieneRecivo al cheque con destino a socio, paso como parametros el where los cheques a marcar
        if($param['cheques']){ 
            $M_CP = new Base_Model_DbTable_ChequesPropios();
            $M_CP->marcarTieneRecibo($param['cheques']);
        }        

        $cabeceraUsar           = 'Cabecera';
        $file                   = APPLICATION_PATH . '/../birt/Reports/Comp_Cheques_EntregadosASocios.rptdesign';
        $texto                  = 'Reporte de Cheques entregados a Socios';
        $formato                = 'pdf';
        $where                  = $this->buildWhere($param);
        $parametros             = array(
            'WHERE'         => " WHERE ".$where,
            'SOCIO'         => $rq->socio,
            'FECHA'         => date('d/m/Y',strtotime($rq->fecha)),
            'OBSERVACIONES' => $rq->observaciones
        );

        $report->renderFromFile($file, $formato, $parametros);
        
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = $nombreRep."___".date('YmdHis');        
        
        $report->sendStream($NombreReporte);


    }
        
}
