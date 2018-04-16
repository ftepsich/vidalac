<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_PersonasIngresosBrutos
 *
 * Personas Ingresos Brutos
 *
 * @copyright Papu Gomez Corporation
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_PersonasIngresosBrutos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasIngresosBrutos extends Rad_Db_Table
{

        protected $_name = 'PersonasIngresosBrutos';

        protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
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
            'comboSource'       => 'datagateway/combolist',
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
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposDeMotivosNoPercepcionRetencionIB',
            'refColumns'         => 'Id'
        ),
        'ActividadesIB' => array(
            'columns'            => 'ActividadIB',
            'refTableClass'      => 'Base_Model_DbTable_CodigosActividadesAfip',
            'refJoinColumns'     => array('Descripcion', 'Porcentaje'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'CodigosActividadesAfip',
            'refColumns'         => 'Id'
        )
    );


    public function fetchParaClientes($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ConceptosImpositivos.TipoDeConcepto = 3 and ConceptosImpositivos.EsPercepcion=1 and ConceptosImpositivos.Descripcion like '%(R)%' and ConceptosImpositivos.EnUso = 1";
        $where = $this->_addCondition($where, $condicion);
        $order = "Jurisdicciones.Descripcion asc";
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchParaProveedores($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ConceptosImpositivos.TipoDeConcepto = 3 and ConceptosImpositivos.EsRetencion=1 and ConceptosImpositivos.Descripcion like '%(R)%' and ConceptosImpositivos.EnUso = 1";
        $where = $this->_addCondition($where, $condicion);
        $order = "Jurisdicciones.Descripcion asc";
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}
