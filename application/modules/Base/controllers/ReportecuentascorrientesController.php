<?php

/**
 * Base_ReporteCuentasCorrientesController
 *
 * Reporte con filtros de las Cuentas Corrientes de los Proveedores / Clientes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_ReporteCuentasCorrientesController
 * @extends Rad_Window_Controller_Action
 */
class Base_ReporteCuentasCorrientesController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Estado de Cuenta Corriente';

    public function initWindow()
    {
    }
    
    protected function buildWhere($param)
    {
        $where = array();
        //if ($param['fecha'])   $where[] = "CC.FechaComprobante <= {$param['fecha']}";
        
        if ($param['persona']) {
            $where[] = "P.Id = {$param['persona']}";  
        } /*else {
            // Si viene la persona dejo de tener en cuenta si es cliente o proveedor
            if ($param['tipo']) {
                switch ($param['tipo']) {
                    case 1: $where[] = "P.EsCliente = 1"; break;
                    case 2: $where[] = "P.EsProveedor = 1"; break;
                } 
            }
        }*/

        // filtra siempre si es por cliente o provedor, elijan o no una persona
        if ($param['tipo']) {
            switch ($param['tipo']) {
                case 1: $where[] = "P.EsCliente = 1"; break;
                case 2: $where[] = "P.EsProveedor = 1"; break;
            } 
        } 
 
        if ($param['ocultarCeros'] == 1) {

              if ( $param['modelo'] != 3 ) {
                   $where[] = "fPersona_CuentaCorriente_A_Fecha(P.Id,".$param['fecha'].",".$param['modelo'].",".$param['tipo'].") <> 0";
              } else {
                   $where[] = "IFNULL(fComprobante_Monto_Disponible(C.Id),0) <> 0";
              }
        }
        
        $where = implode (' and ', $where);

        return $where;
        
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();
        $report->setParameter('Id', 2, 'Int');

        $rq = $this->getRequest();
        
        $db = Zend_Registry::get('db');
        
        if ($rq->fecha) {   $param['fecha'] = $db->quote($rq->fecha);         }
        else {              $param['fecha'] = $db->quote(date('Y-m-d'));      }
        
        $param['persona']   = $db->quote($rq->persona, 'INTEGER');
        $param['tipo']      = $db->quote($rq->tipo, 'INTEGER');
        $param['modelo']    = $db->quote($rq->modelo, 'INTEGER');
        $param['cabecera']  = $db->quote($rq->cabecera, 'INTEGER');
        $param['ocultarCeros']  = $db->quote($rq->ocultarCeros, 'INTEGER');

        if ($param['tipo']) {
            switch ($param['tipo']) {
                case 1: 
                    $texto          = 'Cuenta Corriente de Clientes';
                    $esCliente      = ' ';
                    $esProveedor    = '(P)';
                    $tipoPersona    = 1;
                break;
                case 2: 
                    $texto          = 'Cuenta Corriente de Proveedores';
                    $esCliente      = '(C)';
                    $esProveedor    = ' ';  
                    $tipoPersona    = 2;
                break;
            }
        }
        
        if ($param['persona']) {
                    $texto          = 'Cuenta Corriente de una persona';
                    //$esCliente      = '(C)';
                    //$esProveedor    = '(P)';             
        }

        if ($param['cabecera']) {
            switch ($param['cabecera']) {
                case 1: 
                    $cabeceraUsar   = 'CabeceraInterna';
                break;
                case 2: 
                    $cabeceraUsar   = 'Cabecera';
                break;
            }
        } else { $cabeceraUsar = 'CabeceraInterna';  }
        
        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        if ($rq->fecha)   {     $texto .= " a fecha ".date('d/m/Y',strtotime($rq->fecha));         } 
        else {                  $texto .= " a fecha ".date('d/m/Y',strtotime(date('Y-m-d')));      }
        
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';

        switch ($param['modelo']) {
            case 1: 
                $f = '/../birt/Reports/Rep_CuentasCorrientes_Completo.rptdesign';
            break;
            case 2: 
                $f = '/../birt/Reports/Rep_CuentasCorrientes_Cheques.rptdesign';
            break;
            case 3: 
                $texto = " Reporte de composición de saldos ".$texto;
                $f = '/../birt/Reports/Rep_CuentasCorrientes_Parcial.rptdesign';
            break;
        }
        $file = APPLICATION_PATH . $f;

        $orden = 'P.RazonSocial ';
			
        $where = $this->buildWhere($param);
    
        $report->renderFromFile( $file, $formato, array(
            'WHERE'      => ' WHERE '.$where,
            'TEXTO'      => $texto,
            'ORDEN'      => ' ORDER BY '.$orden,
            'EsCliente'  => $esCliente,
            'EsProveedor'=> $esProveedor,
            'FECHA'      => $param['fecha'],
            'MODELO'     => $param['modelo'],
            'CABECERA'   => $cabeceraUsar,
            'TIPO'       => $tipoPersona
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."___".date('YmdHis');        
        
        $report->sendStream($NombreReporte);
    }
        
}
