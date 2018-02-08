<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ArticulosGrupos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "ArticulosGrupos";

	// Relaciones
    protected $_referenceMap    = array(
        
	        'TiposDeArticulos' => array(
            'columns'           => 'TipoDeArticulo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeArticulos',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeArticulos',
            'refColumns'        => 'Id',
        )    
    );
	
	protected $_dependentTables = array('Base_Model_DbTable_Articulos',
										'Base_Model_DbTable_ArticulosSubGrupos');	
	
}