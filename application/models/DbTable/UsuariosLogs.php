<?php
require_once('Rad/Db/Table.php');
/**
 * Model_DbTable_UsuariosLogs
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_UsuariosLogs
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_UsuariosLogs extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "UsuariosLogs";
	protected $_sort = "Id desc";

	// Relaciones
    protected $_referenceMap    = array(
        
	        'Usuarios' => array(
            'columns'           => 'Usuario',
            'refTableClass'     => 'Model_DbTable_Usuarios',
     		'refJoinColumns'    => array('Nombre'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Usuarios',
            'refColumns'        => 'Id',
        )    
	);
	
	protected $_dependentTables = array();	
	
	public function insert($data)
	{
		throw new Rad_Dbtable_Exception('No pude editar el log');
	}
	
	public function update($data, $where)
	{
		throw new Rad_Dbtable_Exception('No pude editar el log');
	}
	
	public function delete($where)
	{
		throw new Rad_Dbtable_Exception('No pude editar el log');
	}
	
}