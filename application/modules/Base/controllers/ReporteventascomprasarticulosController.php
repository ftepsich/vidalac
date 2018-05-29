<?php

/**
 * Base_ReporteVentasComprasArticulosController
 *
 * Reporte con filtros de Ventas o Compras en cantidades por articulo
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_ReporteVentasComprasArticulosController
 * @extends Rad_Window_Controller_Action
 */
class Base_ReporteVentasComprasArticulosController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte de Cantidades de Ventas o Compras';

    public function initWindow()
    {
    }

    protected function buildWhere($param)
    {
        $where = array();
        if ($param['desde'])        $where[] = "C.FechaEmision >= {$param['desde']}";
        if ($param['hasta'])        $where[] = "C.FechaEmision <= {$param['hasta']}";
        if ($param['persona'])      $where[] = "P.Id = {$param['persona']}";
        if ($param['tipo'])         $where[] = "LD.TipoDeLibro = {$param['tipo']}";
        if ($param['articulo'])     $where[] = "A.Id = {$param['articulo']}";
        if ($param['marcas'])        $where[] = "A.Marca IN ({$param['marcas']})";
        if ($param['subGrupos'])     $where[] = "A.ArticuloSubGrupo IN ({$param['subGrupos']})";

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

        $separador = '';
        if ($rq->marcas) {

            $M_M = new Base_Model_DbTable_Marcas;

            $arr = explode(',',$rq->marcas);

            foreach ($arr as $i => $li) {
                if (!is_numeric($li)) {
                    unset($arr[$i]);
                }
                /*
                else {
                    $textoMarcas .= $separador . $M_M->getNombre($arr[$i]);
                    $separador = ", ";
                }
                */
            }
            $Marcas = implode(',',$arr);
            if ($Marcas) {
                $param['marcas']   = $Marcas;
                // $textoMarcas     = " de las Marcas ". $textoMarcas;
            }
        }

        $separador = '';
        if ($rq->subGrupos) {

            $M_M = new Base_Model_DbTable_ArticulosSubGrupos;

            $arr = explode(',',$rq->subGrupos);

            foreach ($arr as $i => $li) {
                if (!is_numeric($li)) {
                    unset($arr[$i]);
                }
                /*
                else {
                    $textoSubGrupos .= $separador . $M_M->getNombre($arr[$i]);
                    $separador = ", ";
                }
                */
            }
            $SubGrupos = implode(',',$arr);
            if ($SubGrupos) {
                $param['subGrupos']   = $SubGrupos;
                // $textoSubGrupos     = " de las Marcas ". $textoSubGrupos;
            }
        }

        if ($rq->desde) {   $param['desde'] = $db->quote($rq->desde);         }
        else {              $param['desde'] = "'1900/01/01'";      }

        if ($rq->hasta) {   $param['hasta'] = $db->quote($rq->hasta);         }
        else {              $param['hasta'] = $db->quote(date('Y-m-d'));      }

        //$param['desde']    = $db->quote($rq->desde);
        //$param['hasta']    = $db->quote($rq->hasta);
        $param['persona']  = $db->quote($rq->persona , 'INTEGER');
        $param['tipo']     = $db->quote($rq->tipo, 'INTEGER');
        $param['reporte']  = $db->quote($rq->reporte, 'INTEGER');
        $param['articulo'] = $db->quote($rq->articulo, 'INTEGER');
        $param['cabecera'] = $db->quote($rq->cabecera, 'INTEGER');

        if ($param['tipo']) {
            switch ($param['tipo']) {
                case 1: $texto = 'Compras por articulo'; break;
                case 2: $texto = 'Ventas por articulo'; break;
            }
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
        } else { $cabeceraUsar = 'CabeceraInterna'; }

        // ojo hay que usar el $rq->fecha ya que sino falla por el cuoteo
        if ($rq->desde)   {     $texto .= " desde ".date('d/m/Y',strtotime($rq->desde));         }
        if ($rq->hasta)   {     $texto .= " hasta ".date('d/m/Y',strtotime($rq->hasta));         }

        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        //$formato = 'html';
        if ($param['reporte'] == 1) {
            // Detallado
            $file = APPLICATION_PATH . '/../birt/Reports/Rep_Ventas_y_Compras_porArticulos_Detallado.rptdesign';
            $orden = 'C.FechaEmision, P.RazonSocial, A.Codigo ';
        } else {
            $file = APPLICATION_PATH . '/../birt/Reports/Rep_Ventas_y_Compras_porArticulos_Agrupado.rptdesign';
            $orden = 'P.RazonSocial, A.Codigo ';
            $texto = $texto . ' agrupado';
        }


        $where = $this->buildWhere($param);

        //die($where);

        Rad_Log::debug($where);

		$report->renderFromFile($file, $formato, array(
            'WHERE' => " WHERE ".$where,
            'TEXTO' =>  $texto,
            'ORDEN' =>  " ORDER BY ".$orden,
            'CABECERA' => $cabeceraUsar
        ));

        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."___".date('YmdHis');

        $report->sendStream($NombreReporte);
    }

}
