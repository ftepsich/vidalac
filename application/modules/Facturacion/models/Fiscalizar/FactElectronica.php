<?php
require_once 'Adapter.php';

/**
 * Facturacion_Model_Fiscalizar_FactElectronica
 *
 * Adaptador para fiscalizar comprobantes usando el web service de la afip wsfev1
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martin Alejandro Santangelo
 */
class Facturacion_Model_Fiscalizar_FactElectronica extends Facturacion_Fiscalizar_Adapter_Abstract
{
    /**
     * @var bool
     */
    protected $requiereImpresion = true;

    /**
     * [$templateImpresion description]
     * @var string
     */
    public $templateImpresion = "Comp_FacturaEmitida_Electronica";

    /**
     * @var bool
     */
    protected $generaNumero      = true;
    /**
     * Indica si el adaptador permite refiscalizar
     */
    protected $permiteRefiscalizar = true;
    /**
     * @var array
     */
    protected $tiposComprobantes = array(
        24, 25, 26, 28, 29, 30, 31, 32, 37, 38, 39, 40, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88
    );

    /**
     * Fiscaliza el comprobante
     *
     * @param Rad_Db_Table_Row $comprobante
     * @throws Exception
     * @throws Facturacion_Model_Fiscalizar_Exception
     */
    public function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        // hace las verificaciones pertinentes
        $comprobantesModel = new Facturacion_Model_DbTable_Facturas();

        require_once 'FactElect/Wsfev1.php';

        parent::fiscalizar($comprobante);

        $punto = $this->_getPunto($comprobante);

        // Obtenemos el tipo de comprobante Nuestro
        $tipoComprobantes  = $comprobante->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        if (!$tipoComprobantes) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de comprobante');

        $afipTiposDeComprobantes = $tipoComprobantes->findParentRow('Afip_Model_DbTable_AfipTiposDeComprobantes');
        if (!$afipTiposDeComprobantes) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de comprobante en las tablasde Afip');

        // Obtenemos la divisa nuestra
        $tipoDivisa = $comprobante->findParentRow('Base_Model_DbTable_TiposDeDivisas');
        if (!$tipoDivisa) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el tipo de divisa');

        // Obtenemos la divisa de Afip a partir de la nuestra
        $afipDivisa = $tipoDivisa->findParentRow('Afip_Model_DbTable_AfipMonedas');
        if (!$afipDivisa) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de divisa en las tablas de AFIP');

        $cliente = $this->_getClienteDoc($comprobante);

        // Cabecera del comprobante
        $FeCabReq = array();
        $FeCabReq['CantReg']  = 1;    // Por ahora validamos un comprobante por vez
        $FeCabReq['PtoVta']   = $punto;
        $FeCabReq['CbteTipo'] = $afipTiposDeComprobantes->Codigo;

        $auth = null;

        // Si ya se encuentra numerada probamos traer la info de la afip
        if ($comprobante->Numero) {

            try {
                $params = array();
                $params['FeCompConsReq'] = array();
                $params['FeCompConsReq']['CbteTipo'] = $afipTiposDeComprobantes->Codigo;
                $params['FeCompConsReq']['CbteNro']  = $comprobante->Numero;
                $params['FeCompConsReq']['PtoVta']   = $punto;

                $auth = FactElect_Wsfev1::FECompConsultar($params);

            } catch(Exception $e){
               // Si la factura no se encuentra en la afip simplemente ignoro el error y sigo con el proceso de registro
               if($e->getCode() != 602) throw $e;
            }
        }

        // Si esta autorizado guardamos la info y salimos
        if ($auth) {
            $this->_persistLostResponse($auth, $comprobante);
            return;
        }

        $numeroFactura = $this->_getFacturaNumero($comprobante, $afipTiposDeComprobantes, $punto);

        // Detalle de la cabecera
        $FeDetReq = array();

        $FEDetRequest = new StdClass();

        // Cotizacion de la divisa, si es pesos es 1
        if ($comprobante->Divisa == 1) {
            $cotizacion = 1;
        } else {
            $cotizacion = $comprobante->ValorDivisa;
        }

