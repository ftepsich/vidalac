<?php
class Liquidacion_Model_DbTable_VariablesCaracteristicasGanancias extends Rad_Db_Table
{
    protected $_name = 'VariablesCaracteristicasGanancias';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Liquidacion_Model_DbTable_Variables');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeLiquidaciones',
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