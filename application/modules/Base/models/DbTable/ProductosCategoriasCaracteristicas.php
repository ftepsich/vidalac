<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ProductosCategoriasCaracteristicas
 *
 * Productos Categorias caracteristicas
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ProductosCategoriasCaracteristicas
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ProductosCategoriasCaracteristicas extends Rad_Db_Table
{

    protected $_name = "ProductosCategoriasCaracteristicas";
    protected $_referenceMap = array(
        'ProductosCategorias' => array(
            'columns'           => 'ProductoCategoria',
            'refTableClass'     => 'Base_Model_DbTable_ProductosCategorias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ProductosCategorias',
            'refColumns'        => 'Id'
        ),
        'Caracteristicas' => array(
            'columns'           => 'Caracteristica',
            'refTableClass'     => 'Base_Model_DbTable_Caracteristicas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Caracteristicas',
            'refColumns'        => 'Id',
        ));
    protected $_dependentTables = array('Base_Model_DbTable_ProductosCategoriasCaracteristicasValores');

}