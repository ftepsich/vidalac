<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ClientesModalidadesDePagos
 *
 * Conceptos Impositivos
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ClientesModalidadesDePagos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ModalidadesDePagos extends Rad_Db_Table
{
    protected $_name = "ModalidadesDePagos";

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'ModalidadesDePagos',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            ),
        );

        parent::init();
    }

}