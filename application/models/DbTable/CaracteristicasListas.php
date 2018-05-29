<?php

/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_CaracteristicasListas
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Model_DbTable_CaracteristicasListas extends Rad_Db_Table
{
    protected $_name = 'CaracteristicasListas';

    protected $_referenceMap    = array(
        'Caracteristicas' => array(
            'columns'           => 'Caracteristica',
            'refTableClass'     => 'Model_DbTable_Caracteristicas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Caracteristicas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array();
}