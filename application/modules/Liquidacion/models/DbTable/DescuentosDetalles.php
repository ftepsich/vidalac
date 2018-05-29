<?php
/**
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_DbTable_DescuentosDetalles * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_DescuentosDetalles extends Rad_Db_Table
{
    protected $_name = 'DescuentosDetalles';

    protected $_sort = array('Cuota');     

    protected $_referenceMap = array(  
        'Descuentos' => array(
            'columns'           => 'Descuento',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Descuentos',
            'refJoinColumns'    => array('Numero'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Descuentos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'RecibosDetalles' => array(
            'columns'           => 'ReciboDetalle',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles',
            'refJoinColumns'    => array('Id'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesRecibosDetalles',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )        
    );

    protected $_dependentTables = array();
}