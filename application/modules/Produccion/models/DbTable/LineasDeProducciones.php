<?php
class Produccion_Model_DbTable_LineasDeProducciones extends Rad_Db_Table
{
    protected $_name = 'LineasDeProducciones';
	
    protected $_referenceMap = array(
        'TiposDeLineasDeProducciones' => array(
            'columns' => 'TipoDeLineaDeProduccion',
            'refTableClass' => 'Produccion_Model_DbTable_TiposDeLineasDeProducciones',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeLineasDeProducciones',
            'refColumns' => 'Id',
        ),
        'Interdeposito' => array(
            'columns' => 'Interdeposito',
            'refTableClass' => 'Almacenes_Model_DbTable_Almacenes',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Interdeposito',
            'comboPageSize' => 20,
            'refTable' => 'Direcciones',
            'refColumns' => 'Id',
        )
    );

    protected $_dependentTables = array('Produccion_Model_DbTable_OrdenesDeProducciones');
}