<?php

/**
 * Model_DbTable_GruposDeUsuarios
 *
 * Grupos de Usuarios
 *
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_GruposDeUsuarios
 * @extends     Rad_Db_Table_SemiReferencial
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_GruposDeUsuarios extends Rad_Db_Table_SemiReferencial
{
    protected $_name = 'GruposDeUsuarios';
	protected $_sort = array("Descripcion");

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Model_DbTable_GruposDeUsuariosRoles','Model_DbTable_Usuarios');	
}