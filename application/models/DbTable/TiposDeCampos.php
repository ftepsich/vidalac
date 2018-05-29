<?php
require_once('Rad/Db/Table.php');

class Model_DbTable_TiposDeCampos extends Rad_Db_Table
{
    protected $_name = "TiposDeCampos";

    public function init(){
        $this -> _validators = array(
            'Descripcion'=> array(
                array('Db_NoRecordExists',
                    'TiposDeCampos',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => '{Id}'
                    )
                )
            )
        );
        parent::init();
    }
}