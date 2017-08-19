<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ProductosCategoriasCaracteristicasValores
 *
 * Productos Categorias Caracteristicas Valores
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ProductosCategoriasCaracteristicasValores
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ProductosCategoriasCaracteristicasValores extends Rad_Db_Table
{

    protected $_name = 'ProductosCategoriasCaracteristicasValores';
    protected $_referenceMap = array(
        'Productos' => array(
            'columns'           => 'Producto',
            'refTableClass'     => 'Base_Model_DbTable_Productos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Productos',
            'refColumns'        => 'Id'
        ),
        'ProductosCategoriasCaracteristicas' => array(
            'columns'           => 'ProductoCategoriaCaracteristica',
            'refTableClass'     => 'Base_Model_DbTable_ProductosCategoriasCaracteristicas',
            'refJoinColumns'    => array('n'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ProductosCategoriasCaracteristicas',
            'refColumns'        => 'Id'
        ));
    protected $_dependentTables = array();

}