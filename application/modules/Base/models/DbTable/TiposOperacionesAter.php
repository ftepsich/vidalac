<?php

require_once 'Rad/Db/Table.php';

class Base_Model_DbTable_TiposOperacionesAter extends Rad_Db_Table
{

    protected $_name = "TiposOperacionesAter";
    protected $_sort = array("Codigo asc");
    protected $_defaultSource = self::DEFAULT_CLASS;
}
