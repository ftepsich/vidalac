<?php
require_once('Rad/Db/Table.php');

/**
 * Model_DbTable_RolesModelos
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Model_DbTable_RolesModelos
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_RolesModelos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "RolesModelos";
	protected $_sort = array("Descripcion Asc");

	// Relaciones
    protected $_referenceMap    = array(
        
	    'Roles' => array(
            'columns'           => 'Rol',
            'refTableClass'     => 'Model_DbTable_Roles',
            'refTable'			=> 'Roles',
            'refColumns'        => 'Id',
        ),
		'Modelos' => array(
            'columns'           => 'Modelo',
            'refTableClass'     => 'Model_DbTable_Modelos',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Modelos',
            'refColumns'        => 'Id',
        )
		
	);
	
	protected $_dependentTables = array();	
	
}