        $FEDetRequest->Concepto     = 1;
        $FEDetRequest->DocTipo      = $cliente['Tipo'];
        $FEDetRequest->CbteDesde    = $numeroFactura;
        $FEDetRequest->CbteHasta    = $numeroFactura;
        $FEDetRequest->DocNro       = $cliente['Doc'];
        $FEDetRequest->CbteFch      = date('Ymd', strtotime($comprobante->FechaEmision));
        $FEDetRequest->ImpTotal     = round($comprobantesModel->recuperarMontoTotal($comprobante->Id),2);
        $FEDetRequest->ImpTotConc   = round($comprobantesModel->afip_ImporteNetoNoGravado($comprobante->Id),2);
        $FEDetRequest->ImpNeto      = round($comprobantesModel->afip_ImporteNetoGravado($comprobante->Id),2);
        $FEDetRequest->ImpOpEx      = round($comprobantesModel->afip_ImporteNetoExento($comprobante->Id),2);
        $FEDetRequest->ImpTrib      = round($comprobantesModel->afip_MontoConceptosNoIVA($comprobante->Id),2);

//        Rad_Log::debug( $FEDetRequest->ImpTotal );
//        Rad_Log::debug( $FEDetRequest->ImpTotConc );
//        Rad_Log::debug( $FEDetRequest->ImpNeto );
//        Rad_Log::debug( $FEDetRequest->ImpOpEx );
//        Rad_Log::debug( $FEDetRequest->ImpTrib );

        // MiPyMEs - Solo se informa FchVtoPago en Facturas 
        if (in_array($afipTiposDeComprobantes->Codigo,array(201,206))){
           $FEDetRequest->FchVtoPago   = date('Ymd', strtotime($comprobante->FechaVencimiento));
        }

        $FEDetRequest->MonId        = 'PES';
        $FEDetRequest->MonCotiz     = 1;
//        $FEDetRequest->MonId        = $afipDivisa->Codigo;
//        $FEDetRequest->MonCotiz     = $cotizacion;
//        $FEDetRequest->FchServDesde = date('Ymd', date('U'));
//        $FEDetRequest->FchServHasta = date('Ymd', date('U'));
//
        // Agregamos el array de IVA
        $ivas = $comprobantesModel->afip_ArrayConceptosIVA($comprobante->Id);

        $totalIvas = 0;
        foreach ($ivas as $iva) {
            if (!$iva['MontoImponible']) continue;
           // if (!$iva['Monto']) continue;
            $tmp = new stdClass();
            $tmp->Id        = $iva['codAfip'];
//          Rad_Log::debug( $tmp->Id );
            $tmp->BaseImp   = round($iva['MontoImponible'],2);
            $tmp->Importe   = round($iva['Monto'],2);
            $totalIvas      += $iva['Monto'];

            $FEDetRequest->Iva->AlicIva[] = $tmp;
        }

        $FEDetRequest->ImpIVA = round($totalIvas,2);

//      Rad_Log::debug( $FEDetRequest->ImpIVA );

        // Agregamos los tributos
        $tributos = $comprobantesModel->afip_ArrayConceptosNoIVA($comprobante->Id);

        foreach ($tributos as $trib) {
           // if (!$iva['Monto']) continue;
            $tmp = new stdClass();
            $tmp->Id      = $trib['codAfip'];
            $tmp->BaseImp = $trib['MontoImponible'];
            $tmp->Alic    = $trib['ConceptoImpositivoPorcentaje'];
            $tmp->Importe = $trib['Monto'];
            $tmp->Desc    = $trib['Descripcion'];
            $FEDetRequest->Tributos->Tributo[] = $tmp;
        }

