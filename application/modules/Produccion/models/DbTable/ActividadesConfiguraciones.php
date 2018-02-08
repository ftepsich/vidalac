<?php
class Produccion_Model_DbTable_ActividadesConfiguraciones extends Rad_Db_Table
{
    protected $_name = 'ActividadesConfiguraciones';

    protected $_referenceMap    = array(
	
		'TiposDeLineasDeProducciones' => array(
            'columns'           => 'TipoDeLineaDeProduccion',
            'refTableClass'     => 'Produccion_Model_DbTable_TiposDeLineasDeProducciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeLineasDeProducciones',
            'refColumns'        => 'Id',
        )		
	);
			
			

    protected $_dependentTables = array('Produccion_Model_DbTable_OrdenesDeProducciones');	
}