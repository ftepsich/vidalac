<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ConceptosFacturacionVarios extends Base_Model_DbTable_Articulos
{
	protected $_name = "Articulos";
	protected $_sort = array ("Descripcion asc","Tipo asc");

	// Para poner un valor por defecto en un campo--------
	protected $_defaultSource = self::DEFAULT_CLASS;
	protected $_defaultValues = array (
	    'Tipo'                  => '2',
	    'EsProducido'           => '0',
	    'RequiereProtocolo'     => '0',
	    'SeUtilizaParaFason'    => '0',
            'EsInsumo'              => '0',
            'EsParaVenta'           => '0',
            'RequiereLote'          => '0',
            'IVA'                   => '1',
            'TipoDeControlDeStock'  => '1'
	);

	protected function _makeDescripcion ($data)
	{
		return $data;
	}	
	
	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		$condicion = " Articulos.Tipo = 2 ";
	
	    $where = $this->_addCondition($where, $condicion);
	    return parent::fetchAll($where , $order , $count, $offset);
	}
	
	 public function fetchEsIVA($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " EsRetencion = 0 and EsPercepcion = 0 and EsIVA = 1 and EnUso = 1 ";
        $order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
	
	// fin Public Init -------------------------------------------------------------------------------------------
	
}