        // Opcionales para MiPyMEs (obligatorio)
        if ($afipTiposDeComprobantes->Codigo >= 201 && $afipTiposDeComprobantes->Codigo <= 208){
           
           if (in_array($comprobante->TipoDeComprobante,array(79,80))) {

              $modelCBP = new Facturacion_Model_DbTable_vCuentaBancariaPrincipal();
              $rsCBP = $modelCBP->fetchRow('TipoDeCuenta=1 and Propia=1');

              // CBU
              $tmp = new stdClass();
              $tmp->Id      = "2101";
              $tmp->Valor   = $rsCBP->Cbu;
              $FEDetRequest->Opcionales->Opcional[] = $tmp;
              // CBU Alias
              //$tmp = new stdClass();
              //$tmp->Id      = "2102";
              //$tmp->Valor   = "CBU.ALIAS";
              //$FEDetRequest->Opcionales->Opcional[] = $tmp;

           }
    
           // Anulacion por Emisión
           if (in_array($comprobante->TipoDeComprobante,array(81,83,85,87))) {
               $tmp = new stdClass();
               $tmp->Id      = "22";
               $tmp->Valor   = "N";
               $FEDetRequest->Opcionales->Opcional[] = $tmp;
           }
           // Anulacion por Rechazo
           if (in_array($comprobante->TipoDeComprobante,array(82,84,86,88))) {
               $tmp = new stdClass();
               $tmp->Id      = "22";
               $tmp->Valor   = "S";
               $FEDetRequest->Opcionales->Opcional[] = $tmp;
           }
           // Comprobante Relacionado a la Anulacion

           if (in_array($comprobante->TipoDeComprobante,array(81,82,83,84,85,86,87))) {

              if ($comprobante->ComprobanteRelacionado) {

                 $modelFEA = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
                 $rsFEA = $modelFEA->fetchRow('Comprobante = '.$comprobante->ComprobanteRelacionado);

                 $tmp = new stdClass();
                 $tmp->Tipo    = $rsFEA->CbteTipo;
                 $tmp->PtoVta  = $rsFEA->PtoVta;
                 $tmp->Nro     = $rsFEA->CbteDesde;
                 $tmp->Cuit    = $rsFEA->Cuit;
                 $tmp->CbteFch = $rsFEA->CbteFch;
                 $FEDetRequest->CbtesAsoc->CbteAsoc[] = $tmp;

              }

           }

        }

        $FeDetReq[0] = $FEDetRequest;

        $params['FeCAEReq']['FeCabReq'] = $FeCabReq;
        $params['FeCAEReq']['FeDetReq'] = $FeDetReq;

        try {
            $result = FactElect_Wsfev1::FECAESolicitar($params);
        } catch (Exception $e){
            // Si algo falla el comprobante debe volver a tener numero 0 sino no lo fiscalizara de nuevo.
            $comprobante->getTable()->setNumeroFactura_Fiscalizador(0, $comprobante->Id);
            throw $e;
        }

