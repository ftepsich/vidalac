<?php
require_once('Rad/Db/Table.php');

class Facturacion_Model_DbTable_PedidosDeCotizacionesArticulos extends Facturacion_Model_DbTable_ComprobantesDetalles
{
	protected $_name = "ComprobantesDetalles";
	
	protected $_validators = array(	"Articulo" => array("NotEmpty","Int"));
	
	// Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosGenericos',
     		'refJoinColumns'    => array("Descripcion","DescArreglada" =>"IF(ComprobantesDetalles.Articulo is null,ComprobantesDetalles.Observaciones,Articulos.Descripcion)", 'Tipo'),                
     		'comboBox'			=> true,                                    
     		'comboSource'		=> 'datagateway/combolist/fetch/EsArticuloParaCompra',
            'refTable'			=> 'Articulos',
            'refColumns'        => 'Id',
			'comboPageSize'		=> 10	
        ),
        'PedidosDeCotizaciones' => array(
            'columns'           => 'Comprobante',
            'refTableClass'     => 'Facturacion_Model_DbTable_PedidosDeCotizaciones',
     		'refJoinColumns'    => array("Observaciones"),                 
     		'comboBox'			=> true,                                     
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Comprobantes',
            'refColumns'        => 'Id',
        )
);
	// fin  protected $_referenceMap -----------------------------------------------------------------------------

	
	
}

?>
