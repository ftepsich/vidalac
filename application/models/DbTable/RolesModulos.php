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
class Model_DbTable_RolesModulos extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "RolesModulos";
	protected $_sort = array("Descripcion Asc");

	// Relaciones
    protected $_referenceMap    = array(
        
	        'Roles' => array(
            'columns'           => 'Rol',
            'refTableClass'     => 'Model_DbTable_Roles',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Roles',
            'refColumns'        => 'Id',
        ),
	        'Modulos' => array(
            'columns'           => 'Modulo',
            'refTableClass'     => 'Model_DbTable_Modulos',
     		'refJoinColumns'    => array('Nombre'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Modulos',
            'refColumns'        => 'Id',
        )    );
	
	protected $_dependentTables = array();	
	
}