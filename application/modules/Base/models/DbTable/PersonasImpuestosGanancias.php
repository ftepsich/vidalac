<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_PersonasImpuestosGanancias
 *
 * Personas Impuestos Ganancias
 *
 * @copyright Papu Gomez Corporation
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_PersonasImpuestosGanancias
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasImpuestosGanancias extends Rad_Db_Table
{

    protected $_name = 'PersonasRetencionesGanancias';

    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
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
            'comboSource'       => 'datagateway/combolist',
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


    /**
     * Validadores
     *
     * Modalidad Iva              -> not null
     * Tipo Retencion Gcia        -> not null
     *
     */
    protected $_validators = array(

        'TipoInscripcionIva' => array(
            'NotEmpty',
            'allowEmpty' => false,
            'messages' => array('Falta ingresar la inscripción de iva.')
        ),
        'Persona' => array(
            array(
                'Db_NoRecordExists',
                'Personas',
                'RazonSocial',
                'Id <> {Id}'
            ),
            'messages' => array('Ya existe un registro para esa proveedor')
        )
    );

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();
            /*  $fecha = date('d-m-Y');
            $FechaVencimientoCertificadoDeExclusion = date('Y-m-d', strtotime(($data['FechaVencimientoCertificadoDeExclusion']) ? $data['FechaVencimientoCertificadoDeExclusion'] : '1900-01-01'));
            if ($fecha < $FechaVencimientoCertificadoDeExclusion) {
                throw new Rad_Db_Table_Exception("La Fecha de Vencimiento del Certificado de exclusión no puede ser menor a la fecha actual.");
            }
            */
            $data['FechaAlta'] = date('Y-m-d H:i:s');
            $id = parent::insert($data);
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                if ($data['TiposDeImpuestosGananciasRetenciones']) {
                    $condicion = "TiposDeImpuestosGananciasRetenciones = '" . $data['TiposDeImpuestosGananciasRetenciones'] . "' AND PersonasRetencionesGanancias.Id <> " . $row->Id;
                    $tipoImpuestoGanaciasRetenciones = $this->fetchRow($condicion);
                }

                parent::update($data, 'PersonasRetencionesGanancias.Id =' . $row->Id);
                $this->_db->commit();
                return true;
            }
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchImpuestoGananciasProveedores($where = null, $count = null, $offset = null)
    {
        $condicion = "ConceptosImpositivos.TipoDeConcepto = 2 and ConceptosImpositivos.EsRetencion=1 and ConceptosImpositivos.Descripcion like '%(R)%' and ConceptosImpositivos.EnUso = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $count, $offset);
    }
}
