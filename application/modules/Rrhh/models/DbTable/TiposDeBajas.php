<?php
class Rrhh_Model_DbTable_TiposDeBajas extends Rad_Db_Table
{
    protected $_name = 'TiposDeBajas';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Rrhh_Model_DbTable_Servicios');	

    protected $_referenceMap    = array(
        'SituacionesDeRevistas' => array(
            'columns'           => 'SituacionDeRevista',
            'refTableClass'     => 'Rrhh_Model_DbTable_SituacionesDeRevistas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Bajas',
            'refTable'          => 'SituacionesDeRevistas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeBajas',
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