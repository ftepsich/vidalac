<?php
class Liquidacion_Model_DbTable_TiposDeConceptosLiquidaciones extends Rad_Db_Table
{
    protected $_name = 'TiposDeConceptosLiquidaciones';

    protected $_sort = array('Descripcion asc');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'TiposDeConceptosLiquidaciones',
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