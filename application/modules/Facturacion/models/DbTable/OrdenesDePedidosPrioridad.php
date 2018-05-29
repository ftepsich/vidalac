<?php
require_once('Rad/Db/Table.php');

class Model_DbTable_OrdenesDePedidosPrioridad extends Rad_Db_Table
{
	protected $_name = "OrdenesDePedidosPrioridad";
	
	// Inicio Public Init ----------------------------------------------------------------------------------------
	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(	
									array(	'Db_NoRecordExists',
											'OrdenesDePedidosPrioridad',
											'Descripcion',
											array(	
												'field' => 'Id',
												'value' => "{Id}"
											)
									)
								),
			);
			
		parent::init();
	}
	// fin Public Init -------------------------------------------------------------------------------------------	

	
}

?>