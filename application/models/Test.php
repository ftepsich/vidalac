<?php
require_once 'FactElect/ta.php';
require_once 'FactElect/wsfe.php';

class Model_Test
{

	public function notify($sender, $event, $params)
	{
		Zend_Wildfire_Plugin_FirePhp::send($params, "Evento:".$event,Zend_Wildfire_Plugin_FirePhp::LOG);
	}

	public function fe()
	{
		
//		$ultimoNro = FactElect_Wsfex::getTiposExportaciones();
        $ultimoNro = FactElect_Wsfe::RecuperaQTY();
		
		return $ultimoNro;
//		$ultimoCbt = FactElect_Wsfe::RecuperaLastCMP($client, $token, $sign, $this->getPuntoVenta()->getCode(), $this->getTipoComprobante()->getCode());
//		$comprobanteAutorizado = FactElect_Wsfe::Aut($client, $token, $sign, $ultimoNro + 1, $ultimoCbt + 1, $this);

	}
}