        $this->_persistResponse($result, $comprobante);
    }

    protected function _getPunto($comprobante)
    {
        $puntosModel    = new Base_Model_DbTable_PuntosDeVentas();
        $punto          = $puntosModel->find($comprobante->Punto)->current();
        if (!$punto) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el punto de venta');
        return $punto->Numero;
    }

    protected function _getInfoAfip($id)
    {
        $pModel         = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
        $persisitido    = $pModel->fetchRow('Comprobante = $id');
        return $persisitido;
    }

    protected function _persistResponse($result, $comprobante)
    {
        $data['Cuit']       = $result->FeCabResp->Cuit;
        $data['Comprobante']= $comprobante->Id;
        $data['PtoVta']     = $result->FeCabResp->PtoVta;
        $data['CbteTipo']   = $result->FeCabResp->CbteTipo;
        $data['FchProceso'] = $result->FeCabResp->FchProceso;
        $data['Resultado']  = $result->FeCabResp->Resultado;
        $data['Concepto']   = $result->FeDetResp->FECAEDetResponse[0]->Concepto;
        $data['DocTipo']    = $result->FeDetResp->FECAEDetResponse[0]->DocTipo;
        $data['DocNro']     = $result->FeDetResp->FECAEDetResponse[0]->DocNro;
        $data['CbteDesde']  = $result->FeDetResp->FECAEDetResponse[0]->CbteDesde;
        $data['CbteHasta']  = $result->FeDetResp->FECAEDetResponse[0]->CbteHasta;
        $data['CbteFch']    = $result->FeDetResp->FECAEDetResponse[0]->CbteFch;
        $data['CAE']        = $result->FeDetResp->FECAEDetResponse[0]->CAE;
        $data['CAEFchVto']  = $result->FeDetResp->FECAEDetResponse[0]->CAEFchVto;
        $data['Obs']        = FactElect_Wsfev1::getObs();

        $pModel = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
        $pModel->insert($data);
    }

    protected function _persistLostResponse($result, $comprobante)
    {
        $data['Cuit']       = $result->ResultGet->Cuit;
        $data['Comprobante']= $comprobante->Id;
        $data['PtoVta']     = $result->ResultGet->PtoVta;
        $data['CbteTipo']   = $result->ResultGet->CbteTipo;
        $data['FchProceso'] = $result->ResultGet->FchProceso;
        $data['Resultado']  = $result->ResultGet->Resultado;
        $data['Concepto']   = $result->ResultGet->Concepto;
        $data['DocTipo']    = $result->ResultGet->DocTipo;
        $data['DocNro']     = $result->ResultGet->DocNro;
        $data['CbteDesde']  = $result->ResultGet->CbteDesde;
        $data['CbteHasta']  = $result->ResultGet->CbteHasta;
        $data['CbteFch']    = $result->ResultGet->CbteFch;
        $data['CAE']        = $result->ResultGet->CodAutorizacion;
        $data['CAEFchVto']  = $result->ResultGet->FchVto;
        $data['Obs']        = FactElect_Wsfev1::getObs();

        $pModel = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
        $row    = $pModel->fetchRow("Comprobante = $comprobante->Id");

        if ($row) {
            $pModel->update($data,"Id = $row->Id");
        } else {
            $pModel->insert($data);
        }
    }

    protected function _getFacturaNumero($comprobante, $afipTiposDeComprobantes, $punto)
    {
//      Rad_Log::debug("$punto");
        $ultCmp = FactElect_Wsfev1::FECompUltimoAutorizado(array(
            'PtoVta'   => $punto,
            'CbteTipo' => $afipTiposDeComprobantes->Codigo
        ));
        $ULT_CBTE       = $ultCmp->CbteNro;
        $numeroFactura  = $ULT_CBTE + 1;
        // Guardamos el numero en el comprobante
        //Rad_Log::debug("Nuevo Numero de Factura a Fiscalizar $numeroFactura");
        $comprobante->getTable()->setNumeroFactura_Fiscalizador($numeroFactura, $comprobante->Id);
        return $numeroFactura;
    }

    /**
     * Obtiene el Número y tipo de documento del cliente;
     *
     * @param Zend_DbTable_Row $comprobante
     * @return array
     */
    protected function _getClienteDoc($comprobante)
    {
        // Obtenemos el cliente
        $cliente            = $comprobante->findParentRow('Base_Model_DbTable_Personas');
        if (!$cliente)     throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el cliente');
        $cuit               = (float)trim(preg_replace('#[^\d]#','',$cliente->Cuit));
        // Si tiene cuit lo enviamos
        if ($cuit) {
            return array (
                'Doc' => $cuit, //$cuit,
                'Tipo'=> 80     // Valor del codigo Afip de Cuit
            );
        } else { // Sino enviamos el doc que tenga ingresado
            if (!trim($cliente->Dni))   throw new Facturacion_Model_Fiscalizar_Exception("El Cliente $ciente->RazonSocial No tiene Cuit o Dni cargados");

            $tipoDocumento              = $cliente->findParentRow('Base_Model_DbTable_TiposDeDocumentos');
            if (!$tipoDocumento)        throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de documento.');

            $afipTipoDocumento          = $tipoDocumento->findParentRow('Afip_Model_DbTable_AfipTiposDeDocumentos');
            if (!$afipTipoDocumento)    throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de documento de Afip.');

            return array (
                'Doc' => $cliente->Dni,
                'Tipo'=> $afipTipoDocumento->Codigo
            );
        }
    }
}
