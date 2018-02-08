<?php
class Contable_Model_DbTable_LibrosDiariosDetalle extends Rad_Db_Table
{
    protected $_name = 'LibrosDiariosDetalle';

    protected $_referenceMap    = array(
        
	    'LibrosDiarios' => array(
            'columns'           => 'Asiento',
            'refTableClass'     => 'Contable_Model_DbTable_LibrosDiarios',
            'refJoinColumns'    => array('e'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'LibrosDiarios',
            'refColumns'        => 'Id',
        ),
	    'PlanesDeCuentas' => array(
            'columns'           => 'Cuenta',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'PlanesDeCuentas',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}