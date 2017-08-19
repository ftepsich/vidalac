<?php

/**
 * Facturacion_Model_DbTable_TiposDeGruposDeComprobantes
 *
 * Tipos de Grupos de Comprobantes
 *
 * @copyright SmartSoftware Argentina
 * @class Facturacion_Model_DbTable_TiposDeGruposDeComprobantes
 * @extends Rad_Db_Table
 * @package Aplicacion
 * @subpackage Facturacion
 */
class Facturacion_Model_DbTable_TiposDeGruposDeComprobantes extends Rad_Db_Table
{
    protected $_name = 'TiposDeGruposDeComprobantes';

    protected $_dependentTables = array('Facturacion_Model_DbTable_TiposDeComprobantes');	
}