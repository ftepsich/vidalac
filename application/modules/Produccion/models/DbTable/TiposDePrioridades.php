<?php

/**
 * Produccion_Model_DbTable_TiposDeLineasDeProducciones
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_TiposDeLineasDeProducciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Model_DbTable_TiposDePrioridades extends Rad_Db_Table
{
    protected $_name = 'TiposDePrioridades';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Model_DbTable_OrdenesDeProducciones');	
}