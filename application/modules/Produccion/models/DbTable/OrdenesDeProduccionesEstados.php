<?php

/**
 * Produccion_Model_DbTable_OrdenesDeProduccionesEstados
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_OrdenesDeProduccionesEstados
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_OrdenesDeProduccionesEstados extends Rad_Db_Table
{
    protected $_name = 'OrdenesDeProduccionesEstados';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Produccion_Model_DbTable_OrdenesDeProducciones');
}