<?php
require_once 'Rad/Db/Table.php';
/**
 *
 * Base_Model_DbTable_AdaptadoresFiscalizaciones
 *
 * Adaptadores Fiscalizaciones
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_AdaptadoresFiscalizaciones
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_AdaptadoresFiscalizaciones extends Rad_Db_Table
{
    protected $_name = 'AdaptadoresFiscalizaciones';

    protected $_dependentTables = array('Model_DbTable_PuntosDeVentas');	
}