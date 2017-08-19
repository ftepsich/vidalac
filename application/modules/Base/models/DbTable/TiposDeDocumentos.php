<?php
class Base_Model_DbTable_TiposDeDocumentos extends Rad_Db_Table
{
    protected $_name = 'TiposDeDocumentos';
    protected $_sort = array ('Descripcion ASC');
    protected $_referenceMap    = array(
        'AfipTiposDeDocumentos' => array(
            'columns'           => 'Afip',
            'refTableClass'     => 'Afip_Model_DbTable_AfipTiposDeDocumentos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipTiposDeDocumentos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array(
		'Model_DbTable_Personas',
		'Model_DbTable_Clientes',
		'Model_DbTable_Proveedores',
	);
}
