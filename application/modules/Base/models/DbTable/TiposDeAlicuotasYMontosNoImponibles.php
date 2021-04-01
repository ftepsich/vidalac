<?php
require_once 'Rad/Db/Table.php';

class Base_Model_DbTable_TiposDeAlicuotasYMontosNoImponibles extends Rad_Db_Table
{

    // Tabla
    protected $_name = "TiposDeAlicuotasYMontosNoImponibles";
    protected $_sort = array('Codigo asc');
    protected $_defaultSource = self::DEFAULT_CLASS;

    // Validaciones
    protected $_validators = array(
        'Codigo' => array(
            array(
                'Db_NoRecordExists',
                'Codigo',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            ),
            'messages' => 'Ya existe una actividad con el mismo codigo.'
        ),
        'Descripcion' => array(
            'NotEmpty',
            'allowEmpty' => false,
            'messages' => array(
                'Falta ingresar la Descripción de la actividad.'
            )
        ),
    );
}
