<?php
require_once 'Rad/Db/Table.php';
/**
 * Model_DbTable_Usuarios
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_Usuarios
 * @extends     Rad_Db_Table_SemiReferencial
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_Usuarios extends Rad_Db_Table_SemiReferencial
{
    // Tabla mapeada
    protected $_name = 'Usuarios';
    protected $_sort = array('Nombre ASC');

    // Relaciones
    protected $_referenceMap    = array(   
        'GruposDeUsuarios' => array(
            'columns'           => 'GrupoDeUsuario',
            'refTableClass'     => 'Model_DbTable_GruposDeUsuarios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'GruposDeUsuarios',
            'refColumns'        => 'Id',
        )   
    );
    
    //protected $_dependentTables = array('Model_DbTable_UsuariosConfiguraciones'); 
    
}