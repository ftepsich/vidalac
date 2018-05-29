<?php

/**
 * Rrhh_InfPadronEmpleadosPorSucursalController
 *
 * Informe Cuenta Corriente Clientes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Rrhh_InfPadronEmpleadosPorSucursalController
 * @extends Rad_Window_Controller_Action
 */
class Rrhh_InfPadronEmpleadosPorSucursalController extends Rad_Window_Controller_Action
{
    protected $title = 'Informe Padron Empleados por Sucursal';

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
        $report->setParameter('Id', 2, 'Int');

        $rq = $this->getRequest();

        $db = Zend_Registry::get('db');

        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        if ($formato == 'xls') {
            // Detallado
            $texto = 'Padron de Empleados';
            $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Empleados_paraExcell.rptdesign';
        } else {
            $texto = 'Padron de Empleados por Empresa '.$texto;
            $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Empleados_porSucursal.rptdesign';
        }
        
        $where = $this->buildWhere($param);
        
        $report->renderFromFile($file, $formato, array(
            'TEXTO' =>  $texto
        ));
        
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = $nombreRep."___".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }

}