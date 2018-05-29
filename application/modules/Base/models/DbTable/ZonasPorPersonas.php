<?php

/**
 * Base_Model_DbTable_ZonasPorPersonas
 *
 * Zonas de Ventas por Persona
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ZonasPorPersonas
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ZonasPorPersonas extends Rad_Db_Table
{

    protected $_name = 'ZonasPorPersonas';

    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        ),
        'ZonasDeVentas' => array(
            'columns'           => 'ZonaDeVenta',
            'refTableClass'     => 'Base_Model_DbTable_ZonasDeVentas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ZonasDeVentas',
            'refColumns'        => 'Id'
        ));

}