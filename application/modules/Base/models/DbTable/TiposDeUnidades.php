<?php

require_once('Rad/Db/Table.php');


/**
 *
 * Base_Model_DbTable_TiposDeUnidades
 *
 * Unidades De Medidas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_TiposDeUnidades
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_TiposDeUnidades extends Rad_Db_Table
{

    protected $_name = "TiposDeUnidades";
    protected $_sort = array("Descripcion asc");

    
}