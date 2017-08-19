<?php
require_once 'Rad/Db/Table.php';

/**
 * Model_DbTable_ProveedoresListasDePrecios
 *
 * Listas de Precios de los Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Model_DbTable_ProveedoresListasDePrecios
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ProveedoresMarcas extends Rad_Db_Table
{

    protected $_name = 'ProveedoresMarcas';
    Protected $_sort = array('Descripcion asc', 'Proveedor asc');
    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Proveedor',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array("RazonSocial"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        ),
        'Marca' => array(
            'columns'           => 'Marca',
            'refTableClass'     => 'Base_Model_DbTable_MarcasDeTerceros',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Marcas',
            'refColumns'        => 'Id'
        )
    );

}