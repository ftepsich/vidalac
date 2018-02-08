<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_MarcasProduccion extends Base_Model_DbTable_Marcas
{
	protected $_name = "Marcas";

    public $abmTitle = 'Marcas Producción';

	// Para poner un valor por defecto en un campo--------
	protected $_defaultSource = self::DEFAULT_CLASS;

	protected $_defaultValues = array (
	    'Propia' => '0',
        'Produccion' => '1'
	);
	// ----------------------------------------------------
	// Se utiliza para produccion porque son marcas que pueden ser propias o de terceros
    public function fetchAll ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Produccion = 1";
        $where = $this->_addCondition($where, $condicion);
		return parent:: fetchAll ($where , $order , $count , $offset );
    }

}