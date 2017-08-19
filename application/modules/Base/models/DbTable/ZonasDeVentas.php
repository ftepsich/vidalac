<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ZonasDeVentas
 *
 * Zonas de Ventas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ZonasDeVentas
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ZonasDeVentas extends Rad_Db_Table
{
    protected $_name = 'ZonasDeVentas';

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'ZonasDeVentas',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => ($_POST["Id"]) ? $_POST["Id"] : ($_POST["node"] ? $_POST["node"] : 0)
                    )
                )
            )
        );
        parent::init();
    }

}