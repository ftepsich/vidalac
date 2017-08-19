<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ClientesConceptosImpositivos
 *
 * Clientes Conceptos Impositivos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ClientesConceptosImpositivos
 * @extends Base_Model_DbTable_PersonasConceptosImpositivos
 */
class Base_Model_DbTable_ClientesConceptosImpositivos extends Base_Model_DbTable_PersonasConceptosImpositivos
{
    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Clientes',
            'refJoinColumns'    => array('RazonSocial'),
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        ),
        'ConceptosImpositivos' => array(
            'columns'           => 'ConceptoImpositivo',
            'refTableClass'     => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns'    => array('Descripcion', 'ParaCompra', 'ParaVenta', 'ParaPago', 'ParaCobro'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ConceptosImpositivos',
            'refColumns'        => 'Id'
        )
    );
    
}