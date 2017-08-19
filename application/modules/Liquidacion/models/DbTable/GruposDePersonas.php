<?php
class Liquidacion_Model_DbTable_GruposDePersonas extends Rad_Db_Table
{
    protected $_name = 'GruposDePersonas';

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array('Liquidacion_Model_DbTable_GruposDePersonasDetalles');	

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'GruposDePersonas',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            ),
            'FechaBaja'=> array(
                array( 'GreaterThan',
                        '{FechaAlta}'
                ),
                'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
            )            
        );

        parent::init();
    }    
}