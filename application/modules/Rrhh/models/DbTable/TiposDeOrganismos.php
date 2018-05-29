<?php
class Rrhh_Model_DbTable_TiposDeOrganismos extends Rad_Db_Table
{
    protected $_name = 'TiposDeOrganismos';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Rrhh_Model_DbTable_Organismos');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeOrganismos',
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