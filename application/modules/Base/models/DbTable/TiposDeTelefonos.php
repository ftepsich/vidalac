<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_AdministrarClientesController
 *
 * Direcciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_AdministrarClientesController
 * @extends Rad_Window_Controller_Action
 */
class Base_Model_DbTable_TiposDeTelefonos extends Rad_Db_Table
{

    protected $_name = 'TiposDeTelefonos';

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'TiposDeTelefonos',
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