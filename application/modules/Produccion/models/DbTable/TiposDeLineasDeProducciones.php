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
class Produccion_Model_DbTable_TiposDeLineasDeProducciones extends Rad_Db_Table
{
    protected $_name = 'TiposDeLineasDeProducciones';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Produccion_Model_DbTable_LineasDeProducciones','Produccion_Model_DbTable_ActividadesConfiguraciones');	
}