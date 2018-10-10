<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TelefonosSucursales extends  Base_Model_DbTable_Telefonos
{
	protected $_name = "Telefonos";
	
	
	// Inicio  protected $_referenceMap --------------------------------------------------------------------------
	protected $_referenceMap    = array(
 		'BancosSucursales' => array(
            'columns'           => 'BancoSucursal',
            'refTableClass'     => 'Base_Model_DbTable_BancosSucursales',
     		'refJoinColumns'    => array("Descripcion"),               
     		'comboBox'			=> true,                                 
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'BancosSucursales',
            'refColumns'        => 'Id'
        ),
		'TiposDeTelefonos' => array(
            'columns'           => 'TipoDeTelefono',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeTelefonos',
     		'refJoinColumns'    => array("Descripcion"),                    
     		'comboBox'			=> true,                                   
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeTelefonos',
            'refColumns'        => 'Id'
        )

	);
	// fin  protected $_referenceMap -----------------------------------------------------------------------------


	
	
	
}

?>