<?php
/**
 * @package     Aplicacion
 * @subpackage  Base
 * @class       Base_Model_DbTable_EmpresasSucursales * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Base_Model_DbTable_EmpresasSucursales extends Rad_Db_Table
{
    protected $_name = 'EmpresasSucursales';

    protected $_referenceMap = array(        
        'Empresas' => array(
            'columns'           => 'Empresa',
            'refTableClass'     => 'Base_Model_DbTable_Empresas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Empresas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Localidades' => array(
            'columns' => 'Localidad',
            'refTableClass' => 'Base_Model_DbTable_Localidades',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Localidades',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        )
    );

    protected $_dependentTables = array();
}