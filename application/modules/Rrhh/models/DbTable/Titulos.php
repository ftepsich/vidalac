<?php
class Rrhh_Model_DbTable_Titulos extends Rad_Db_Table
{
    protected $_name = 'Titulos';

    protected $_referenceMap    = array(
        'TiposDeTitulos' => array(
            'columns'           => 'TipoDeTitulo',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeTitulos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeTitulos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        ),
	    'TitulosNivelesAcademicos' => array(
            'columns'           => 'TituloNivelAcademico',
            'refTableClass'     => 'Rrhh_Model_DbTable_TitulosNivelesAcademicos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TitulosNivelesAcademicos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        )    
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasTitulos');	
}