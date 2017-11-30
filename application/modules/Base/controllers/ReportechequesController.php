<?php
use Rad\Util\FileExport;
/**
 * Base_ReporteChequesController
 *
 * Reporte con filtros de Cheques
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarVendedoresController
 * @extends Rad_Window_Controller_Action
 */
class Base_ReporteChequesController extends Rad_Window_Controller_Action
{
    protected $title = 'Reporte Cheques Propios';
    public function initWindow()
    {
    
    }
    public function imprimirAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $rq     = $this->getRequest();
        $model  = new Base_Model_DbTable_ChequesPropios;
        $ids    = $rq->ids;
        $model->enviarAImpresora($ids);
    }
    protected function buildJoin($param) {
        $join = "";
        if ($param['ordenDePago']) 
            $join = " 
                LEFT JOIN ComprobantesDetalles CDOP    ON C.Id  = CDOP.Cheque
                LEFT JOIN Comprobantes COOP            ON COOP.Id = CDOP.Comprobante
            ";
        if ($param['recibo']) 
            $join = " 
                LEFT JOIN ComprobantesDetalles CDR     ON C.Id  = CDR.Cheque
                LEFT JOIN Comprobantes COR             ON COR.Id = CDR.Comprobante
            ";
        return $join;        
    }
    protected function buildOrder($param) 
    {
        $orden   = ' ';
        $sentido =  ( $param['ordenSentido'] ) ? ' desc ' : ' asc ';
        if ( $param['ordenCombo']) {
            switch ($param['ordenCombo']) {
                case 1: $orden .= ' order by C.FechaDeEmision '.$sentido;     break;
                case 2: $orden .= ' order by SB.Descripcion '.$sentido;       break;
                case 3: $orden .= ' order by C.Numero '.$sentido;             break;
                case 4: $orden .= ' order by C.FechaDeVencimiento '.$sentido; break;
                case 5: $orden .= ' order by P.RazonSocial '.$sentido;        break;
            }
        }
        return $orden;
    }
    protected function buildSQL($param)
    {
        $join       = $this->buildJoin($param);
        $where      = $this->buildWhere($param);
        $separador  = '|';
        $sql = "
                SELECT 
                    CAST(
                    CONCAT(
                    'Mes',                          '$separador',
                    'Tipo',                         '$separador',
                    'Estado',                       '$separador',
                    'Banco',                        '$separador',
                    'Cuenta Bancaria',              '$separador',
                    'CH. Serie',                    '$separador',
                    'CH. Numero',                   '$separador',
                    'Chequera',                     '$separador',
                    'Razon Social',                 '$separador',
                    'Paguese a',                    '$separador',
                    'Fecha Emision',                '$separador',
                    'Fecha Cobro',                  '$separador',
                    'Fecha Vencimiento',            '$separador',
                    'Monto',                        '$separador',
                    'Cobrado',                      '$separador',
                    'Impreso',                      '$separador',
                    'NoALaOrden',                   '$separador',
                    'Cruzado',                      '$separador',
                    'Cheque Manual',                '$separador',
                    'Cuenta Destino',               '$separador',
                    'Tercero Emisor',               '$separador',
                    'CUIT T. Emisor',               '$separador',
                    'Comprobantes',                 '$separador'
                    )
                    AS CHAR CHARACTER SET utf8)  COLLATE utf8_general_ci
                    as renglon
                UNION
                SELECT 
                    CAST(
                    CONCAT(
                    ifnull(DATE_FORMAT(C.FechaDeVencimiento, ' %m / %Y'),'') , '$separador',
                    ifnull(TE.Descripcion,'') ,                      '$separador',
                    ifnull(CE.Descripcion,'') ,                      '$separador',
                    ifnull(SB.Descripcion,'') ,                      '$separador',
                    ifnull(fCuentaBancaria(CH.CuentaBancaria),''),   '$separador',
                    ifnull(upper(C.Serie),'') ,                      '$separador',
                    ifnull(LPAD(CONVERT(C.Numero, CHAR(8)), 8, '0'),''), '$separador',
                    ifnull(CH.NumeroDeChequera,''),                  '$separador',
                    ifnull(P.RazonSocial,''),                        '$separador',
                    ifnull(C.PagueseA,''),                           '$separador',
                    ifnull(C.FechaDeEmision,''),                     '$separador',
                    ifnull(C.FechaDeCobro,''),                       '$separador',
                    ifnull(C.FechaDeVencimiento,''),                 '$separador',
                    ifnull(C.Monto,''),                              '$separador',
                    if(C.Cobrado = 1,'Si','No'),          '$separador',
                    if(C.Impreso = 1,'Si','No'),          '$separador',
                    if(C.NoALaOrden = 1,'Si','No'),       '$separador',
                    if(C.Cruzado = 1,'Si','No'),          '$separador',
                    if(C.ChequeManual = 1,'Si','No'),     '$separador',
                    ifnull(fCuentaBancaria(C.CuentaDeMovimiento),''),                 '$separador',
                    ifnull(C.TerceroEmisor,''),                      '$separador',
                    ifnull(C.CuitTerceroEmisor,''),                  '$separador',
                    ifnull(
                        (
                        SELECT  GROUP_CONCAT(distinct CAST(fNumeroCompleto(CD1.Comprobante,'G') AS CHAR) ORDER BY C1.FechaEmision asc)
                        FROM    Comprobantes C1
                        INNER JOIN ComprobantesDetalles CD1 on C1.Id = CD1.Comprobante
                        WHERE   CD1.Cheque = C.Id
                        )
                    ,'')
                    )
                    AS CHAR CHARACTER SET utf8)  COLLATE utf8_general_ci
                 as Ranglon
                FROM Cheques C
                LEFT JOIN BancosSucursales SB           ON SB.Id = C.BancoSucursal
                LEFT JOIN Personas P                    ON P.Id  = C.Persona
                LEFT JOIN ChequesEstados CE             ON CE.Id = C.ChequeEstado
                LEFT JOIN TiposDeCheques TCH            ON TCH.Id = C.TipoDeCheque
                LEFT JOIN TiposDeEmisoresDeCheques TE   ON TE.ID = C.TipoDeEmisorDeCheque
                LEFT JOIN Chequeras CH                  ON C.Chequera  = CH.Id
                $join
                WHERE C.ChequeEstado not in (1,5) and
                $where
            ";
        return $sql;
    }
    protected function buildWhere($param)
    {
        $where = array();
        // general
        if (trim($param['tipo']) == "'Propios'")  {
            $where[] = "C.TipoDeEmisorDeCheque = 1";
        } 
        else {
            $where[] = "C.TipoDeEmisorDeCheque <> 1";
        }        
        if ($param['bancoSucursal'])     $where[] = "C.BancoSucursal = {$param['bancoSucursal']}";
        if ($param['chequera'])          $where[] = "C.Chequera = {$param['chequera']}";
        if ($param['numeroDesde'])       $where[] = "C.Numero >= {$param['numeroDesde']}";
        if ($param['numeroHasta'])       $where[] = "C.Numero <= {$param['numeroHasta']}";
		if ($param['razonSocial'])       $where[] = "C.Persona = {$param['razonSocial']}";
		if ($param['pagueseA'])			 $where[] = "C.PagueseA like {$param['pagueseA']}";
		if ($param['noAlaOrden'] && $param['noAlaOrden'] != 3)  {
            $noAlaOrden = ($param['noAlaOrden'] == 2 ) ? 0 : $param['noAlaOrden'];
            $where[]    = "ifnull(C.noAlaOrden,0) = {$noAlaOrden}";
        }
        if ($param['cruzado'] && $param['cruzado'] != 3) {
            $cruzado = ($param['cruzado'] == 2 ) ? 0 : $param['cruzado'];
            $where[] = "ifnull(C.Cruzado,0) = {$cruzado}";  
        } 
	    if ($param['montoDesde'])        $where[] = "C.Monto >= {$param['montoDesde']}";
	    if ($param['montoHasta'])        $where[] = "C.Monto <= {$param['montoHasta']}";
	    if ($param['ordenDePago'])       $where[] = "COOP.Numero = {$param['ordenDePago']}";
	    if ($param['recibo'])            $where[] = "COR.Numero = {$param['recibo']}";
        if ($param['cuentaBancariaPropia'])       $where[] = "CH.CuentaBancaria = {$param['cuentaBancariaPropia']}";
        if ($param['cuentaBancariaDestino'])      $where[] = "C.CuentaDeMovimiento = {$param['cuentaBancariaDestino']}";
        if ($param['terceroEmisor'])     $where[] = "C.TerceroEmisor like {$param['terceroEmisor']}";
        if ($param['cuitTerceroEmisor']) $where[] = "C.CuitTerceroEmisor like {$param['cuitTerceroEmisor']}";
        // Fechas
        if ($param['emisionDesde'] !== '')      $where[] = "C.FechaDeEmision >= {$param['emisionDesde']}";
        if ($param['emisionHasta'] !== '')      $where[] = "C.FechaDeEmision <= {$param['emisionHasta']}";         
        if ($param['vencimientoDesde'] !== '')  $where[] = "C.FechaDeVencimiento >= {$param['vencimientoDesde']}";
        if ($param['vencimientoHasta'] !== '')  $where[] = "C.FechaDeVencimiento <= {$param['vencimientoHasta']}";
	// Se debe suprimir la condición de búsqueda por rango de "Fecha de Cobro" es seleccionado el estado Cobrado = "NO".
        if ($param['cobrado'] && $param['cobrado'] != 2) {
           if ($param['cobroDesde'] !== '')        $where[] = "C.FechaDeCobro >= {$param['cobroDesde']}";
           if ($param['cobroHasta'] !== '')        $where[] = "C.FechaDeCobro <= {$param['cobroHasta']}";
        }
        // Estado
        if ($param['cobrado'] && $param['cobrado'] != 3) {
            $cobrado    = ($param['cobrado'] == 2 ) ? 0 : $param['cobrado'];
            $where[]    = "ifnull(C.Cobrado,0) = {$cobrado}";
        }
        if ($param['impreso'] && $param['impreso'] != 3) {
            $impreso = ($param['impreso'] == 2 ) ? 0 : $param['impreso'];
            $where[] = "ifnull(C.Impreso,0) = {$impreso}";  
        } 
        
        if ($param['estado'] )       	 $where[] = "C.ChequeEstado = {$param['estado']}";
       
        $where = implode (' and ',$where);
        //throw new Rad_Db_Table_Exception($where);
        return $where;
    }
    protected function buildParametros($rq,$db) {
        $param = array();
        // General
        $param['tipo']                  = ($rq->tipo)                   ? $db->quote($rq->tipo)                      : "";
        $param['bancoSucursal']         = ($rq->bancoSucursal)          ? $db->quote($rq->bancoSucursal, 'INTEGER')  : "";
        $param['chequera']              = ($rq->chequera)               ? $db->quote($rq->chequera, 'INTEGER')       : "";
        $param['numeroDesde']           = ($rq->numeroDesde)            ? $db->quote($rq->numeroDesde, 'INTEGER')    : "";
        $param['numeroHasta']           = ($rq->numeroHasta)            ? $db->quote($rq->numeroHasta, 'INTEGER')    : "";
        $param['razonSocial']           = ($rq->razonSocial)            ? $db->quote($rq->razonSocial, 'INTEGER')    : "";
        $param['pagueseA']              = ($rq->pagueseA)               ? $db->quote("%".$rq->pagueseA."%")          : "";
        $param['noAlaOrden']            = ($rq->noAlaOrden)             ? $db->quote($rq->noAlaOrden, 'INTEGER')     : "";
        $param['cruzado']               = ($rq->cruzado)                ? $db->quote($rq->cruzado, 'INTEGER')        : "";
        $param['montoDesde']            = ($rq->montoDesde)             ? $db->quote($rq->montoDesde, 'INTEGER')     : "";
        $param['montoHasta']            = ($rq->montoHasta)             ? $db->quote($rq->montoHasta, 'INTEGER')     : "";
        $param['ordenDePago']           = ($rq->ordenDePago)            ? $db->quote($rq->ordenDePago, 'INTEGER')    : "";
        $param['terceroEmisor']         = ($rq->terceroEmisor)          ? $db->quote("%".$rq->terceroEmisor."%")     : "";
        $param['cuitTerceroEmisor']     = ($rq->cuitTerceroEmisor)      ? $db->quote("%".$rq->cuitTerceroEmisor."%") : "";
        $param['ordenDePago']           = ($rq->ordenDePago)            ? $db->quote($rq->ordenDePago, 'INTEGER')    : "";
        $param['recibo']                = ($rq->recibo)                 ? $db->quote($rq->recibo, 'INTEGER')         : "";
        $param['cuentaBancariaPropia']  = ($rq->cuentaBancariaPropia)   ? $db->quote($rq->cuentaBancariaPropia, 'INTEGER')  : "";
        $param['cuentaBancariaDestino'] = ($rq->cuentaBancariaDestino)  ? $db->quote($rq->cuentaBancariaDestino, 'INTEGER') : "";
        // Fechas
        $param['emisionDesde']     = (trim($rq->emisionDesde) !== '' && $rq->emisionDesde     !== 'undefined' ) ? $db->quote($rq->emisionDesde)             : ""; 
        $param['emisionHasta']     = (trim($rq->emisionHasta) !== '' && $rq->emisionHasta     !== 'undefined' ) ? $db->quote($rq->emisionHasta)             : "";
        $param['vencimientoDesde'] = (trim($rq->vencimientoDesde) !== '' && $rq->vencimientoDesde !== 'undefined' ) ? $db->quote($rq->vencimientoDesde)         : ""; 
        $param['vencimientoHasta'] = (trim($rq->vencimientoHasta) !== '' && $rq->vencimientoHasta !== 'undefined' ) ? $db->quote($rq->vencimientoHasta)         : "";
        $param['cobroDesde']       = (trim($rq->cobroDesde) !== '' && $rq->cobroDesde       !== 'undefined' ) ? $db->quote($rq->cobroDesde)               : ""; 
        $param['cobroHasta']       = (trim($rq->cobroHasta) !== '' && $rq->cobroHasta       !== 'undefined' ) ? $db->quote($rq->cobroHasta)               : "";
        // Estado
        $param['cobrado']          = ($rq->cobrado)          ? $db->quote($rq->cobrado, 'INTEGER')       : "";
        $param['impreso']          = ($rq->impreso)          ? $db->quote($rq->impreso, 'INTEGER')       : "";
        $param['estado']           = ($rq->estado)           ? $db->quote($rq->estado, 'INTEGER')        : "";
        
        // Formato
        $param['cabecera']         = ($rq->cabecera)         ? $db->quote($rq->cabecera, 'INTEGER')      : "";
        $param['ordenCombo']       = ($rq->ordenCombo)       ? $db->quote($rq->ordenCombo, 'INTEGER')    : "";
        $param['ordenSentido']     = ($rq->ordenSentido)     ? $db->quote($rq->ordenSentido, 'INTEGER')  : "";
        $param['formato']          = ($rq->formato)          ? $db->quote($rq->formato, 'INTEGER')       : "";
        return $param;
    }
    public function verreporteAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $report = new Rad_BirtEngine();
        $report->setParameter('Id', 2, 'Int');
        $rq = $this->getRequest();
        $db = Zend_Registry::get('db');
        $param   = $this->buildParametros($rq,$db);
        $formato = ($rq->formato) ? $rq->formato : 'pdf';
        switch ($formato) {
            case 'pdf': 
            case 'xls':
                if ($param['cabecera']) {
                    switch ($param['cabecera']) {
                        case 1: $cabeceraUsar   = 'CabeceraInterna';  break;
                        case 2: $cabeceraUsar   = 'Cabecera';         break;
                        case 3: $cabeceraUsar   = 'CabeceraVacia';    break;                
                    }
                } else { $cabeceraUsar = 'CabeceraInterna'; }
                
                $where = ' where C.ChequeEstado not in (1,5) and '.$this->buildWhere($param).' '.$this->buildOrder($param);
                $join  = $this->buildJoin($param);
                $file  = APPLICATION_PATH . '/../birt/Reports/Rep_Cheques.rptdesign';
                //Rad_Log::debug($where);
                $report->renderFromFile($file, $formato, 
                    array(
                        'WHERE'     => $where,
                        'CABECERA'  => $cabeceraUsar,
                        'JOIN'      => $join,
                        'FILTRO'    => htmlentities($rq->filtro)
                    )
                );
                $NombreReporte  = "Reporte_de_Cheques___".date('YmdHis');        
                $report->sendStream($NombreReporte);
                
            break;
          
            case 'csv':
                $sql = $this->buildSQL($param);
                $datos = $db->fetchAll($sql);
                $e = new FileExport(FileExport::MODE_SEPARATOR);
                $e->setLineEnd("\r\n");
                $e->addAll($datos);
                $contenido = $e->getContent();
                $Nombre    = "Reporte de Cheques___".date('YmdHis').".csv";
                header("Content-disposition: attachment; filename=$Nombre");
                header("Content-type: text/csv");
                echo $contenido;                
            break;
        }
    }
}
