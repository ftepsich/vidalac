<?php
class Liquidacion_Model_DbTable_TiposDeVariables extends Rad_Db_Table
{
    protected $_name = 'TiposDeVariables';

    protected $_sort = array('Descripcion asc'); 

    protected $_dependentTables = array('Liquidacion_Model_DbTable_VariablesAbstractas');	
}