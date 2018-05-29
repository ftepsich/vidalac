<?php
class Liquidacion_Model_DbTable_ConceptosTiposDeLiquidaciones extends Rad_Db_Table
{
    protected $_name = 'ConceptosTiposDeLiquidaciones';

    protected $_referenceMap    = array(
        
	    'TiposDeLiquidaciones' => array(
            'columns'           => 'TipoDeLiquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeLiquidaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeLiquidaciones',
            'refColumns'        => 'Id',
        ),
	    'VariablesDetalles' => array(
            'columns'           => 'Concepto',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesDetalles',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'VariablesDetalles',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	
}