<?php
class Rrhh_Model_DbTable_TiposDeJornadas extends Rad_Db_Table
{
    protected $_name = 'TiposDeJornadas';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap    = array(
        
        'TiposDeSueldos' => array(
            'columns'           => 'TipoDeSueldo',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeSueldos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeSueldos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        )    
    );    

    protected $_defaultValues = array(
        'Activo' => 1 
    );      
    
    protected $_dependentTables = array('Rrhh_Model_DbTable_Servicios');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeJornadas',
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