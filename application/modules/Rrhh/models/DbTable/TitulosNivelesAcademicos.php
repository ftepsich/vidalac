<?php
class Rrhh_Model_DbTable_TitulosNivelesAcademicos extends Rad_Db_Table
{
    protected $_name = 'TitulosNivelesAcademicos';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Rrhh_Model_DbTable_Titulos');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TitulosNivelesAcademicos',
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