<?php
require_once('Rad/Db/Table.php');
/**
 * Model_DbTable_ModulosModelos
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Model_DbTable_ModulosModelos
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_ModulosModelos extends Rad_Db_Table
{
    // Tabla mapeada
    protected $_name = "ModulosModelos";
    protected $_sort = array("Descripcion Asc");

    // Relaciones
    protected $_referenceMap    = array(
        
            'Modelos' => array(
            'columns'           => 'Modelo',
            'refTableClass'     => 'Model_DbTable_Modelos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Modelos',
            'refColumns'        => 'Id',
        ),
            'Modulos' => array(
            'columns'           => 'Modulo',
            'refTableClass'     => 'Model_DbTable_Modulos',
            'refTable'          => 'Modulos',
            'refColumns'        => 'Id',
        )
        );
    
    protected $_dependentTables = array();  
    
}