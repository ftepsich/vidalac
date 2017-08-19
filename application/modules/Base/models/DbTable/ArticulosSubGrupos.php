<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ArticulosSubGrupos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "ArticulosSubGrupos";

	// Relaciones
    protected $_referenceMap    = array(
        
	        'ArticulosGrupos' => array(
            'columns'           => 'ArticuloGrupo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosGrupos',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'ArticulosGrupos',
            'refColumns'        => 'Id',
        )    
    );
	
	protected $_dependentTables = array('Base_Model_DbTable_Articulos');	
	
}