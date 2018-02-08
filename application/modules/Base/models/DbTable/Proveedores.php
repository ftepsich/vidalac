<?php
require_once 'Personas.php';

/**
 * Base_Model_DbTable_Proveedores
 *
 * Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Model_DbTable_Proveedores
 * @extends Model_DbTable_Personas
 */
class Base_Model_DbTable_Proveedores extends Base_Model_DbTable_Personas
{
    protected $_name = 'Personas';
    protected $_sort = array('RazonSocial ASC');
    protected $_permanentValues = array('EsProveedor' => 1);

    protected $_referenceMap = array(
        'ModalidadesIVA' => array(
            'columns'           => 'ModalidadIva',
            'refTableClass'     => 'Base_Model_DbTable_ModalidadesIVA',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ModalidadesIVA',
            'refColumns'        => 'Id'
        ),
        'TiposDeInscripcionesGanancias' => array(
            'columns'           => 'ModalidadGanancia',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeInscripcionesGanancias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeInscripcionesGanancias',
            'refColumns'        => 'Id'
        ),
        'TransportePorDefecto' => array(
            'columns'           => 'TransportePorDefecto',
            'refTableClass'     => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns'    => array('RazonSocial','Denominacion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
		),
        'TiposDeInscripcionesIB' => array(
            'columns'           => 'TipoInscripcionIB',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeInscripcionesIB',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeInscripcionesIB',
            'refColumns'        => 'Id'	
        ),
        'TiposDeDocumentos' => array(
            'columns'           => 'TipoDeDocumento',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDocumentos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDocumentos',
            'refColumns'        => 'Id'
        )
    );

    public function fetchTransporte($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'EsTransporte = 1';
        
        $where = $this->_addCondition($where, $condicion);
        
        return parent::fetchAll($where, $order, $count, $offset);
    }

}