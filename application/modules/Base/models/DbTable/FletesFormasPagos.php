<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_FletesFormasPagos extends Rad_Db_Table
{
    protected $_name = "FletesFormasPagos";

    // Inicio Public Init ----------------------------------------------------------------------------------------
    public function init()     {
            $this -> _validators = array(
                'Descripcion'=> array(
                    array(  'Db_NoRecordExists',
                            'FletesTiposPagos',
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