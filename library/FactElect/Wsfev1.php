<?php

/**
 * @copyright SmartSoftware Argentina
 * @package FactElect
 *
 * @author Martin Alejandro Santangelo
 */
require_once 'WsfeException.php';
require_once 'BaseService.php';

/**
 * FactElect_Wsfe
 * Cliente del Web Service Facturacion electronica v1 de la afip
 * Obligatorio a partir del 01/01/2010
 *
 * @copyright SmartSoftware Argentina
 * @package FactElect
 * @author Martin Alejandro Santangelo
 */
class FactElect_Wsfev1 extends FactElect_BaseService
{
    /**
     * Nombre Servicio Web Afip
     * @var string
     */
    protected static $service = 'wsfe';

    /**
     * Ruta archivo WSDL
     * @var string
     */
    protected static $wsdl = '/cert/wsfev1.wsdl';

    /**
     * Parametro de la configuracion del que se obtiene la Url del Servicio Web Afip
     * @var string
     */
    protected static $serviceUrl = 'WSFEV1URL';

    /**
     * Nombre del parametro eventos para el WS
     * @var string
     */
    protected static $eventProperty = 'Events';

    /**
     * Solicita el cae para un comprobante
     *
     * @param $params
     * @return stdClass
     */
    public static function FECAESolicitar($params) {
        // error_log(print_r($params, true), 3, APPLICATION_PATH.'/../logs/wsfe-errors');
        $result = self::call('FECAESolicitar', $params);
        self::_checkFECAESolicitarResult($result);
        return $result;
    }

    /**
     * Revisa si se recibio un evento junto a la respuesta y lo formatea
     * @param $results
     */
    protected static function _chkServiceEvents($results)
    {
        $e = static::$eventProperty;
        $events = $results->{$e};

        // eventos formateados
        $fevents = array();

        if ($events) {
            foreach ($events as $event) {

                self::log("Evento $event->EventCode: ".$event->EventMsg);

                $fevents[] = array(
                    'Codigo' => $event->Code,
                    'Msg'    => $event->Msg
                );
            }
        }
        static::$events = $fevents;
    }

    protected static function _checkFECAESolicitarResult($result)
    {
        self::$caeRequestObs = "";
        foreach ($result->FeDetResp->FECAEDetResponse[0]->Observaciones->Obs as $e)
        {
            self::$caeRequestObs .= htmlentities($e->Code . ': ' . $e->Msg)."<br>";
        }

        if ($result->FeCabResp->Resultado == 'R') {

            $msg = '<b>Afip Rechazo el comprobante:</b><br>';
            self::log('Comprobante Rechazado: '.self::$caeRequestObs);

            throw new WsfeException($msg.self::$caeRequestObs);
        }
    }

    protected static function _chkServiceError($results)
    {
        $msg = "";
        if (isset($results->Errors))
        {
            foreach ($results->Errors->Err as $e)
            {
                // Si el codigo de error es de 'Verificación de Token y Firma' actualizamos el token
                if ($e->Code == 600) {
                    self::$ta->actualizate();
                    self::$retry = true;
                }

                $msg .= htmlentities($e->Code . ': ' . $e->Msg)."<br>";
                self::log($e->Code . ': ' . $e->Msg);
            }
            throw new WsfeException($msg, $e->Code);
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
}