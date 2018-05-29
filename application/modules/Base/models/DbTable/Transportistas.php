<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_Transportistas extends Base_Model_DbTable_Personas
{
    protected $_name = "Personas";
    Protected $_sort = array("RazonSocial asc");
	protected $_permanentValues = array('EsTransporte' => 1);
}

?>