<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_EstadosCiviles
 *
 * Estados Civiles
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_EstadosCiviles
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_EstadosCiviles extends Rad_Db_Table
{

    protected $_name = 'EstadosCiviles';

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'EstadosCiviles',
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