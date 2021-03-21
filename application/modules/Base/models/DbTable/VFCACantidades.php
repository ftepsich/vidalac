<?php
class Model_DbTable_VFCACantidades extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = 'VFCACantidades';

	protected $_primary = "Id";	
	
	// Relaciones
    protected $_referenceMap    = array(
		'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Model_DbTable_Articulos',
     		'refColumns'        => 'Id',
     		'refJoinColumns'    => array("Descripcion"),                     
     		'comboBox'			=> true,                                    -
     		'comboSource'		=> 'datagateway/combolist/fetch/EsArticuloParaCompra',
            'refTable'			=> 'Productos',
    		'comboPageSize'		=>	10
        ),
		'FacturasCompras' => array(
            'columns'           => 'FacturaCompra',
            'refTableClass'     => 'Model_DbTable_FacturasCompras',
     		'refColumns'        => 'Id',
     		'refJoinColumns'    => array("FacturaNumero"),                    
     		'comboBox'			=> true,                                     
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'FacturasCompras',
    		'comboPageSize'		=>	10
        )		
    );
	
	protected $_dependentTables = array();	
	
}