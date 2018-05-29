<?php

/**
 * Model_DbTable_UsuariosConfiguracionesEscritorios
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_UsuariosConfiguracionesEscritorios
 * @extends     Rad_Db_Table_SemiReferencial
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_UsuariosConfiguracionesEscritorios extends Rad_Db_Table
{
    protected $_name = 'UsuariosConfiguracionesEscritorios';

    protected $_referenceMap    = array(
        
	    'Usuarios' => array(
            'columns'           => 'Usuario',
            'refTableClass'     => 'Model_DbTable_Usuarios',
            'refJoinColumns'    => array('Nombre'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Usuarios',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}