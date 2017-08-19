<?php

require_once 'Rad/Db/Table.php';

/**
 * Laboratorio_Model_DbTable_TiposDeAnalisis
 *
 * Tipos De Analisis
 *
 * @package     Aplicacion
 * @subpackage 	Laboratorio
 * @class       Laboratorio_Model_DbTable_TiposDeAnalisis
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_TiposDeAnalisis extends Rad_Db_Table
{

    protected $_name = "TiposDeAnalisis";

    /**
     * Init del Modelo
     */
    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array(
                    'Db_NoRecordExists',
                    'TiposDeAnalisis',
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
