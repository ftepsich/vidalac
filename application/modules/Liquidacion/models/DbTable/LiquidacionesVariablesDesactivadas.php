<?php
class Liquidacion_Model_DbTable_LiquidacionesVariablesDesactivadas extends Rad_Db_Table
{
    protected $_name = 'LiquidacionesVariablesDesactivadas';

    protected $_sort = array('Descripcion asc');    

    protected $_referenceMap    = array(
        
	    'Variables' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Variables',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Variables',
            'refColumns'        => 'Id',
        ),
	    'Liquidaciones' => array(
            'columns'           => 'Liquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Liquidaciones',
            'refJoinColumns'    => array('e'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Liquidaciones',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}