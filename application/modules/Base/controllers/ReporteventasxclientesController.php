<?php
class Base_ReporteVentasxClientesController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Estadistico de Ventas x Cliente';

    public function initWindow()
    {
        
    }

    protected function buildWhere($param)
    {
        $where = array();
        
        if ($param['desde'])   $where[] = "C.FechaEmision >= {$param['desde']}";
        if ($param['hasta'])   $where[] = "C.FechaEmision <= {$param['hasta']}";
        if ($param['cliente']) $where[] = "C.Persona IN ({$param['cliente']})";
            
        $where[] = "Anulado = 0 AND Cerrado = 1 AND TC.Grupo = 6";
     
        $where = implode (' and ', $where);

        if ($where) $where = $where;

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
        else {              $param['desde'] = "'1900/01/01'";      } 

        if ($rq->hasta) {   $param['hasta'] = $db->quote($rq->hasta);         }
        else {              $param['hasta'] = $db->quote(date('Y-m-d'));      }

        if ($rq->cliente) {

            $M_P = new Base_Model_DbTable_Personas;

            $arr = explode(',',$rq->cliente);
            
            foreach ($arr as $i => $li) {
                if (!is_numeric($li)) {
                    unset($arr[$i]);
                } else {
                    $textoClientes .= $separador . $M_P->getDescripcionEmpresa($arr[$i]);
                    $separador = ", ";
                }
                                    
            }
            $cliente = implode(',',$arr);
            if ($cliente) {
                $param['cliente']   = $cliente;
                $textoClientes      = " de los clientes ". $textoClientes;
            }
        }


        $param['cabecera']  = $db->quote($rq->cabecera, 'INTEGER'); 

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
		
        
        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        $textoPeriodo = "";
        if ($rq->desde)   {     $textoPeriodo .= " desde ".date('d/m/Y',strtotime($rq->desde));         } 
        if ($rq->hasta)   {     $textoPeriodo .= " hasta ".date('d/m/Y',strtotime($rq->hasta));         } 

        $where = " AND ".$this->buildWhere($param);
        $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Ventas_x_Clientes.rptdesign';
        // Divido entre texto y texto2 para que no de error el largo del nombre.
        $texto = "Estadistica de Ventas Por Clientes ".$textoPeriodo;
        $texto2 = $texto." ".$textoClientes;
        //Rad_Log::debug($where);

        $report->renderFromFile( $file, $formato, array(
            'WHERE' => $where,
            'TEXTO' => $texto2,
            'CABECERA' => $cabeceraUsar
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."___".date('YmdHis');
        
        $report->sendStream($NombreReporte);
    }
}