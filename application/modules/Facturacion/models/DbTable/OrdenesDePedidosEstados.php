<?php
require_once('Rad/Db/Table.php');

class Model_DbTable_OrdenesDePedidosEstados extends Rad_Db_Table
{
	protected $_name = "OrdenesDePedidosEstados";
	
	// Inicio Public Init ----------------------------------------------------------------------------------------
	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(	
									array(	'Db_NoRecordExists',
											'OrdenesDePedidosEstados',
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