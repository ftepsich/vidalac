<?php
require_once 'wsException.php';
require_once 'TokenAuth.php';

/**
 * Clase base con métodos comunes para los webservice de afip
 *
 * @copyright SmartSoftware Argentina
 * @package FactElect
 * @author Martin Alejandro Santangelo
 */
class FactElect_BaseService {
    /**
     * @var Bool
     */
    protected static $retry = false;
    /**
     * @var FactElect_TokenAuth
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
    public static $client;

    /**
     * Nombre Servicio Web Afip
     * @var string
     */
    protected static $service;
    /**
     * Ruta archivo WSDL
     * @var string
     */
    protected static $wsdl;

    /**
     * Url Servicio Web Afip
     * @var string
     */
    protected static $serviceUrl;

    /**
     * Array de eventos enviados por AFIP
     * @var array
     */
    protected static $events = null;

    /**
     * Nombre del parametro eventos para el WS
     * @var string
     */
    protected static $eventProperty = null;

    /**
     * @var string
     */
    protected static $caeRequestObs;


    /**
     * Metodo magico que captura la llamada a metodos no implementados explicitamentes en la clase
     * e intenta llamar al mismo en el webservice de AFIP
     *
     * @param  string $method Nombre del metodo
     * @param  array  $args   Argumentos
     * @return array          Respuesta del WebService
     */
    public static function __callStatic($method, $args)
    {
        /**
         * solo paso el primer argumento ya que el webservice recibe
         * los argumentos como un array asociativo
         */
        return self::call($method,$args[0]);
    }

    /**
     * @param $results
     * @throws WsfeException
     */
    protected static function _chkSoapFault($results)
    {
        if (is_soap_fault($results)) {
            self::log("Error de comunicacion $results->faultcode, $results->faultstring");
            throw new WsfeException("Error de comunicacion $results->faultcode, $results->faultstring");
        }
    }

    protected static function log($text)
    {
        error_log(static::$service.': '.$text."\n", 3, APPLICATION_PATH.'/../logs/wsfe-errors');
    }


    protected static function _chkServiceError($results)
    {
        // Implementar en hijos
    }

    protected static function _chkServiceEvents($results)
    {
        // Implementar en hijos
    }
    /**
     * Llama al metodo $func del web service pasando los parametros $params
     * Verifica los errores y automaticamente updatea los tokens de ser necesario y vuelve a llamar al mismo metodo.
     *
     *
     * @param string $func
     * @param array $params
     * @return stdClass
     */
    protected static function call($func, $params)
    {
        self::getTa();

        //Agregamos a los parametros los token de autorizacion
        self::_getAuth($params);

        return self::_call($func, $params);
    }

    protected static function _call($func, $params)
    {
        self::generateSoapClient();
        try {
            // Llamamos al metodo del web service
            // Rad_Log::getLog()->debug('>>> '.print_r($params, true));
            self::log(print_r($params, true));
            $results = self::$client->{$func}($params);
            // Rad_Log::getLog()->debug('<<< '.print_r($results, true));

            $resultVariable = $func.'Result';

            // Verificamos errores
            self::_chkSoapFault($results);

            static::_chkServiceError($results->{$resultVariable});
            static::_chkServiceEvents($results->{$resultVariable});

        } catch (Exception $e) {
            if (self::$retry) {
                self::$retry = false;
                $results = self::$client->{$func}($params);
                $resultVariable = $func.'Result';
                // Verificamos errores
                self::_chkSoapFault($results);
                static::_chkServiceError($results->{$resultVariable});
            } else {
                throw $e;
            }
        }
        return $results->{$resultVariable};
    }

    /**
     * Retorna el array de eventos que fueron informados por el webservice
     *
     * Se establecio el siguiente formato de array para los eventos
     * array(
     *  array(
     *    Codigo => 1
     *    Msg    => 'Error'
     *  ),
     *  array(
     *    Codigo => 2
     *    Msg    => 'Error2'
     *  )
     * )
     * Ya que los distintos webservice de afip retornan diferentes formatos
     *
     * @return array
     */
    public static function getEvents()
    {
        return self::$events;
    }

    /**
     * @param $params
     */
    protected static function _getAuth(&$params)
    {
        $cfg = Rad_Cfg::get('/configs/cert/fe.ini');

        if ($params instanceof stdClass) {
            $params->Auth->Token = self::$token;
            $params->Auth->Sign  = self::$sign;
            $params->Auth->Cuit  = doubleval($cfg->FacturacionElectronica->CUIT);
        } else {
            $params['Auth']['Token'] = self::$token;
            $params['Auth']['Sign']  = self::$sign;
            $params['Auth']['Cuit']  = doubleval($cfg->FacturacionElectronica->CUIT);
        }
    }

    /**
     * Genera el FactElect_TokenAuth y si esta expirado lo actualiza
     */
    protected static function getTa() {
        if (!self::$ta) {
            $ta = new FactElect_TokenAuth(static::$service);
            if ($ta->isExpired()) {
                $ta->actualizate();
            }
            self::$ta    = $ta;
            self::$token = $ta->getToken();
            self::$sign  = $ta->getSign();
        }
    }

    /**
     * Retorna las observaciones de la ultima respues por solicitud de CAE
     * @return string
     */
    public static function getObs()
    {
        return self::$caeRequestObs;
    }

    /**
     * Retororna el cliente Soap
     * return SoarpClient
     */
    public static function generateSoapClient() {
        if (!self::$client) {
            $cfg = Rad_Cfg::get('/configs/cert/fe.ini');

            $urlVar = static::$serviceUrl;

            self::$client = new SoapClient(
                $cfg->FacturacionElectronica->{$urlVar}.'?WSDL',
                array(
                    'soap_version'       => SOAP_1_2,
                    'location'           => $cfg->FacturacionElectronica->{$urlVar},
                    'encoding'           => 'ISO-8859-1',
                    'features'           => SOAP_USE_XSI_ARRAY_TYPE + SOAP_SINGLE_ELEMENT_ARRAYS,
                    'exceptions'         => 0,
                    'connection_timeout' => 7,
                    'trace'              => 1
                )
            );
        }
        return self::$client;
    }

    public static function getCuitEmisor() {
        $cfg = Rad_Cfg::get('/configs/cert/fe.ini');
        return $cfg->FacturacionElectronica->CUIT;
    }
}