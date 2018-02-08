<?php
/**
 * @copyright SmartSoftware Argentina
 * @package FactElect
 * @author Martin Alejandro Santangelo
 */
require_once 'WsfeException.php';
require_once 'BaseService.php';

/**
 * FactElect_Wsfex
 * Cliente del Web Service Facturacion electronica de exportacion
 *
 * @copyright SmartSoftware Argentina
 * @package FactElect
 * @author Martin Alejandro Santangelo
 */
class FactElect_Wsfex extends FactElect_BaseService
{

    /**
     * Nombre Servicio Web Afip
     * @var string
     */
    protected static $service = 'wsfex';

    /**
     * Ruta archivo WSDL
     * @var string
     */
    protected static $wsdl = '/cert/wsfex.wsdl';

    /**
     * Parametro de la configuracion del que se obtiene la Url del Servicio Web Afip
     * @var string
     */
    protected static $serviceUrl = 'WSFEXURL';

    /**
     * Nombre del parametro eventos para el WS
     * @var string
     */
    protected static $eventProperty = 'FEXEvents';

    protected static function _chkServiceError($results)
    {
        $msg = "";
        if (isset($results->FEXErr))
        {
            $e = $results->FEXErr;

            if ($e->ErrCode == 0) return;

            // Si el codigo de error es de 'Verificación de Token y Firma' actualizamos el token
//            if ($e->ErrCode == 1000) {
//                self::$ta->actualizate();
//                self::$retry = true;
//            }

            $msg .= htmlentities($e->ErrCode . ': ' . $e->ErrMsg)."<br>";
            self::log($e->ErrCode . ': ' . $e->ErrMsg);

            throw new WsfeException($msg, $e->ErrCode);
        }
    }

    /**
     * Revisa si se recibio un evento junto a la respuesta y lo formatea
     * @param $results
     */
    protected static function _chkServiceEvents($results)
    {
        $e = static::$eventProperty;
        $event = $results->{$e};

        if ($event){
            if($event->EventCode != 0) {
                self::log("Evento $event->EventCode: ".$event->EventMsg);
                $ev = array(
                    'Codigo' => $event->EventCode,
                    'Msg'    => $event->EventMsg
                );
                static::$events = array($ev);
            }
        }
    }
}
