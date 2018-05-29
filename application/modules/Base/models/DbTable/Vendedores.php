<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Vendedores
 *
 * Vendedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Vendedores
 * @extends Base_Model_DbTable_Personas
 */
class Base_Model_DbTable_Vendedores extends Base_Model_DbTable_Personas
{

    protected $_permanentValues = array('EsVendedor' => 1);

    protected $_referenceMap = array(
        'Sexos' => array(
            'columns'           => 'Sexo',
            'refTableClass'     => 'Base_Model_DbTable_Sexos',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Sexos',
            'refColumns'        => 'Id'
        ),
        'EstadosCiviles' => array(
            'columns'           => 'EstadoCivil',
            'refTableClass'     => 'Base_Model_DbTable_EstadosCiviles',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'EstadosCiviles',
            'refColumns'        => 'Id'
        )
    );

}