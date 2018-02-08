<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_Variables_Variables
 *
 * Variables Generales
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_Variables_Variables
 * @extends Liquidacion_Model_DbTable_VariablesAbstractas
 */
class Liquidacion_Model_DbTable_Variables_Variables extends Liquidacion_Model_DbTable_VariablesAbstractas
{

    protected $_referenceMap    = array(
	    'TiposDeVariables' => array(
            'columns'           => 'TipoDeVariable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeVariables',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeVariables',
            'refColumns'        => 'Id',
        ),
	    'TiposDeConceptosLiquidaciones' => array(
            'columns'           => 'TipoDeConceptoLiquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeConceptosLiquidaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeConceptosLiquidaciones',
            'refColumns'        => 'Id',
        ),
        'VariablesCategorias' => array(
            'columns'           => 'VariableCategoria',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesCategorias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'VariablesCategorias',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )    
    );

    protected $_permanentValues  = array( 'TipoDeVariable' => 2);
    protected $_defaultValues    = array( 'TipoDeVariable' => 2);




}