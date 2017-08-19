<?php
class Base_ReporteVentasxLocalidadController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Estadistico de Ventas x Localidad';

    public function initWindow()
    {
        
    }

    protected function buildWhere($param)
    {
        $where = array();
        
        if ($param['desde'])        $where[] = "C.FechaEmision >= {$param['desde']}";
        if ($param['hasta'])        $where[] = "C.FechaEmision <= {$param['hasta']}";
        if ($param['localidad'])    $where[] = "L.Id IN ({$param['localidad']})";
        if ($param['provincia'])    $where[] = "PR.Id IN ({$param['provincia']})";

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

        $separador = '';
        if ($rq->localidad) {

            $M_L = new Base_Model_DbTable_Localidades;

            $arr = explode(',',$rq->localidad);
            
            foreach ($arr as $i => $li) {
                if (!is_numeric($li)) {
                    unset($arr[$i]);
                } else {
                    $textoLocalidades .= $separador . $M_L->getNombre($arr[$i]);
                    $separador = ", ";
                }
                                    
            }
            $Localidades = implode(',',$arr);
            if ($Localidades) {
                $param['localidad']   = $Localidades;
                $textoLocalidades     = " de las Localidades ". $textoLocalidades;
            }
        }

        $separador = '';
        if ($rq->provincia) {

            $M_L = new Base_Model_DbTable_Provincias;

            $arr = explode(',',$rq->provincia);
            
            foreach ($arr as $i => $li) {
                if (!is_numeric($li)) {
                    unset($arr[$i]);
                } else {
                    $textoProvincias .= $separador . $M_L->getNombre($arr[$i]);
                    $separador = ", ";
                }
                                    
            }
            $Provincias = implode(',',$arr);
            if ($Provincias) {
                $param['provincia']   = $Provincias;
                $textoProvincias     = " de las Provincias ". $textoProvincias;
            }
        }

        $param['cabecera'] = $db->quote($rq->cabecera, 'INTEGER'); 

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
        if ($rq->desde)   {     $textoPeriodo .= " desde ".date('d/m/Y',strtotime($rq->desde));         } 
        if ($rq->hasta)   {     $textoPeriodo .= " hasta ".date('d/m/Y',strtotime($rq->hasta));         } 
   
        $where = " AND ".$this->buildWhere($param);
        $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Ventas_x_Localidad_2.rptdesign';
        $texto = "Estadistica de Ventas Por Localidad". $textoPeriodo;
        $texto2 = $texto.' '.$textoLocalidades.' '.$textoProvincias;

        Rad_Log::debug($where);

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