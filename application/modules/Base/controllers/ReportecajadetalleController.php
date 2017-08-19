<?php
class Base_ReporteCajaDetalleController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Caja Detalle';

    public function initWindow()
    {
        
    }

    protected function buildWhere($param)
    {
        $where = array();
        if ($param['Mes'])   $where[] = "Month(Cm.Fecha) = {$param['Mes']}";
        if ($param['Anio'])   $where[] = "Year(Cm.Fecha) = {$param['Anio']}";
        
        $aLibrosIVA = explode(',', $param['LibroIVA']);
        foreach ($aLibrosIVA as $i => $li) {
            if (!ctype_digit($li))
                unset($aLibrosIVA[$i]);
        }
        //Rad_Log::debug($aLibrosIVA);
                
        //$where[] = "Anulado = 0 AND Cerrado = 1 AND TC.Grupo = 6";
     
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
        //$param['LibroIVA'] = $rq->libroiva;
		
    
        $formato = ($rq->formato) ? $rq->formato : 'pdf';

        $file = APPLICATION_PATH . '/../birt/Reports/ListadoDeCajaMovimientos.rptdesign';
   
        $where = " WHERE ".$this->buildWhere($param);

        $report->renderFromFile($file, $formato, array(
            'WHERE' => $where,
            'TEXTO' => "Listado Movimiento de Caja {$param['Mes']} de {$param['Anio']}"
        ));
        $report->sendStream();
    }
}
