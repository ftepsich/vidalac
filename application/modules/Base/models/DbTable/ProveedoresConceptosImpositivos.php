<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ProveedoresConceptosImpositivos
 *
 * Conceptos Impositivos de los Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ProveedoresConceptosImpositivos
 * @extends Model_DbTable_PersonasConceptosImpositivos
 */
class Base_Model_DbTable_ProveedoresConceptosImpositivos extends Base_Model_DbTable_PersonasConceptosImpositivos
{

    protected $_referenceMap = array(
        'Proveedores' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns'    => array("RazonSocial"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ConceptosImpositivos' => array(
            'columns'           => 'ConceptoImpositivo',
            'refTableClass'     => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns'    => array("Descripcion", "ParaCompra", "ParaVenta", "ParaPago", "ParaCobro"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ConceptosImpositivos',
            'refColumns'        => 'Id'
        )
    );
    
}