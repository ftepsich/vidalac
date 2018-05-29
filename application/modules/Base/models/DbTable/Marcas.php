<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_Marcas extends Rad_Db_Table
{
	protected $_name = "Marcas";

	// Para poner un valor por defecto en un campo--------
	protected $_defaultSource = self::DEFAULT_CLASS;
	Protected $_sort = array ("Descripcion asc");
	
	protected $_defaultValues = array (
	    'Propia' => '1',
            'Produccion' => '0'
	);
	// ----------------------------------------------------

	// Inicio Public Init ----------------------------------------------------------------------------------------
    public function init()     {
		$this -> _validators = array(
		'Descripcion'=> array(
							array('Db_NoRecordExists',
									'Marcas',
									'Descripcion',
									array(	
										'field' => 'Id',
										'value' => "{Id}"
									)
							)
						)
		);
		parent::init();
	}
	
	// fin Public Init -------------------------------------------------------------------------------------------
	
}

?>