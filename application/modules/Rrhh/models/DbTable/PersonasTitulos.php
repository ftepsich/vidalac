<?php
class Rrhh_Model_DbTable_PersonasTitulos extends Rad_Db_Table
{
    protected $_name = 'PersonasTitulos';

    protected $_referenceMap    = array(
        'Titulos' => array(
            'columns'           => 'Titulo',
            'refTableClass'     => 'Rrhh_Model_DbTable_Titulos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Titulos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        ),
	    'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        )    
    );

}