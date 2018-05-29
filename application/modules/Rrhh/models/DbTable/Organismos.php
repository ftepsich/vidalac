<?php
class Rrhh_Model_DbTable_Organismos extends Rad_Db_Table
{
    protected $_name = 'Organismos';

    protected $_sort = array('Descripcion asc');    

    protected $_referenceMap    = array(
        
	    'TiposDeOrganismos' => array(
            'columns'           => 'TipoDeOrganismo',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeOrganismos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeOrganismos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        )    
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasAfiliaciones');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'Organismos',
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

    public function fetchActivo($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Organismos.Activo = 1 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }       
    
}