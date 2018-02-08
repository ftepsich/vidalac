<?php
class Rrhh_Model_DbTable_ModalidadesDeContrataciones extends Rad_Db_Table
{
    protected $_name = 'ModalidadesDeContrataciones';

    protected $_sort = array('Descripcion asc');    

    protected $_dependentTables = array('Rrhh_Model_DbTable_Servicios');

    protected $_defaultValues = array(
        'Activo' => 1 
    );  

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'ModalidadesDeContrataciones',
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
        $condicion = "ModalidadesDeContrataciones.Activo = 1 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }    

}