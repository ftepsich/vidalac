<?php
/**
 * @package     Aplicacion
 * @subpackage  Base
 * @class       Base_Model_DbTable_Idiomas * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Base_Model_DbTable_Idiomas extends Rad_Db_Table
{
    protected $_name = 'Idiomas';

    protected $_referenceMap = array(   
		'AfipIdiomas' => array(
			'columns'           => 'Afip',
			'refTableClass'     => 'Afip_Model_DbTable_AfipIdiomas',
			'refJoinColumns'    => array('Descripcion'),
			'comboBox'          => true,
			'comboSource'       => 'datagateway/combolist',
			'refTable'          => 'AfipIdiomas',
			'refColumns'        => 'Id',
			'comboPageSize'     => '10'
		)
    );

    protected $_dependentTables = array();
}