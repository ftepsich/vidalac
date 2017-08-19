<?php
/**
 * FactElect_Wsfe
 *
 * @copyright SmartSoftware Argentina
 * @package FactElect
 * @author Martin Alejandro Santangelo
 */

require_once 'ta.php';

/**
 * FactElect_Wsfe
 * Cliente del Web Service Facturacion electronica
 *
 * @copyright SmartSoftware Argentina
 * @package FactElect
 * @author Martin Alejandro Santangelo
 */
class FactElect_Wsfe {

    /**
     * @var FactElect_TA
     */
    protected static $ta;
    /**
     * @var string token
     */
    protected static $token;
    /**
     * @var string sign
     */
    protected static $sign;
    /**
     * @var Soap_Client
     */
    protected static $client;

    /**
     * Verfica si exitio error en la peticion ajax
     * @param Object $results
     */
    protected static function _chkSoapFault($results)
    {
        if (is_soap_fault($results)) {
            throw new Exception("Error de comunicacion $results->faultcode, $results->faultstring");
        } else {
            if($results->FEXEvents->EventCode != 0) {
                error_log("Evento $results->FEXEvents->EventCode: ".$results->FEXEvents->EventMsg, 3, APPLICATION_PATH.'/../logs/wsfe-event');
                throw new Exception($results->FEXEvents->EventMsg);
            }
        }
    }

    /**
     * Genera el FactElect_TA y si esta expirado lo actualiza
     */
    protected static function getTa() {
        if (!self::$ta) {
            $ta = new FactElect_TA(FactElect_TA::WSFE);
            if ($ta->isExpired()) {
                $ta->actualizate();
            }
            self::$ta = $ta;
            self::$token = $ta->getToken();
            self::$sign = $ta->getSign();
        }
    }

    protected static function _RecuperaQTY() {
        $cfg = Rad_Cfg::get();
        $results = self::$client->FERecuperaQTYRequest(
            array(
                'argAuth' => array(
                    'Token' => self::$token,
                    'Sign' => self::$sign,
                    'cuit' => doubleval($cfg->FacturacionElectronica->CUIT)
                )
            )
        );
        self::_chkSoapFault($results);
        return $results;
    }

    public static function RecuperaQTY() {

        self::getTa();
        self::generateSoapClient();

        $results = self::_RecuperaQTY();

        if ($results->FERecuperaQTYRequestResult->RError->percode == 1000) {
            self::$ta->actualizate();
            $results = self::_RecuperaQTY();
        }

        if ($results->FERecuperaQTYRequestResult->RError->percode != 0) {
            $ec = $results->FERecuperaQTYRequestResult->RError->percode;
            require 'wsfeException.php';
            throw new WsfeException(
                $ec,
                $results->FERecuperaQTYRequestResult->RError->perrmsg
            );
        }
        return $results->FERecuperaQTYRequestResult->qty->value;
    }

    #==============================================================================
    protected static function _UltNro() {
        $cfg = Rad_Cfg::get();
        $results = self::$client->FEUltNroRequest(
            array(
                'argAuth' => array(
                    'Token' => self::$token,
                    'Sign'  => self::$sign,
                    'cuit'  => doubleval($cfg->FacturacionElectronica->CUIT)
                )
            )
        );
        self::_chkSoapFault($results);
        return $results;
    }

    public static function ultNro() {

        self::getTa();
        self::generateSoapClient();

        $results = self::_UltNro();

        if ($results->FERecuperaQTYRequestResult->RError->percode == 1000) {
            self::$ta->actualizate();
            $results = self::_UltNro();
        }

        if ($results->FEUltNroRequestResult->RError->percode != 0) {
            $ec = $results->FEUltNroRequestResult->RError->percode;
            require 'wsfeException.php';
            throw new WsfeException(
                    $ec,
                    $results->FEUltNroRequestResult->RError->perrmsg
            );
        }
        return $results->FEUltNroRequestResult->nro->value;
    }

    #==============================================================================
    protected static function _RecuperaLastCMP($ptovta, $tipocbte) {
        $cfg = Rad_Cfg::get();
        $results = self::$client->FERecuperaLastCMPRequest(
            array(
                'argAuth' => array(
                    'Token' => self::$token,
                    'Sign' => self::$sign,
                    'cuit' => doubleval($cfg->FacturacionElectronica->CUIT)
                ),
                'argTCMP' => array(
                    'PtoVta' => $ptovta,
                    'TipoCbte' => $tipocbte
                )
            )
                
        );
        self::_chkSoapFault($results);
        return $results;
    }

    public static function recuperaLastCMP($ptovta, $tipocbte) {
        self::getTa();
        self::generateSoapClient();

        $results = self::_RecuperaLastCMP($ptovta, $tipocbte);

        if ($results->FERecuperaQTYRequestResult->RError->percode == 1000) {
            self::$ta->actualizate();
            $results = self::_RecuperaLastCMP($ptovta, $tipocbte);
        }
        
        if ($results->FERecuperaLastCMPRequestResult->RError->percode != 0) {
            $ec = $results->FERecuperaLastCMPRequestResult->RError->percode;
            require 'wsfeException.php';
            throw new WsfeException(
                $ec,
                $results->FERecuperaLastCMPRequestResult->RError->perrmsg
            );
        }
        return $results->FERecuperaLastCMPRequestResult->cbte_nro;
    }

