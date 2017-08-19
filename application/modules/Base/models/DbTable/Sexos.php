<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Sexos
 *
 * Sexos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Sexos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Sexos extends Rad_Db_Table
{

    protected $_name = 'Sexos';

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'Sexos',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            ),
            'DescripcionR' => array(
                array('Db_NoRecordExists',
                    'Sexos',
                    'DescripcionR',
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