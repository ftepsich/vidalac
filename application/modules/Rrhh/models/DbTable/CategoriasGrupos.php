<?php
require_once('Rad/Db/Table.php');

class Rrhh_Model_DbTable_CategoriasGrupos extends Rad_Db_Table
{
    protected $_name = 'CategoriasGrupos';

    protected $_sort = 'Descripcion ASC';

    protected $_referenceMap    = array(
        
	    'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_ConveniosCategorias');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'CategoriasGrupos',
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