    protected static function _Aut($ID, $cbte, $comprobante) {
        $cfg = Rad_Cfg::get();
        $results = self::$client->FEAutRequest(
            array(
                'argAuth' => array(
                    'Token' => self::$token,
                    'Sign'  => self::$sign,
                    'cuit'  => doubleval($cfg->FacturacionElectronica->CUIT)
                ),
                'Fer' => array(
                    'Fecr' => array (
                        'id' => $ID,
                        'cantidadreg' => 1, //TODO: hacer una llamada para esto recursivo. Solo se hace si es factura B y por montos menores a $1000 x c/u
                        'presta_serv' => $comprobante->getEsServicio()
                    ),
                    'Fedr' => array(
                        'FEDetalleRequest' => array(
                            'tipo_doc'  => $comprobante->getCliente()->getTipoDocumento()->getCode(),
                            'nro_doc'   => $comprobante->getCliente()->getNroDocumento(),
                            'tipo_cbte' => $comprobante->getTipoComprobante()->getCode(),
                            'punto_vta' => $comprobante->getPuntoVenta()->getCode(),
                            'cbt_desde' => $cbte, //TODO: hacer una llamada para esto recursivo. Solo se hace si es factura B y por montos menores a $1000 x c/u
                            'cbt_hasta' => $cbte, //Si es B, es el número de factura desde y el número de factura hasta. Solo se permite para B
                            'imp_total' => $comprobante->getImpTotal(),
                            'imp_tot_conc' => $comprobante->getImpTotalConceptos(),
                            'imp_neto'  => $comprobante->getImpNeto(),
                            'impto_liq' => $comprobante->getImpLiquidado(),
                            'impto_liq_rni' => $comprobante->getImpLiquidadoRni(),
                            'imp_op_ex'  => $comprobante->getImpOperacionesEx(),
                            'fecha_cbte' => $comprobante->getFechaComprobante('Ymd'),
                            'fecha_serv_desde' => $comprobante->getFechaServicioDesde('Ymd'),
                            'fecha_serv_hasta' => $comprobante->getFechaServicioHasta('Ymd'),
                            'fecha_venc_pago'  => $comprobante->getFechaVencimientoPago('Ymd')
                        )
                    )
                )
            )
        );
        self::_chkSoapFault($results);
        return $results;
    }

    /**
     * Pide aprobacion de la AFIP de un comprobante
     * 
     * @param int $ID
     * @param int $cbte
     * @param Zend_Db_Table_Row $comprobante
     * @return <type>
     */
    public static function emitirFactura($comprobante) {

        self::getTa();
        self::generateSoapClient();

        $results = self::_Aut($comprobante);

        if ($results->FERecuperaQTYRequestResult->RError->percode == 1000) {
            self::$ta->actualizate();
            $results = self::_Aut($comprobante);
        }

        if ($results->FEAutRequestResult->RError->percode != 0) {
            $ec = $results->FEAutRequestResult->RError->percode;
            require 'wsfeException.php';
            throw new WsfeException(
                    $ec,
                    $results->FEAutRequestResult->RError->perrmsg
            );
        }

        $comprobante->setNroComprobante($results->FEAutRequestResult->FecResp->id);
        $comprobante->setFechaCae($results->FEAutRequestResult->FecResp->fecha_cae);
        $comprobante->setReproceso($results->FEAutRequestResult->FecResp->reproceso);
        $comprobante->setMotivo("-Fec: " . $results->FEAutRequestResult->FecResp->motivo);

        $comprobante->setCae($results->FEAutRequestResult->FedResp->FEDetalleResponse->cae);
        $comprobante->setResultado($results->FEAutRequestResult->FedResp->FEDetalleResponse->resultado);
        $comprobante->setMotivo($comprobante->getMotivo() . " -Fed: " . $results->FEAutRequestResult->FedResp->FEDetalleResponse->motivo);
        $comprobante->setFechaVtoCae($results->FEAutRequestResult->FedResp->FEDetalleResponse->fecha_vto);
        return $comprobante;
    }

    #==============================================================================

    public static function dummy() {
        self::generateSoapClient();
        $results = self::$client->FEDummy();

        if (is_soap_fault($results)) {
            require 'wsfeException.php';
            throw new WsfeException($results->faultcode, $results->faultstring);
        }
        return $results;
    }

    #==============================================================================

    public static function generateSoapClient() {
        if (!self::$client) {
            $cfg = Rad_Cfg::get();

            self::$client = new SoapClient(
                dirname(__FILE__) . '/cert/wsfe.wsdl',
                array(
                    'soap_version' => SOAP_1_2,
                    'location' => $cfg->FacturacionElectronica->WSFEURL,
                    'exceptions' => 0,
                    'trace' => 1
                )
            );
        }
        return self::$client;
    }

    #==============================================================================

    public static function getCuitEmisor() {
        $cfg = Rad_Cfg::get();
        return $cfg->FacturacionElectronica->CUIT;
    }

}
