<?php
/**
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_DbTable_DeduccionesGananciasTipos * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_DeduccionesGananciasTipos extends Rad_Db_Table
{
    protected $_name = 'DeduccionesGananciasTipos';

    protected $_sort = array('Descripcion asc');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'DeduccionesGananciasTipos',
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

    protected $_dependentTables = array('Liquidacion_Model_DbTable_DeduccionesGanancias');
}