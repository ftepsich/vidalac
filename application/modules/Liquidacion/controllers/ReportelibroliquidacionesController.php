<?php
use Rad\Util\FileExport;
    /**
     * Class Liquidacion_ReporteLibroLiquidacionesController
     */
    class Liquidacion_ReporteLibroLiquidacionesController extends Rad_Window_Controller_Action
    {
        protected $title = 'Libro de Liquidaciones';

        public function initWindow()
        {
        }

        public function verreporteAction()
        {
            $this->_helper->viewRenderer->setNoRender(true);

            $report = new Rad_BirtEngine();
            $report->setParameter('Id', 2, 'Int');

            $rq = $this->getRequest();

            $db                     = Zend_Registry::get('db');

            $param['Liquidacion']   = $db->quote($rq->Liquidacion, 'INTEGER');
            $param['Modelo']        = $db->quote($rq->Modelo, 'INTEGER');
            $param['Fecha']         = $db->quote($rq->Fecha);

            // $param['Empresa'] = $db->fetchOne("Select Empresa from Liquidaciones where Id = {$param['Liquidacion']}");

            /* Recupero desc Liquidacion */
            $sql = "Select  TL.Descripcion as TipoLiq,
                            LP.Descripcion as Liquidacion,
                            -- E.Descripcion as Empresa,
                            if  (
                                (LP.Anio > 2015 || (LP.Anio = 2015 && LP.Valor > 2 )),
                                case E.Id 
                                    When 1 then 'SINDICATO DE CHOFERES DE CAMIONES O. y E.T.A.C.G.L. y S. de ENTRE RIOS'
                                    When 2 then 'ASOCIACION MUTUAL DE TRABAJADORES CAMIONEROS DE ENTRE RIOS'
                                    When 3 then 'OBRA SOCIAL DE CONDUCTORES CAMIONEROS Y PERSONAL DEL TRANSPORTE AUTOMOTOR DE CARGAS - FILIAL ENTRE RIOS'
                                End,
                                E.Descripcion
                                ) AS Empresa,                            
                            E.Id as EmpresaId,
                            L.Cerrada as Cerrada,
                            LP.Anio as Anio,
                            LP.Valor as Mes
                    from    Liquidaciones L
                    inner join TiposDeLiquidaciones TL on TL.Id = L.TipoDeLiquidacion
                    inner join LiquidacionesPeriodos LP on LP.Id = L.LiquidacionPeriodo
                    inner join Empresas E on E.Id = L.Empresa
                    where   L.Id = {$param['Liquidacion']}";
            $L   = $db->fetchRow($sql);
            if (!$L) throw new Rad_Db_Table_Exception('Liquidacion inexistente.');

            // Pedido de Juli 05/09/2014 que no muestre el texto normal
            if ($L['TipoLiq'] == 'Normal') {
                $tipoLiq = '';
            } else {
                $tipoLiq = $L['TipoLiq'].' ';
            }

            $textoLiquidacion   = $tipoLiq.$L['Liquidacion'];
            
            If ($L['Anio'] > 2015 || ($L['Anio'] == 2015 && $L['Mes'] > 2 )) {

                if ($L['EmpresaId'] == 1) $textoEmpresa = "SINDICATO DE CHOFERES DE CAMIONES O. y E.T.A.C.G.L. y S. de ENTRE RIOS";
                if ($L['EmpresaId'] == 2) $textoEmpresa = "ASOCIACION MUTUAL DE TRABAJADORES CAMIONEROS DE ENTRE RIOS";
                if ($L['EmpresaId'] == 3) $textoEmpresa = "OBRA SOCIAL DE CONDUCTORES CAMIONEROS Y PERSONAL DEL TRANSPORTE AUTOMOTOR DE CARGAS - FILIAL ENTRE RIOS";
            } else {
                $textoEmpresa       = $L['Empresa'];
            }

            $pre2015_02 = '';
            if ($L['Anio'] < 2015 || ($L['Anio'] == 2015 && $L['Mes'] < 2)) {
                $pre2015_02 = '_pre2015_02';
            }

            switch ($param['Modelo']) {
                case 1: case 7: case 9: case 10:
                    // --------------------------------------------------------------------------------------
                    // Libro de Liquidaciones
                    // --------------------------------------------------------------------------------------

                    $paraPago = '';
                    if ($param['Modelo'] == 7) $paraPago = '_paraPago';

                    $individual = '';
                    if ($param['Modelo'] == 9) $individual = '_Individual';

                    $normalyEspecial = '';
                    if ($param['Modelo'] == 10) $normalyEspecial = '_NormalyEspecial';

                    $report->setParameter('idLiquidacion', $param['Liquidacion'], 'Int');
                    $report->setParameter('idEmpresa', $L['EmpresaId'], 'Int');

                    //Rad_Log::debug();
                    //$textoLiquidacion   = $L['Liquidacion'];
                    //$textoEmpresa       = $L['Empresa'];

                    /* Recupero desc Empresa */
                    /*
                    $sql = "Select Descripcion as Empresa from Empresas where Id = {$param['Empresa']}";
                    $E   = $db->fetchRow($sql);
                    if (!$E) throw new Rad_Db_Table_Exception('Empresa inexistente.');
                    $textoEmpresa = $E['Empresa'];
                    */
                    $texto = "LibroDeLiquidaciones-$textoEmpresa-$textoLiquidacion"; 

                    $formato = ($rq->formato) ? $rq->formato : 'pdf';
                    //$formato = 'html';

                    $file = APPLICATION_PATH . "/../birt/Reports/Liq_LibroDeLiquidacion".$paraPago.$individual.$normalyEspecial.$pre2015_02.".rptdesign";

                    $report->renderFromFile($file, $formato, array(
                        'EMPRESA'      => $textoEmpresa,
                        'LIQUIDACION'  => $textoLiquidacion
                    ));

                    //$nombreRep     = str_replace(array(" ", "/"), array("_", "-"), $texto);
                    //$NombreReporte = $nombreRep . "___" . date('YmdHis');
                    $NombreReporte = $texto . "___" . date('YmdHis');

                    $report->sendStream($NombreReporte);

                    break;
                case 2: case 4:
                    // --------------------------------------------------------------------------------------
                    // Exportador 931
                    // --------------------------------------------------------------------------------------

                    switch ($param['Modelo']) {
                        case 2:
                            $sql = "CALL AFIP_exportador_931v34_35_36({$param['Liquidacion']});";
                            break;

                        case 4:
                            $sql = "CALL AFIP_exportador_931v365({$param['Liquidacion']});";
                            break;
                    }

                    $datos = $db->fetchAll($sql);

                    if (!count($datos)) throw new Rad_Db_Table_Exception('No se encuentra el archivo 931 a exportar o no tiene registros.');

                    $formatoSalida = function($e){
                        return str_replace('.',',',$e);
                    };

                    $e = new FileExport(FileExport::MODE_SEPARATOR);
                    $e->setSeparator("");
                    $e->setLineEnd("\r\n");
                    $e->setLineFormat( array(
                                            'Cuil'                  => array('format' => $formatoSalida),
                                            'ApeNom'                => array('format' => $formatoSalida),
                                            'Conyuge'               => array('format' => $formatoSalida),
                                            'CantHijo'              => array('format' => $formatoSalida),
                                            'CodSit'                => array('format' => $formatoSalida),
                                            'CodCon'                => array('format' => $formatoSalida),
                                            'CodAct'                => array('format' => $formatoSalida),
                                            'CodZona'               => array('format' => $formatoSalida),
                                            'PorAporAdicSS'         => array('format' => $formatoSalida),
                                            'CodModCont'            => array('format' => $formatoSalida),
                                            'CodOS'                 => array('format' => $formatoSalida),
                                            'CantAdh'               => array('format' => $formatoSalida),
                                            'RemTot'                => array('format' => $formatoSalida),
                                            'RemImp1'               => array('format' => $formatoSalida),
                                            'AsigFamPag'            => array('format' => $formatoSalida),
                                            'ImpAportVol'           => array('format' => $formatoSalida),
                                            'ImpAdiOS'              => array('format' => $formatoSalida),
                                            'ImpExcAportSS'         => array('format' => $formatoSalida),
                                            'ImpExcAportOS'         => array('format' => $formatoSalida),
                                            'ProvLoc'               => array('format' => $formatoSalida),
                                            'RemImp2'               => array('format' => $formatoSalida),
                                            'RemImp3'               => array('format' => $formatoSalida),
                                            'RemImp4'               => array('format' => $formatoSalida),
                                            'CodSin'                => array('format' => $formatoSalida),
                                            'MarCorrRed'            => array('format' => $formatoSalida),
                                            'CapRecLRT'             => array('format' => $formatoSalida),
                                            'TipoEmp'               => array('format' => $formatoSalida),
                                            'AporAdiOS'             => array('format' => $formatoSalida),
                                            'Regimen'               => array('format' => $formatoSalida),
                                            'SitRev1'               => array('format' => $formatoSalida),
                                            'DiaIniSitRev1'         => array('format' => $formatoSalida),
                                            'SitRev2'               => array('format' => $formatoSalida),
                                            'DiaIniSitRev2'         => array('format' => $formatoSalida),
                                            'SitRev3'               => array('format' => $formatoSalida),
                                            'DiaIniSitRev3'         => array('format' => $formatoSalida),
                                            'SuelAdic'              => array('format' => $formatoSalida),
                                            'SAC'                   => array('format' => $formatoSalida),
                                            'HorasExtras'           => array('format' => $formatoSalida),
                                            'ZonaDesf'              => array('format' => $formatoSalida),
                                            'Vacaciones'            => array('format' => $formatoSalida),
                                            'CantDiasTrab'          => array('format' => $formatoSalida),
                                            'RemImp5'               => array('format' => $formatoSalida),
                                            'TrabConv'              => array('format' => $formatoSalida),
                                            'RemImp6'               => array('format' => $formatoSalida),
                                            'TipoOper'              => array('format' => $formatoSalida),
                                            'Adicionales'           => array('format' => $formatoSalida),
                                            'Premios'               => array('format' => $formatoSalida),
                                            'RemDec78805RemImp8'    => array('format' => $formatoSalida),
                                            'RemImp7'               => array('format' => $formatoSalida),
                                            'CantHorasExtras'       => array('format' => $formatoSalida),
                                            'ConcNoRem'             => array('format' => $formatoSalida),
                                            'Maternidad'            => array('format' => $formatoSalida),
                                            'RectRem'               => array('format' => $formatoSalida),
                                            'RemImp9'               => array('format' => $formatoSalida),
                                            'ContTarDif'            => array('format' => $formatoSalida),
                                            'HorasTrab'             => array('format' => $formatoSalida),
                                            'SegColVidaOblig'       => array('format' => $formatoSalida)
                        )
                    );
                    $e->addAll($datos);

                    $contenido = $e->getContent();

                    if($param['Empresa'] == 1) $empresa = "SINDICATO";
                    if($param['Empresa'] == 2) $empresa = "MUTUAL";
                    if($param['Empresa'] == 3) $empresa = "OBRA_SOCIAL";

                    $sql = "    SELECT  CONCAT(  CASE WHEN valor < 10 THEN LPAD(valor,2,'0') ELSE valor END,'-',Anio) AS fecha
                                FROM    LiquidacionesPeriodos
                                where   Id in (Select LiquidacionPeriodo from Liquidaciones where Id =".$param['Liquidacion'].")";

                    $fecha = $db->fetchOne($sql);
                    $Nombre = "INFORME_931_(" . $empresa . ")_" . $fecha . "__" . date('YmdHis') . ".txt";

                    header("Content-disposition: attachment; filename=$Nombre");
                    header("Content-type: text/txt");
                    echo $contenido;


                    break;
                case 5:
                    // --------------------------------------------------------------------------------------
                    // Exportador sicore
                    // --------------------------------------------------------------------------------------
                    $fecheEmision  = $param['Fecha'];

                    if(!$fecheEmision ) throw new Rad_Db_Table_Exception('Debe ingresar la fecha de emision.');

                    $dia  = substr($fecheEmision,9,2);
                    $mes  = substr($fecheEmision,6,2);
                    $anio = substr($fecheEmision,1,4);

                    $fecheEmision = $dia.'/'.$mes.'/'.$anio;
                    Rad_Log::debug($fecheEmision);

                    $sql = "CALL AFIP_exportador_Sicore_v_8({$param['Liquidacion']},'{$fecheEmision}');";

                    $datos = $db->fetchAll($sql);

                    if (!count($datos)) throw new Rad_Db_Table_Exception($sql.'No se encuentra el archivo SICORE a exportar o no tiene registros.');

                    $formatoSalida = function($e){
                        return str_replace(',','.',$e);
                    };

                    $e = new FileExport(FileExport::MODE_SEPARATOR);
                    $e->setSeparator("");
                    $e->setLineEnd("\r\n");
                    $e->setLineFormat( array(
                                            'CodComprobante'        => array('format' => $formatoSalida),
                                            'FechaEmision'          => array('format' => $formatoSalida),
                                            'NumeroRecibo'          => array('format' => $formatoSalida),
                                            'Importe1'              => array('format' => $formatoSalida),
                                            'CodImpuesto'           => array('format' => $formatoSalida),
                                            'CodRegimen'            => array('format' => $formatoSalida),
                                            'CodOperacion'          => array('format' => $formatoSalida),
                                            'BaseCalculo'           => array('format' => $formatoSalida),
                                            'FechaEmision2'         => array('format' => $formatoSalida),
                                            'CodCondicion'          => array('format' => $formatoSalida),
                                            'RetPracticada'         => array('format' => $formatoSalida),
                                            'Importe2'              => array('format' => $formatoSalida),
                                            'PorcExclusion'         => array('format' => $formatoSalida),
                                            'FechaEmisionBoletin'   => array('format' => $formatoSalida),
                                            'Cod86'                 => array('format' => $formatoSalida),
                                            'Ceros'                 => array('format' => $formatoSalida),
                                            'Cuit0'                 => array('format' => $formatoSalida),
                                            'MasCeros'              => array('format' => $formatoSalida)
                        )
                    );
                    $e->addAll($datos);

                    $contenido = $e->getContent();

                    if($param['Empresa'] == 1) $empresa = "SINDICATO";
                    if($param['Empresa'] == 2) $empresa = "MUTUAL";
                    if($param['Empresa'] == 3) $empresa = "OBRA_SOCIAL";

                    $sql = "    SELECT  CONCAT(  CASE WHEN valor < 10 THEN LPAD(valor,2,'0') ELSE valor END,'-',Anio) AS fecha
                                FROM    LiquidacionesPeriodos
                                where   Id in (Select LiquidacionPeriodo from Liquidaciones where Id =".$param['Liquidacion'].")";

                    $fecha = $db->fetchOne($sql);
                    $Nombre = "INFORME_SICORE_(" . $empresa . ")_" . $fecha . "__" . date('YmdHis') . ".txt";

                    header("Content-disposition: attachment; filename=$Nombre");
                    header("Content-type: text/txt");
                    echo $contenido;


                    break;
                case 3:
                    // --------------------------------------------------------------------------------------
                    // Recibos de Sueldo
                    // --------------------------------------------------------------------------------------

                    // Controlo que este cerrada la liq
                    // if (!$L['Cerrada']) throw new Rad_Db_Table_Exception('La Liquidacion no se encuentra cerrada, debe cerrar la liquidación para ejecutar la tarea solicitada.');

                    $report->setParameter('idLiquidacion', $param['Liquidacion'], 'Int');

                    $where = "LR.Liquidacion = {$param['Liquidacion']}";

                    //$formato = ($rq->formato) ? $rq->formato : 'pdf';
                    $formato = 'pdf';

                    $file = APPLICATION_PATH . "/../birt/Reports/ReciboSueldo".$pre2015_02.".rptdesign";

                    $report->renderFromFile($file, $formato, array(
                        'WHERE'        => $where
                    ));
                    $nombreRep     = $L['Empresa'].'_'.$L['Liquidacion'];
                    $nombreRep     = str_replace(array(" ", "/"), array("_", "-"), $nombreRep);
                    $NombreReporte = $nombreRep . "___" . date('YmdHis');

                    $report->sendStream($NombreReporte);
                    break;
                case 6:
                    // --------------------------------------------------------------------------------------
                    // Resumen totales
                    // --------------------------------------------------------------------------------------

                    // Controlo que este cerrada la liq
                    // if (!$L['Cerrada']) throw new Rad_Db_Table_Exception('La Liquidacion no se encuentra cerrada, debe cerrar la liquidación para ejecutar la tarea solicitada.');

                    $report->setParameter('idLiquidacion', $param['Liquidacion'], 'Int');

                    //$where = "LR.Liquidacion = {$param['Liquidacion']}";

                    //$formato = ($rq->formato) ? $rq->formato : 'pdf';
                    $formato = ($rq->formato) ? $rq->formato : 'pdf';

                    $file = APPLICATION_PATH . '/../birt/Reports/Rep_Liquidaciones_TotalesPorCuit.rptdesign';

                    $texto = "Reporte de Totales por Cuit";

                    $report->renderFromFile($file, $formato, array(
                        'EMPRESA'      => $textoEmpresa,
                        'LIQUIDACION'  => $textoLiquidacion,
                        'TEXTO'        => $texto
                    ));
                    $nombreRep     = $L['Empresa'].'_'.$L['Liquidacion'];
                    $nombreRep     = str_replace(array(" ", "/"), array("_", "-"), $nombreRep);
                    $NombreReporte = $nombreRep . "___" . date('YmdHis');

                    $report->sendStream($NombreReporte);
                    break;
                case 8:
                    // --------------------------------------------------------------------------------------
                    // Resumen totales por Codigo
                    // --------------------------------------------------------------------------------------

                    // Controlo que este cerrada la liq
                    // if (!$L['Cerrada']) throw new Rad_Db_Table_Exception('La Liquidacion no se encuentra cerrada, debe cerrar la liquidación para ejecutar la tarea solicitada.');

                    $report->setParameter('idLiquidacion', $param['Liquidacion'], 'Int');

                    //$where = "LR.Liquidacion = {$param['Liquidacion']}";

                    //$formato = ($rq->formato) ? $rq->formato : 'pdf';
                    $formato = ($rq->formato) ? $rq->formato : 'pdf';

                    $file = APPLICATION_PATH . '/../birt/Reports/Rep_Liquidaciones_TotalesPorCodigo.rptdesign';

                    $texto = "Reporte de Totales por Codigo";

                    $report->renderFromFile($file, $formato, array(
                        'EMPRESA'      => $textoEmpresa,
                        'LIQUIDACION'  => $textoLiquidacion,
                        'TEXTO'        => $texto
                    ));
                    $nombreRep     = $L['Empresa'].'_'.$L['Liquidacion'];
                    $nombreRep     = str_replace(array(" ", "/"), array("_", "-"), $nombreRep);
                    $NombreReporte = $nombreRep . "___" . date('YmdHis');

                    $report->sendStream($NombreReporte);
                    break;                    
                default:
                    # code...
                    break;
            }
        }
    }