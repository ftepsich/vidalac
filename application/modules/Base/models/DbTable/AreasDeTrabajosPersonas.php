<?php
class Base_Model_DbTable_AreasDeTrabajosPersonas extends Rad_Db_Table
{
    protected $_name = 'AreasDeTrabajosPersonas';

    protected $_referenceMap    = array(
        
	    'Empleados' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Personas',
            'refColumns'        => 'Id',
        ),
	    'AreasDeTrabajos' => array(
            'columns'           => 'AreaDeTrabajo',
            'refTableClass'     => 'Base_Model_DbTable_AreasDeTrabajos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'AreasDeTrabajos',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}