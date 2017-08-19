<?php
require_once 'Rad/Db/Table.php';

/**
 * Model_DbTable_ProveedoresModalidadesDePagos
 *
 * Modalidades de Pagos de los Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Model_DbTable_ProveedoresModalidadesDePagos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ProveedoresModalidadesDePagos extends Rad_Db_Table
{

    protected $_name = 'PersonasModalidadesDePagos';

    protected $_referenceMap = array(
        'ModalidadesDePagos' => array(
            'columns'           => 'ModalidadDePago',
            'refTableClass'     => 'Base_Model_DbTable_ModalidadesDePagos',
            'refColumns'        => 'Id',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ModalidadesDePagos'
        ),
        'Proveedores' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns'    => array("RazonSocial"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        )
    );

}