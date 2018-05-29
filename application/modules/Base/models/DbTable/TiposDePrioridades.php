<?php
class Base_Model_DbTable_TiposDePrioridades extends Rad_Db_Table
{
    protected $_name = 'TiposDePrioridades';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Produccion_Model_DbTable_OrdenesDeProducciones');
}