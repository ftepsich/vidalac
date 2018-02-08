<?php
class Base_Model_DbTable_Empresas extends Rad_Db_Table
{
    protected $_name = 'Empresas';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Rrhh_Model_DbTable_Servicios');	
}