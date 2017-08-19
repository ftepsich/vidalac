<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesDetalles_PrimitivasDetalles
 *
 * Primitivas Detalles
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesDetalles_PrimitivasDetalles
 * @extends Liquidacion_Model_DbTable_VariablesDetallesAbstractas
 */
class Liquidacion_Model_DbTable_VariablesDetalles_PrimitivasDetalles extends Liquidacion_Model_DbTable_VariablesDetallesAbstractas
{  

    protected $_referenceMap    = array(
        'Variables' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Variables_Primitivas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Variables',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );   

}