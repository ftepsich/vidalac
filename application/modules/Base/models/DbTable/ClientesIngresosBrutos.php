<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ClientesIngresosBrutos
 *
 * Clientes Ingresos Brutos
 *
 * @copyright Papu Gomez Corporation
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ClientesIngresosBrutos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ClientesIngresosBrutos extends Base_Model_DbTable_PersonasIngresosBrutos
{


    protected $_referenceMap = array(
        'Clientes' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Clientes',
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
            'comboSource'       => 'datagateway/combolist/fetch/IbPercepcionesR',
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
            'comboSource'        => 'datagateway/combolist/fetch/ParaClientes',
            'refTable'           => 'TiposDeMotivosNoRetencionIB',
            'refColumns'         => 'Id'
        ),
        'ActividadesIB' => array(
            'columns'            => 'ActividadIB',
            'refTableClass'      => 'Base_Model_DbTable_CodigosActividadesAfip',
            'refJoinColumns'     => array('Descripcion', 'Porcentaje'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist/fetch/ParaClientes',
            'refTable'           => 'CodigosActividadesAfip',
            'refColumns'         => 'Id'
        )
    );

    public function init()
    {
        parent::init();
    }

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();
            $FechaAltaJurisdiccionCM = date('Y-m-d', strtotime( ($data['FechaAltaJurisdiccionCM']) ? $data['FechaAltaJurisdiccionCM'] : '1900-01-01' ));
            $FechaInicioCM05 = date('Y-m-d', strtotime( ($data['FechaInicioCM05']) ? $data['FechaInicioCM05'] : '1900-01-01' ));
            $FechaVencimientoCM05 = date('Y-m-d', strtotime( ($data['FechaVencimientoCM05']) ? $data['FechaVencimientoCM05'] : '1900-01-01' ));
            if ( $FechaVencimientoCM05 < $FechaInicioCM05 ) {
                throw new Rad_Db_Table_Exception("La Fecha de Vencimiento CM05 no puede ser menor a la Fecha de Inicio CM05.");
            }
            if ( ($data['FechaInicioCM05']) && $FechaInicioCM05 < $FechaAltaJurisdiccionCM ) {
                throw new Rad_Db_Table_Exception("La Fecha de Inicio CM05 no puede ser menor a la Fecha de Alta en la Jurisdiccion CM.");
            }
            $data['PeriodoCM05'] = ( ($data['PeriodoCM05'] == 0) ? null : $data['PeriodoCM05'] );
            $data['CoeficienteCM05'] = ( ($data['CoeficienteCM05'] == 0) ? null : $data['CoeficienteCM05'] );
            $data['Porcentaje'] = ( ($data['Porcentaje'] == 0) ? null : $data['Porcentaje'] );
            $data['MontoMinimo'] = ( ($data['MontoMinimo'] == 0) ? null : $data['MontoMinimo'] );
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
            $row = $this->fetchrow($where);
            $FechaAltaJurisdiccionCM = date('Y-m-d', strtotime( ($data['FechaAltaJurisdiccionCM']) ? $data['FechaAltaJurisdiccionCM'] : $row->FechaAltaJurisdiccionCM ));
            $FechaInicioCM05 = date('Y-m-d', strtotime( ($data['FechaInicioCM05']) ? $data['FechaInicioCM05'] : $row->FechaInicioCM05 ));
            $FechaVencimientoCM05 = date('Y-m-d', strtotime( ($data['FechaVencimientoCM05']) ? $data['FechaVencimientoCM05'] : $row->FechaVencimientoCM05 ));
            if ( $FechaVencimientoCM05 < $FechaInicioCM05 ) {
                throw new Rad_Db_Table_Exception("La Fecha de Vencimiento CM05 no puede ser menor a la Fecha de Inicio CM05.");
            }
            if ( ($data['FechaInicioCM05']) && $FechaInicioCM05 < $FechaAltaJurisdiccionCM ) {
                throw new Rad_Db_Table_Exception("La Fecha de Inicio CM05 no puede ser menor a la Fecha de Alta en la Jurisdiccion CM.");
            }
            $data['PeriodoCM05'] = ( ($data['PeriodoCM05'] == 0) ? null : $data['PeriodoCM05'] );
            $data['CoeficienteCM05'] = ( ($data['CoeficienteCM05'] == 0) ? null : $data['CoeficienteCM05'] );
            $data['Porcentaje'] = ( ($data['Porcentaje'] == 0) ? null : $data['Porcentaje'] );
            $data['MontoMinimo'] = ( ($data['MontoMinimo'] == 0) ? null : $data['MontoMinimo'] );
            $data['FechaUltCambio'] = date('Y-m-d H:i:s');
            parent::update($data, $where);
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}
