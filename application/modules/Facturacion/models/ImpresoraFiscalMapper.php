<?php
require_once 'Fiscal/HasarComm.php';
require_once 'Fiscal/HasarFiscal.php';

class Facturacion_Model_ImpresoraFiscalMapper
{
    /**
     * Verifica el estado de la impresora
     * @param $punto
     */
    public function estado($punto)
    {
        $p = $this->_getFiscal($punto);
        $e = $p->estado();

        if ($err = $p->isStatusError()) {
            throw new Exception($err);
        }
        return $e;
    }

    public function cancelar($punto)
    {
        $p = $this->_getFiscal($punto);
        $e = $p->cancelar();

        if ($err = $p->isStatusError()) {
            throw new Exception($err);
        }
        return $e;
    }

    /**
     * Genera el Cierre Z de la impresora fiscal
     * @param $punto
     */
    public function cierreDiario($punto)
    {
        $p = $this->_getFiscal($punto);

        $p->cierreDiario();

        if ($err = $p->isStatusError()) {
            throw new Exception($err);
        }
    }

    protected function _getFiscal($punto)
    {
        $cfg = Rad_Cfg::get('/configs/fiscal.yml');

        $conf = $cfg->puntodeventa->$punto;

        if (empty($conf)) throw new Facturacion_Fiscalizar_Adapter_Exception('No esta configurado el punto de venta en fiscal.yml');

        return new HasarFiscal(new HasarComm($conf->Ip, $conf->Port));
    }
}
