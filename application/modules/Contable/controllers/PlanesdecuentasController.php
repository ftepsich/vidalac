<?php

/**
 * Contable_Reporteplandecuentacontroller
 *
 *
 * @class Contable_reporteplandecuentaController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ReportePlanDeCuentaController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Plan de Cuenta';

    public function initWindow()
    {
        
    }

    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $report = new Rad_BirtEngine();

        $rq = $this->getRequest();
     
        $modelo          = ($rq->modelo) ? $rq->modelo : 1;
        $fechaDesde      = ($rq->fechadesde) ? $rq->fechadesde : '';
        $fechaHasta      = ($rq->fechahasta) ? $rq->fechahasta : '';
        $idLibroIVADesde = ($rq->libroivadesde) ? $rq->libroivadesde : 0;
        $idLibroIVAHasta = ($rq->libroivahasta) ? $rq->libroivahasta : 0;
        $cuenta          = ($rq->cuenta) ? $rq->cuenta : 0;
        $grupo           = ($rq->grupo)  ? $rq->grupo : 0;
        $formato         = ($rq->formato) ? $rq->formato : 'pdf';
        $whereQuery1     = " ";
        $whereQuery2     = " ";
        $selectQuery1    = " ";
        $selectQuery2    = " ";
        $selectQuery3    = " ";
        $selectQuery4    = " ";
        switch ($modelo) {
            case 1:
                $texto = "Reporte Plan de Cuenta General";   
                $path  = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaGeneral.rptdesign";
                $whereQuery1 = " WHERE PlanDeCuenta_Id IS NOT NULL ";
                break;
            case 2:
                $texto = "Reporte Plan de Cuenta Detallado";   
                $path  = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaDetallado.rptdesign";
                $report->setParameter('idCuenta', $cuenta, 'Int');
                $whereQuery1 = " WHERE PlanDeCuenta_Id = ".$cuenta; 
                $whereQuery2 = $whereQuery1;
                break;
            case 3:
                $texto = "Reporte Plan de Cuenta General por Grupo ";   
                $path  = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaGeneralGrupo.rptdesign";
                $report->setParameter('idGrupo', $grupo, 'Int');
                $whereQuery1 = " WHERE PlanDeCuenta_Id IS NOT NULL AND Grupo = ".$grupo;
                break;
            case 4:
                $texto = "Reporte Plan de Cuenta Detallado por Grupo ";   
                $path  = APPLICATION_PATH . "/../birt/Reports/Reporte_PlanDeCuentaDetalladoGrupo.rptdesign";
                $report->setParameter('idGrupo', $grupo, 'Int');
                $whereQuery1 = " WHERE Grupo = ".$grupo;
                $whereQuery2 = $whereQuery1;
                break;
        }

        if ( $fechaDesde <> '' ) {
            $report->setParameter('fechaDesde', $fechaDesde, 'String');
            switch ($modelo) {
               case 1:
               case 3:
                   $selectQuery1 .= " Comprobante_FechaEmision < '".$fechaDesde."'";
                   break;
               case 2:    
               case 4:    
                   $selectQuery1 .= " Comprobante_FechaEmision < '".$fechaDesde."'";
                   $whereQuery2 .= " AND Comprobante_FechaEmision >= '".$fechaDesde."'";
                   break;
            } 
        }
        if ( $fechaHasta <> '' ) {
            $report->setParameter('fechaHasta', $fechaHasta, 'String');
            switch ($modelo) {
               case 1:
               case 3:
                   $selectQuery2 .= " Comprobante_FechaEmision <= '".$fechaHasta."'";
                   $whereQuery1  .= " AND Comprobante_FechaEmision <= '".$fechaHasta."'";
                   break;
               case 2:    
               case 4:    
                   $selectQuery2 .= " Comprobante_FechaEmision <= '".$fechaHasta."'";
                   $whereQuery1  .= " AND Comprobante_FechaEmision <= '".$fechaHasta."'";
                   $whereQuery2  .= " AND Comprobante_FechaEmision <= '".$fechaHasta."'";
                   break;
            }
        }
        if ( $idLibroIVADesde <> 0 ) {
            $report->setParameter('idLibroIVADesde', $idLibroIVADesde, 'Int');
            switch ($modelo) {
               case 1:
               case 3:
                   $selectQuery1 .= " Comprobante_LibroIva < ".$idLibroIVADesde;
                   break;
               case 2:    
               case 4:    
                   $selectQuery1 .= " Comprobante_LibroIva < ".$idLibroIVADesde;
                   $whereQuery2 .= " AND Comprobante_LibroIva >= ".$idLibroIVADesde; 
                   break;
            }
        }
        if ( $idLibroIVAHasta <> 0 ) {
            $report->setParameter('idLibroIVAHasta', $idLibroIVAHasta, 'Int');
            switch ($modelo) {
               case 1:
               case 3:
                   $selectQuery2 .= " Comprobante_LibroIva <= ".$idLibroIVAHasta; 
                   $whereQuery1  .= " AND Comprobante_LibroIva <= ".$idLibroIVAHasta;
                   break;
               case 2:    
               case 4:    
                   $selectQuery2 .= " Comprobante_LibroIva <= ".$idLibroIVAHasta; 
                   $whereQuery1  .= " AND Comprobante_LibroIva <= ".$idLibroIVAHasta;
                   $whereQuery2  .= " AND Comprobante_LibroIva <= ".$idLibroIVAHasta;
                   break;
            }
        }
        $report->renderFromFile($path, $formato, array(
            'TEXTO' => $texto,
            'SELECTQUERY1' => $selectQuery1,
            'SELECTQUERY2' => $selectQuery2,
            'SELECTQUERY3' => $selectQuery3,
            'SELECTQUERY4' => $selectQuery4,
            'WHEREQUERY1'  => $whereQuery1,
            'WHEREQUERY2'  => $whereQuery2
        ));
        Rad_Log::debug( $selectQuery1 );
        Rad_Log::debug( $selectQuery2 );
        Rad_Log::debug( $whereQuery1 );
        Rad_Log::debug( $whereQuery2 );
        $nombreRep      = str_replace(  array(" ","/"), array("_","-") , $texto);
        $NombreReporte  = "Reporte_".$nombreRep."_".date('YmdHis');        
        $report->sendStream($NombreReporte);
    }
}
