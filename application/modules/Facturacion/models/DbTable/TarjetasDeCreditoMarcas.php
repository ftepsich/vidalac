<?php
/**
 * Facturacion_Model_DbTable_TarjetasDeCreditoMarcas
 *
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_TarjetasDeCreditoMarcas * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_TarjetasDeCreditoMarcas extends Rad_Db_Table
{
    protected $_name = 'TarjetasDeCreditoMarcas';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap = array(
        'Personas' => array(
            'columns'           => 'Proveedor',
            'refTableClass'     => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array('Facturacion_Model_DbTable_TarjetasDeCredito');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TarjetasDeCreditoMarcas',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );

        parent::init();
    }

    public function fetchActivas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TarjetasDeCreditoMarcas.Activa = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}