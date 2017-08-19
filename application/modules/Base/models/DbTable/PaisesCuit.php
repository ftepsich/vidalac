<?php
/**
 * @package     Aplicacion
 * @subpackage  Base
 * @class       Base_Model_DbTable_PaisesCuit * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Base_Model_DbTable_PaisesCuit extends Rad_Db_Table
{
    protected $_name = 'PaisesCuit';

    protected $_referenceMap = array(
		'AfipCuitPaises' => array(
			'columns'           => 'AfipCuitPais',
			'refTableClass'     => 'Afip_Model_DbTable_AfipCuitPaises',
			'refJoinColumns'    => array('Descripcion'),
			'comboBox'          => true,
			'comboSource'       => 'datagateway/combolist',
			'refTable'          => 'AfipCuitPaises',
			'refColumns'        => 'Id',
			'comboPageSize'     => 20
		),
		'Paises' => array(
			'columns'           => 'Pais',
			'refTableClass'     => 'Base_Model_DbTable_Paises',
			'refJoinColumns'    => array('Descripcion'),
			'comboBox'          => true,
			'comboSource'       => 'datagateway/combolist',
			'refTable'          => 'Paises',
			'refColumns'        => 'Id',
			'comboPageSize'     => 20
		)
    );

    protected $_dependentTables = array();


}