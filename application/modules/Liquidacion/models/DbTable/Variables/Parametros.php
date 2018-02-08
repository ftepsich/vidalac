<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_Variables_Parametros
 *
 * Parametros
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_Variables_Parametros
 * @extends Liquidacion_Model_DbTable_VariablesAbstractas
 */
class Liquidacion_Model_DbTable_Variables_Parametros extends Liquidacion_Model_DbTable_VariablesAbstractas
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
        )
    );

    protected $_permanentValues  = array(   'TipoDeVariable' => 3);
    protected $_defaultValues    = array(   'TipoDeVariable' => 3,
                                            'Activo' => 1
                                        );




}