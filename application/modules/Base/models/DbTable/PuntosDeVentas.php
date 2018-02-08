<?php
require_once 'Rad/Db/Table.php';
/**
 *
 * Base_Model_DbTable_PuntosDeVentas
 *
 * PuntosDeVentass
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_PuntosDeVentas
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_PuntosDeVentas extends Rad_Db_Table
{
    protected $_name = 'PuntosDeVentas';

    protected $_referenceMap    = array(
	    'AdaptadoresFiscalizaciones' => array(
            'columns'           => 'Adaptador',
            'refTableClass'     => 'Base_Model_DbTable_AdaptadoresFiscalizaciones',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'AdaptadoresFiscalizaciones',
            'refColumns'        => 'Id'
        ),
        'Cajas' => array(
            'columns'           => 'Caja',
            'refTableClass'     => 'Contable_Model_DbTable_Cajas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Cajas',
            'refColumns'        => 'Id'
        )
    );


    protected $_dependentTables = array();

}
