<?php
class Rrhh_Model_DbTable_TiposDeSueldos extends Rad_Db_Table
{
    protected $_name = 'TiposDeSueldos';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array(
    		Rrhh_Model_DbTable_SituacionesDeRevistas
    		);	
}