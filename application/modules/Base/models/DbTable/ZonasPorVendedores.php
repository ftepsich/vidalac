<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ZonasPorVendedores
 *
 * Zonas por Vendedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ZonasPorVendedores
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ZonasPorVendedores extends Rad_Db_Table
{

    protected $_name = 'ZonasPorPersonas';

    protected $_referenceMap = array(
        'ZonasDeVentas' => array(
            'columns'           => 'ZonaDeVenta',
            'refTableClass'     => 'Base_Model_DbTable_ZonasDeVentas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ZonasDeVentas',
            'refColumns'        => 'Id'
         ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array("Denominacion"),
            //'comboBox'          => true,
            //'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        )
    );
}