<?php
class Base_Model_DbTable_AreasDeTrabajos extends Rad_Db_Table_SemiReferencial
{
    protected $_name = 'AreasDeTrabajos';

    protected $_dependentTables = array('Base_Model_DbTable_AreasDeTrabajosPersonas');

    protected $_mensajeError = 'El Area Produccion no puede modificarse.<br>Es un Area reservada del sistema.';
    
    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'AreasDeTrabajos',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            )
        );

        parent::init();
    }
    
}