<?php
/**
 * Facturacion_Model_DbTable_TarjetasDeCreditoCuponesEntrantes
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_Model_DbTable_TarjetasDeCreditoCuponesEntrantes * @extends     Facturacion_Model_DbTable_TarjetasDeCreditoCupones
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_TarjetasDeCreditoCuponesEntrantes extends Facturacion_Model_DbTable_TarjetasDeCreditoCupones
{

    protected $_referenceMap = array(

    'TarjetasDeCredito' => array(
        'columns'           => 'TarjetaDeCredito',
        'refTableClass'     => 'Facturacion_Model_DbTable_TarjetasDeCredito_Terceros',
        'refJoinColumns'    => array('Numero'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'TarjetasDeCredito',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    ),
    'TiposDeMovimientos' => array(
        'columns'           => 'TipoDeMovimiento',
        'refTableClass'     => 'Facturacion_Model_DbTable_TiposDeMovimientosTarjetas',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'TiposDeMovimientosTarjetas',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
    );

	protected $_permanentValues = array (
		'TipoDeMovimiento' => 1
	);
}