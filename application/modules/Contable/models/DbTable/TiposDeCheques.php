<?php
require_once('Rad/Db/Table.php');

class Contable_Model_DbTable_TiposDeCheques extends Rad_Db_Table
{
	protected $_name = "TiposDeCheques";
	
	// Inicio Public Init ----------------------------------------------------------------------------------------
	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(	
									array(	'Db_NoRecordExists',
											'TiposDeCheques',
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