<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ProveedoresImpuestosGanancias
 *
 * Proveedores Impuestos Ganancias
 *
 * @copyright Papu Gomez Corporation
 * @package Aplicacion
 * @subpackage Base 
 * @class Base_Model_DbTable_ProveedoresImpuestosGanancias
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ProveedoresImpuestosGanancias extends Base_Model_DbTable_PersonasImpuestosGanancias
{

    protected $_referenceMap = array(
        'Proveedores' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns'    => array("RazonSocial"),
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ConceptosImpositivos' => array(
            'columns'           => 'ConceptoImpositivo',
            'refTableClass'     => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/RetencionesGananciasR',
            'refTable'          => 'ConceptosImpositivos',
            'refColumns'        => 'Id'
        ),
        'TiposDeInscripcionesGanancias' => array(
            'columns'            => 'TipoInscripcionGanancia',
            'refTableClass'      => 'Base_Model_DbTable_TiposDeInscripcionesGanancias',
            'refJoinColumns'     => array("Descripcion"),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposDeInscripcionesGanancias',
            'refColumns'         => 'Id'
        ),
          'TiposDeAlicuotasYMontosNoImponibles' => array(
            'columns'            => 'TipoRetencionGanancia',
            'refTableClass'      => 'Base_Model_DbTable_TiposDeAlicuotasYMontosNoImponibles',
            'refJoinColumns'     => array("Codigo", "Descripcion"),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposDeAlicuotasYMontosNoImponibles',
            'refColumns'         => 'Id',
            'comboPageSize' => 10
        ),
    );

    protected $_defaultSource = self::DEFAULT_CLASS;

    protected $_defaultValues = array(
        'ConceptoImpositivo' => '49',
    );

    public function init()
    {

        parent::init();
    }
}
