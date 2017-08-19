<?php
class Base_Model_DbTable_TiposDeRegistrosDePrecios extends Rad_Db_Table
{
    protected $_name = 'TiposDeRegistrosDePrecios';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Base_Model_DbTable_PersonasRegistrosDePrecios');	
}