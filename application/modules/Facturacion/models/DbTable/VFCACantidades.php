<?php
class Facturacion_Model_DbTable_VFCACantidades extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = 'VFCACantidades';

	protected $_primary = "Id";	
	
	protected $_gridGroupField = "FacturaCompraArticulo";		
	
	// Relaciones
    protected $_referenceMap    = array(
		'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_Articulos',
     		'refColumns'        => 'Id',
     		'refJoinColumns'    => array("Descripcion"),                     // De esta relacion queremos traer estos campos por JOIN
     		'comboBox'			=> true,                                    
     		'comboSource'		=> 'datagateway/combolist/fetch/EsArticuloParaCompra',
            'refTable'			=> 'Articulos',
    		'comboPageSize'		=>	10
        ),
		'FacturasCompras' => array(
            'columns'           => 'FacturaCompra',
            'refTableClass'     => 'Facturacion_Model_DbTable_FacturasCompras',
     		'refColumns'        => 'Id',
     		'refJoinColumns'    => array("Numero"),                     // De esta relacion queremos traer estos campos por JOIN
     		'comboBox'			=> true,                                    
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Comprobantes',
    		'comboPageSize'		=>	10
        )		
    );
	
	protected $_dependentTables = array();	
	
}