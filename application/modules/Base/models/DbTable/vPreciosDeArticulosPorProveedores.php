<?php
require_once('Rad/Db/Table.php');

class Model_DbTable_VPreciosDeArticulosPorProveedores extends Rad_Db_Table
{
	protected $_name = "VPreciosDeArticulosPorProveedores";

	protected $_primary = "Id";

	protected $_sort = array ("Lista asc","PorCantidad asc");
}

?>