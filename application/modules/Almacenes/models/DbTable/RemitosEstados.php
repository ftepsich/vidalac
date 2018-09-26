<?php
require_once('Rad/Db/Table.php');
/**
 * Remitos Estados
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 * @class 		Almacenes_Model_DbTable_RemitosEstados
 * @extends		Rad_Db_Table
 */
class Almacenes_Model_DbTable_RemitosEstados extends Rad_Db_Table {

    protected $_name = "RemitosEstados";

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'RemitosEstados',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => ($_POST["Id"]) ? $_POST["Id"] : ($_POST["node"] ? $_POST["node"] : 0)
                    )
                )
            )
        );

        parent::init();
    }
}