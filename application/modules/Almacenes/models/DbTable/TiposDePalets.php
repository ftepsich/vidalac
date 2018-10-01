<?php
require_once('Rad/Db/Table.php');
/**
 * Tipos de Palets
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 * @class 		Almacenes_Model_DbTable_TiposDePalets
 * @extends		Rad_Db_Table
 */
class Almacenes_Model_DbTable_TiposDePalets extends Rad_Db_Table
{
	protected $_name = "TiposDePalets";

    public function init()     {
        $this -> _validators = array(
            'Descripcion'=> array(
                array(
                    'Db_NoRecordExists',
                    'TiposDePalets',
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