<?php
class Base_ReporteMontosDeVentasxLocalidadController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Montos de Ventas x Localidad';

    public function initWindow()
    {
        
    }

    protected function buildWhere($param)
    {
        $where = array();

        if ($param['Mes'])   $where[] = "Month(C.FechaEmision) = {$param['Mes']}";
        if ($param['Anio'])  $where[] = "Year(C.FechaEmision)  = {$param['Anio']}";
        
        $aLibrosIVA = explode(',', $param['LibroIVA']);
        foreach ($aLibrosIVA as $i => $li) {
            if (!ctype_digit($li))
                unset($aLibrosIVA[$i]);
        }
                
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

        $param['Mes']  = $db->quote($rq->mes, 'INTEGER');
        $param['Anio'] = $db->quote($rq->anio, 'INTEGER');
        $param['LibroIVA'] = $db->quote($rq->libroiva, 'INTEGER');
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

   
        $where = " WHERE ".$this->buildWhere($param);
        $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Ventas_x_Localidad_Montos.rptdesign';
        $texto = "Montos de Ventas Por Localidad periodo {$param['Mes']} / {$param['Anio']}";


        $report->renderFromFile( $file, $formato, array(
            'WHERE' => $where,
            'TEXTO' => $texto,
            'CABECERA' => $cabeceraUsar
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."___".date('YmdHis');
        
        $report->sendStream($NombreReporte);
    }
}