<?php
require_once 'Rad/Db/Table.php';

/**
 * Model_DbTable_PersonasConceptosImpositivos
 *
 * Personas Conceptos Impositivos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Model_DbTable_PersonasConceptosImpositivos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasConceptosImpositivos extends Rad_Db_Table
{
    protected $_name = 'PersonasConceptosImpositivos';

    protected $_sort = array('RazonSocial asc');
    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ConceptosImpositivos' => array(
            'columns'           => 'ConceptoImpositivo',
            'refTableClass'     => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ConceptosImpositivos',
            'refColumns'        => 'Id'
        )
    );

}