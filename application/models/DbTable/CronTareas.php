<?php
class Model_DbTable_CronTareas extends Rad_Db_Table
{
    protected $_name = 'CronTareas';

    protected $_referenceMap    = array();

    protected $_dependentTables = array('Model_DbTable_CronProgramaciones');	
}