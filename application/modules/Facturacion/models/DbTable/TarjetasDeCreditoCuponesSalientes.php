<?php
/**
 * Facturacion_Model_DbTable_TarjetasDeCreditoCuponesSalientes
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_Model_DbTable_TarjetasDeCreditoCuponesSalientes * @extends     Facturacion_Model_DbTable_TarjetasDeCreditoCupones
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_TarjetasDeCreditoCuponesSalientes extends Facturacion_Model_DbTable_TarjetasDeCreditoCupones
{

    protected $_referenceMap = array(

    'TarjetasDeCredito' => array(
        'columns'           => 'TarjetaDeCredito',
        'refTableClass'     => 'Facturacion_Model_DbTable_TarjetasDeCredito_Propias',
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
		'TipoDeMovimiento' => 2
	);
}