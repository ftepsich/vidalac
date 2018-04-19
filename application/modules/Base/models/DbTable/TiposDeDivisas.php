<?php
require_once('Rad/Db/Table.php');
/**
 * Base_Model_DbTable_TiposDeDivisas
 *
 * Tipos De Divisas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_TiposDeDivisas
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_TiposDeDivisas extends Rad_Db_Table
{
	protected $_name = "TiposDeDivisas";

    protected $_sort = array("Descripcion asc");

    protected $_referenceMap = array(
        'AfipMonedas' => array(
            'columns' => 'Afip',
            'refTableClass' => 'Afip_Model_DbTable_AfipMonedas',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'AfipMonedas',
            'refColumns' => 'Id'
        ),
    );
    

	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(
                    array(	'Db_NoRecordExists',
                            'TiposDeDivisas',
                            'Descripcion',
                            array(
                                'field' => 'Id',
                                'value' => "{Id}"
                            )
                    )
                )
			);

		parent::init();
    }


}