<?php
/**
 * Facturacion_Model_DbTable_TarjetasDeCredito
 *
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Facturacion_Model_DbTable_TarjetasDeCredito * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_TarjetasDeCredito extends Rad_Db_Table
{
    protected $_name = 'TarjetasDeCredito';

    protected $_validators = array(
        'Numero' => array(
            'NotEmpty',
            array(
                'Db_NoRecordExists',
                'TarjetasDeCredito',
                'Numero'
            ),
            'messages' => array(
                'Falta ingresar el Número',
                'El numero %value% de Tarjeta ya existe'
            )
        ),
    );

    protected $_referenceMap = array(

        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Bancos' => array(
            'columns'           => 'EntidadEmisora',
            'refTableClass'     => 'Base_Model_DbTable_Bancos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Bancos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'TarjetasDeCreditoMarcas' => array(
            'columns'           => 'TarjetaCreditoMarca',
            'refTableClass'     => 'Facturacion_Model_DbTable_TarjetasDeCreditoMarcas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Activas',
            'refTable'          => 'TarjetasDeCreditoMarcas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array('Facturacion_Model_DbTable_TarjetasDeCreditoCupones');
}