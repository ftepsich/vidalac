<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ChequesBloqueosTipos extends Rad_Db_Table
{
	protected $_name = "ChequesBloqueosTipos";

	public function init()     {
	 
		$this -> _validators = array(
			'Descripcion'=> array(
							array('Db_NoRecordExists',
									'ChequesBloqueosTipos',
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
}

?>