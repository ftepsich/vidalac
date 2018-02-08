<?php
require_once('Rad/Db/Table.php');

class Almacenes_Model_DbTable_MmisTipos extends Rad_Db_Table
{
	protected $_name = "MmisTipos";

	public function init()     {
			$this ->_validators = array(
				'Descripcion'=> array(	
                    array(
                        'Db_NoRecordExists',
                        'MmisTipos',
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