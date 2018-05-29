<?php
class Rrhh_Model_DbTable_TiposDeHorasExtras extends Rad_Db_Table
{
    protected $_name = 'TiposDeHorasExtras';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap    = array(

        'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );    

    protected $_dependentTables = array('Rrhh_Model_DbTable_ServiciosHorasExtras');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeHorasExtras',
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