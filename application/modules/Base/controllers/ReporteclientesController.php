<?php

/**
 * Base_ReporteClientesController
 *
 * Reporte de Clientes con filtros 
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarVendedoresController
 * @extends Rad_Window_Controller_Action
 */
class Base_ReporteClientesController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Personas';

    public function initWindow()
    {
        
    }

    protected function buildWhere($param)
    {
        $where = array();
        if ($param['desde'])        $where[] = "Personas.FechaDeAlta >= {$param['desde']}";
        if ($param['hasta'])        $where[] = "Personas.FechaDeAlta <= {$param['hasta']}";
        if ($param['inscripcion'])  $where[] = "Personas.ModalidadIva  = {$param['inscripcion']}'";
        if ($param['provincia'])    $where[]  = "Personas.Id IN ( SELECT Direcciones.Persona FROM Direcciones INNER JOIN Localidades ON Localidades.Id = Direcciones.Localidad WHERE Localidades.Provincia = {$param['provincia']})";
        if ($param['localidad'])    $where[] = "Personas.Id IN ( SELECT Direcciones.Persona FROM Direcciones WHERE Direcciones.localidad = {$param['localidad']})";
        
        if ($param['tipopersona'] != 1) {
            $where[] = "Personas.EsProveedor = 1";
        } else {
            $where[] = "Personas.EsCliente = 1";    
        }

        $where = implode (' and ',$where);

        if ($where) $where = ' where '.$where;

        return $where;
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();
        $report->setParameter('Id', 2, 'Int');

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        if ($rq->desde) {   $param['desde'] = $db->quote($rq->desde);         }
        else {              $param['desde'] = '';      } 

        if ($rq->hasta) {   $param['hasta'] = $db->quote($rq->hasta);         }
        else {              $param['hasta'] = '';      } 
        
        if($rq->inscripcion)    $param['inscripcion'] = $db->quote($rq->inscripcion, 'INTEGER');
        if($rq->provincia)      $param['provincia']   = $db->quote($rq->provincia, 'INTEGER');
        if($rq->localidad)      $param['localidad']   = $db->quote($rq->localidad, 'INTEGER');
        if($rq->tipopersona)    $param['tipopersona'] = $db->quote($rq->tipopersona, 'INTEGER');
 
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        $file = APPLICATION_PATH . '/../birt/Reports/ListadoDeClientesFiltro.rptdesign';
        
        $where = $this->buildWhere($param);

        $report->renderFromFile( $file, $formato, array(
            'WHERE' => $where
        ));

        Rad_Log::debug($where);

        $report->sendStream();
    }
}