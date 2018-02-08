<?php
	
class Rrhh_Model_DbTable_PersonasCaracteristicasValores extends Base_Model_DbTable_CaracteristicasValores
{
    protected $_name = 'CaracteristicasValores';
    protected $_sort = array('Caracteristicas.Descripcion asc');
    protected $_campoRelacion;
  
    public function init()
    {
    	$this->_campoRelacion = $this->_referenceMap["Personas"]["columns"];
		parent::init();
    }
}