<?php
require_once('Rad/Db/Table.php');
/**
 * Base_Model_DbTable_ModalidadesIVA
 *
 * Modalidades de IVA
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_ModalidadesIVA
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ModalidadesIVA extends Rad_Db_Table
{
	protected $_name = "ModalidadesIVA";
	protected $_sort = array ('Descripcion ASC');

    protected $_readOnlyFields  = array(
        'AFIP'
    );

    protected $_referenceMap = array(
        'AfipTiposDeResponsables' => array(
            'columns'           => 'Afip',
            'refTableClass'     => 'Afip_Model_DbTable_AfipTiposDeResponsables',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipTiposDeResponsables',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );    
	
	public function init()     {
        $this -> _validators = array(
            'Descripcion'=> array(
                array(	
                    'Db_NoRecordExists',
                    'ModalidadesIVA',
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