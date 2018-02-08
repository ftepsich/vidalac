<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_PersonasActividades
 *
 * Actividades de la Personas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_PersonasActividades
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasActividades extends Rad_Db_Table
{

    protected $_name = 'PersonasActividades';
    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            //'comboBox'          => true,
            //'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        ),
        'CodigosActividadesAfip' => array(
            'columns'           => 'CodigoActividad',
            'refTableClass'     => 'Base_Model_DbTable_CodigosActividadesAfip',
            'refJoinColumns'    => array('Descripcion','Codigo'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'CodigosActividadesAfip',
            'refColumns'        => 'Id',
			'comboPageSize'     => 20
        )
		
    );
    protected $_dependentTables = array();

}