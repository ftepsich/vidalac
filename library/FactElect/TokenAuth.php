<?php

/**
 * Autorizacion de Afip
 */
class FactElect_TokenAuth {
    private $TAfile;
    private $filePath;
    private $service;

    public function __construct($service) {
        $cfg = Rad_Cfg::get('/configs/cert/fe.ini');
        $this->filePath = $cfg->FacturacionElectronica->TA.'TA'.$service.'.xml';
        $this->service = $service;

        if (!file_exists($this->filePath)) {
            $this->generateTA();
        }
        $this->loadTa();
    }

    protected function loadTa()
    {
        $this->TAfile = simplexml_load_file($this->filePath);
    }

    public function getToken() {
        return $this->getTA()->credentials->token;
    }

    public function getExpirationTime() {
        return date('Y-m-d H:i:s',strtotime ($this->getTA()->header->expirationTime));
    }

    public function isExpired() {
        return date('Y-m-d H:i:s',strtotime ($this->getTA()->header->expirationTime)) < date('Y-m-d H:i:s');
    }

    public function getSign() {
        return $this->getTA()->credentials->sign;
    }

    private function getTA() {
        return $this->TAfile;
    }

    public function actualizate() {
        $this->generateTA();
        $this->loadTa();
    }

    /**
     * Manejar el error como la gente
     * @return unknown_type
     */
    private function generateTA() {
        ini_set("soap.wsdl_cache_enabled", "0");

        require_once 'Wsaa.php';

        FactElect_Wsaa::CreateTRA($this->service);

        $CMS = FactElect_Wsaa::SignTRA();
        $TA  = FactElect_Wsaa::CallWSAA($CMS);

        if (!file_put_contents($this->filePath, $TA)) {
            throw new Exception("FactElect_TA->generateTA: Error al escribir $this->filePath");
        }
        chown($this->filePath, 'www-data');
    }

}