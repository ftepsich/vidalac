<?php
/**
 * Produccion_Model_DbTable_LineasDeProduccionesActividades
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_LineasDeProduccionesActividades
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_LineasDeProduccionesActividades extends Rad_Db_Table
{
    protected $_name = 'LineasDeProduccionesActividades';

    protected $_referenceMap    = array(
        
	    'Actividades' => array(
            'columns'           => 'Actividad',
            'refTableClass'     => 'Produccion_Model_DbTable_Actividades',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Actividades',
            'refColumns'        => 'Id',
        ),
	    'ActividadesConfiguraciones' => array(
            'columns'           => 'ActividadConfiguracion',
            'refTableClass'     => 'Produccion_Model_DbTable_ActividadesConfiguraciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'ActividadesConfiguraciones',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}