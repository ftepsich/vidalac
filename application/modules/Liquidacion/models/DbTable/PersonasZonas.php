<?php
class Liquidacion_Model_DbTable_PersonasZonas extends Rad_Db_Table
{
    protected $_name = 'PersonasZonas';

    protected $_referenceMap    = array(
        'AfipZonas' => array(
            'columns'           => 'AfipZona',
            'refTableClass'     => 'Afip_Model_DbTable_AfipZonas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipZonas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        ),
	    'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            
        )    
    );

}