<?php
class Facturacion_Model_DbTable_TiposDeMovimientosTarjetas extends Rad_Db_Table
{
    protected $_name = 'TiposDeMovimientosTarjetas';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Facturacion_Model_DbTable_TiposDeMovimientosTarjetas');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeMovimientosTarjetas',
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