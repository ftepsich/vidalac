<?php
    
//require_once 'Rad/Db/Table.php';

/**
 *
 * Laboratorio_Model_DbTable_AnalisisTiposModelos
 *
 * Anlisis Tipos modelos
 *
 *
 * @package     Aplicacion
 * @subpackage 	Laboratorio
 * @class       Base_Model_DbTable_Productos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_AnalisisTiposModelos extends Rad_Db_Table
{

    protected $_name = "AnalisisTiposModelos";

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'AnalisisTiposModelos',
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
