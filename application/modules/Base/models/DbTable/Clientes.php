<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Clientes
 *
 * Clientes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Clientes
 * @extends Base_Model_DbTable_Personas
 */
class Base_Model_DbTable_Clientes extends Base_Model_DbTable_Personas
{
    protected $_name = 'Personas';
    protected $_sort = array('RazonSocial ASC');
    protected $_permanentValues = array('EsCliente' => 1);

    protected $_referenceMap = array(
       'ModalidadesIVA' => array(
           'columns'            => 'ModalidadIva',
           'refTableClass'      => 'Base_Model_DbTable_ModalidadesIVA',
           'refJoinColumns'     => array('Descripcion'),
           'comboBox'           => true,
           'comboSource'        => 'datagateway/combolist',
           'refTable'           => 'ModalidadesIVA',
           'refColumns'         => 'Id'
       ),	
        'TiposDeInscripcionesGanancias' => array(
            'columns'            => 'ModalidadGanancia',
            'refTableClass'      => 'Base_Model_DbTable_TiposDeInscripcionesGanancias',
            'refJoinColumns'     => array('Descripcion'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposDeInscripcionesGanancias',
            'refColumns'         => 'Id'
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
        'TiposFormasDePagos' => array(
            'columns'            => 'TipoFormaDePago',
            'refTableClass'      => 'Base_Model_DbTable_TiposFormasDePagos',
            'refJoinColumns'     => array('Descripcion'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposFormasDePagos',
            'refColumns'         => 'Id'
        ),
        'TiposFletesACargo' => array(
            'columns'            => 'TipoFleteACargo',
            'refTableClass'      => 'Base_Model_DbTable_TiposFletesACargo',
            'refJoinColumns'     => array('Descripcion'),
            'comboBox'           => true,
            'comboSource'        => 'datagateway/combolist',
            'refTable'           => 'TiposFletesACargo',
            'refColumns'         => 'Id'
        )
    );

    /**
     * Validadores
     *
     * CUIT         -> valor unico y no vacio
     * Razon Social -> valor unico y no vacio
     *
     */    
    
    public function init()
    {
        $this->_validators['RazonSocial']= array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array(
                'Falta ingresar la Razon Social.'
            )
        );        
        parent::init();     
        $this->_calculatedFields['IBProximosVencimientosCM05'] = "(SELECT COUNT(Id) FROM personasingresosbrutos WHERE Persona = Personas.Id AND FechaVencimientoCM05 IS NOT NULL AND FechaVencimientoCM05 < DATE_ADD(CURDATE(), INTERVAL 10 DAY) )";
    }    

    public function fetchTieneComprobantes($where = null, $order = null, $count = null, $offset = null)
    {
        /*
            Este fetch se usaen los reportes estadisticos para filtrar los clientes que tienen comprobantes.
        */
        $j  = $this->getJoiner();
        $jc = $j->with('Facturacion_Model_DbTable_Comprobantes');
        if (!$jc) {
            $j->joinDep('Facturacion_Model_DbTable_Comprobantes',array(),null,null,'Personas.Id');
        }
        $condicion = 'ClientesComprobantes.Id is not null';
        $where = $this->_addCondition($where, $condicion);        
        return parent::fetchAll($where, $order, $count, $offset);
    }    
}