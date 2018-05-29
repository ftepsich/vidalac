<?php
class Rrhh_Model_DbTable_TiposDeEscolaridades extends Rad_Db_Table
{
    protected $_name = 'TiposDeEscolaridades';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Rrhh_Model_DbTable_Familiares');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeEscolaridades',
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