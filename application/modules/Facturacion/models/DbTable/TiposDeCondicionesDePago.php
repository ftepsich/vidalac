<?php
class Facturacion_Model_DbTable_TiposDeCondicionesDePago extends Rad_Db_Table
{
    protected $_name = 'TiposDeCondicionesDePago';

    protected $_dependentTables = array('Facturacion_Model_DbTable_Comprobantes');	
}