<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesCategorias
 *
 * Conceptos Liquidaciones Detalles
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesCategorias
 * @extends Rad_Db_Table
 */
class Liquidacion_Model_DbTable_VariablesCategorias extends Rad_Db_Table
{
    protected $_name = 'VariablesCategorias';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Model_DbTable_Variables');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'VariablesCategorias',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );

        parent::init();
    }    
}