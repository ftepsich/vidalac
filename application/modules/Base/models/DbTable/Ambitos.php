<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_Ambitos extends Rad_Db_Table {

    protected $_name = "Ambitos";

    // Inicio Public Init ----------------------------------------------------------------------------------------
    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'Ambitos',
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