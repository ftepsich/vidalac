<?php
class FactElect_Wsaa
{
    public static function CreateTRA($service) {
        $cfg = Rad_Cfg::get('/configs/cert/fe.ini');
        $TRA = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">' .
            '</loginTicketRequest>'
        );
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', date('U'));
        $TRA->header->addChild('generationTime', date('c', date('U') - 600));
        $TRA->header->addChild('expirationTime', date('c', date('U') + 600));
        $TRA->addChild('service', $service);

        if (!$TRA->asXML($cfg->FacturacionElectronica->TEMPPATH.'/TRA.xml')) {
            require 'WsaaException.php';
            throw new WsaaException("Error al crear TRA.xml");
        }
    }

    #==============================================================================
    # This functions makes the PKCS#7 signature using TRA as input file, CERT and
    # PRIVATEKEY to sign. Generates an intermediate file and finally trims the
    # MIME heading leaving the final CMS required by WSAA.

    public static function SignTRA() {
        $cfg = Rad_Cfg::get('/configs/cert/fe.ini');

        if (!file_exists($cfg->FacturacionElectronica->CERT)) {
            require 'WsaaException.php';
            throw new WsaaException('Error al intentar abrir el archivo CERT');
        }
        if (!file_exists($cfg->FacturacionElectronica->PRIVATEKEY)) {
            require 'WsaaException.php';
            throw new WsaaException('Error al intentar abrir el archivo PRIVATEKEY');
        }
        if (!file_exists(dirname(__FILE__).'/cert/wsaa.wsdl')) {
            require 'WsaaException.php';
            throw new WsaaException('Error al intentar abrir el archivo PASSPHRASE');
        }
        umask(766);
        $STATUS = openssl_pkcs7_sign(
            $cfg->FacturacionElectronica->TEMPPATH.'/TRA.xml',
            $cfg->FacturacionElectronica->TEMPPATH.'/TRA.tmp',
            'file://' . $cfg->FacturacionElectronica->CERT,
            array('file://' . $cfg->FacturacionElectronica->PRIVATEKEY, $cfg->FacturacionElectronica->PASSPHRASE),
            array(),
            !PKCS7_DETACHED
        );
        if (!$STATUS) {
            require 'WsaaException.php';
            throw new WsaaException('Error al intentar firmar el TRA');
        }
        $inf = fopen($cfg->FacturacionElectronica->TEMPPATH.'/TRA.tmp', 'r');
        $i   = 0;
        $CMS = "";
        while (!feof($inf)) {
            $buffer = fgets($inf);
            if ($i++ >= 4) {
                $CMS .= $buffer;
            }
        }
        fclose($inf);
        unlink($cfg->FacturacionElectronica->TEMPPATH.'/TRA.xml');
        unlink($cfg->FacturacionElectronica->TEMPPATH.'/TRA.tmp');
        return $CMS;
    }

    #==============================================================================

    public static function CallWSAA($CMS) {
        $cfg = Rad_Cfg::get('/configs/cert/fe.ini');
        # Now we create a context to specify remote web server certificate checking
        # If you don want to check remote server, you may set verify_peer to FALSE.
        $ctx = stream_context_create(
            array(
                'ssl' => array(
                    #'capath'            => "/xxx/yyy/",
                    #'localcert'         => "crtPlusKey.pem",
                    #'passphrase'        => "xxxx",

                    'cafile' => $cfg->FacturacionElectronica->REMCACERT,
                    'allow_self_signed' => $cfg->FacturacionElectronica->REMSELFSIGN,
                    'verify_peer' => $cfg->FacturacionElectronica->REMVERIFY
                )
            )
        );

        $client = new SoapClient(
            dirname(__FILE__).'/cert/wsaa.wsdl',
            array(
                #'proxy_host'     => "proxy",
                #'proxy_port'     => 80,
                'stream_context' => $ctx,
                'soap_version' => SOAP_1_2,
                'location' => $cfg->FacturacionElectronica->WSAAURL,
                'exceptions' => 0
            )
        );
        $results = $client->loginCms(
            array('in0' => $CMS)
        );
        if (is_soap_fault($results)) {
            require 'WsaaException.php';
            throw new WsaaException($results->faultcode .' '. $results->faultstring);
        }
        return $results->loginCmsReturn;
    }

}
