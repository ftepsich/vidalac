<?php
require_once('Rad/Db/Table.php');

class Almacenes_Model_DbTable_MmisAcciones extends Rad_Db_Table
{
    protected $_name = "MmisAcciones";

    public function init()     {
            // Controlo que no existan dos iguales
            $this ->_validators = array(
                'Descripcion'=> array(  
                    array(
                        'Db_NoRecordExists',
                        'Mmisacciones',
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