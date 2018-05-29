<?php
class Rrhh_Model_DbTable_FamiliaresPersonas extends Rad_Db_Table
{
    protected $_name = 'FamiliaresPersonas';

    protected $_referenceMap    = array(
        
	    'TiposDeEscolaridades' => array(
            'columns'           => 'TipoDeEscolaridad',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeEscolaridades',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeEscolaridades',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'PersonasEmpleados' => array(
            'columns'           => 'PersonaEmpleado',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'Personas' => array(
            'columns'           => 'PersonaFamiliar',
            'refTableClass'     => 'Rrhh_Model_DbTable_Familiares',
            'refJoinColumns'    => array('RazonSocial','Denominacion','Dni','FechaNacimiento'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'TiposDeFamiliares' => array(
            'columns'           => 'TipoDeFamiliar',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeFamiliares',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeFamiliares',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasAfiliacionesAdherentes');	
}