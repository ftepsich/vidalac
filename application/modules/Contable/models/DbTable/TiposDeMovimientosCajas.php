<?php
require_once('Rad/Db/Table.php');

class Contable_Model_DbTable_TiposDeMovimientosCajas extends Rad_Db_Table
{
	protected $_name = "TiposDeMovimientosCajas";
	
	public function init()     {
        $this -> _validators = array(
            'Descripcion'=> array(	
                array(	'Db_NoRecordExists',
                        'TiposDeMovimientosCajas',
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