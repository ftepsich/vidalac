<?php

require_once('Rad/Db/Table.php');

class Rrhh_Model_DbTable_TiposDeFeriados extends Rad_Db_Table
{
    protected $_name = 'TiposDeFeriados';

    protected $_dependentTables = array();	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeFeriados',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );

        parent::init();
    } 


}