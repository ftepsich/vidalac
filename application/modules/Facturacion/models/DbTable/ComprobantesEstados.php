<?php
class Facturacion_Model_DbTable_ComprobantesEstados extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = 'ComprobantesEstados';

	// Relaciones
    protected $_referenceMap    = array(
            );
	
	protected $_dependentTables = array('Facturacion_Model_DbTable_Comprobantes');	
	
}