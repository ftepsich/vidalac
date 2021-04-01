<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ProveedoresIngresosBrutos
 *
 * Proveedores Ingresos Brutos
 *
 * @copyright Papu Gomez Corporation
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ProveedoresIngresosBrutos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ProveedoresIngresosBrutos extends Base_Model_DbTable_PersonasIngresosBrutos
{

    protected $_referenceMap = array(
        'Proveedores' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns'    => array("RazonSocial", "TipoInscripcionIB", "NroInscripcionIB"),
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ConceptosImpositivos' => array(
            'columns'           => 'ConceptoImpositivo',
            'refTableClass'     => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns'    => array("Descripcion", "PorcentajeActual", "EsRetencion", "EsPercepcion", "MontoMinimo", "Jurisdiccion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/IbRetencionesR',
            'refTable'          => 'ConceptosImpositivos',
            'refColumns'        => 'Id'
        ),
        'TiposDeInscripcionesIB' => array(
            'columns'            => 'TipoInscripcionIB',
            'refTableClass'      => 'Base_Model_DbTable_TiposDeInscripcionesIB',
            'refJoinColumns'     => array('Descripcion'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposDeInscripcionesIB',
            'refColumns'         => 'Id'
        ),
        'MotivosDeNoRetencionIB' => array(
            'columns'            => 'MotivoNoPercepcionRetencionIB',
            'refTableClass'      => 'Base_Model_DbTable_TiposDeMotivosNoPercepcionRetencionIB',
            'refJoinColumns'     => array('Descripcion'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist/fetch/ParaProveedores',
            'refTable'           => 'TiposDeMotivosNoRetencionIB',
            'refColumns'         => 'Id'
        ),
        'ActividadesIB' => array(
            'columns'            => 'ActividadIB',
            'refTableClass'      => 'Base_Model_DbTable_CodigosActividadesAfip',
            'refJoinColumns'     => array('Descripcion', 'Porcentaje'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist/fetch/ParaProveedores',
            'refTable'           => 'CodigosActividadesAfip',
            'refColumns'         => 'Id'
        ),
      'TipoDeOperacion' => array(
            'columns'            => 'TipoDeOperacion',
            'refTableClass'      => 'Base_Model_DbTable_TiposOperacionesAter',
            'refJoinColumns'     => array('Descripcion'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposOperacionesAter',
            'refColumns'         => 'Id'
        )
    );

    public function init()
    {
        parent::init();
    }

  

}
