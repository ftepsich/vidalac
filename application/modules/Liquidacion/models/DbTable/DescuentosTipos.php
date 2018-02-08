<?php
/**
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_DbTable_DescuentosTipos * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_DescuentosTipos extends Rad_Db_Table
{
    protected $_name = 'DescuentosTipos';

    protected $_sort = array('Descripcion');  

    protected $_dependentTables = array('Liquidacion_Model_DbTable_Descuentos');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'DescuentosTipos',
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