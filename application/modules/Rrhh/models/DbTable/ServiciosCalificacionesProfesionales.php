<?php
class Rrhh_Model_DbTable_ServiciosCalificacionesProfesionales extends Rad_Db_Table
{
    protected $_name = 'ServiciosCalificacionesProfesionales';

    protected $_sort = array('Descripcion asc');    

    protected $_dependentTables = array('Rrhh_Model_DbTable_Servicios');	

    /*
    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'ServiciosCalificacionesProfesionales',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );
    }
    */
}