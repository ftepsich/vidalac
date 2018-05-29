<?php
/**
 * Model_DbTable_GruposDeUsuariosRoles
 *
 * Grupos de Usuarios Roles
 *
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_GruposDeUsuariosRoles
 * @extends     Rad_Db_Table_SemiReferencial
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_GruposDeUsuariosRoles extends Rad_Db_Table_SemiReferencial
{
    protected $_name = 'GruposDeUsuariosRoles';
	protected $_sort = array("Descripcion Asc");

    protected $_referenceMap    = array(
        
	    'GruposDeUsuarios' => array(
            'columns'           => 'GrupoDeUsuario',
            'refTableClass'     => 'Model_DbTable_GruposDeUsuarios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'GruposDeUsuarios',
            'refColumns'        => 'Id',
        ),
	    'Roles' => array(
            'columns'           => 'Rol',
            'refTableClass'     => 'Model_DbTable_Roles',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Roles',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}