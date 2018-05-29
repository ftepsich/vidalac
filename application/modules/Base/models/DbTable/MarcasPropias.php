<?php
require_once('Rad/Db/Table.php');

class Model_DbTable_MarcasPropias extends Model_DbTable_Marcas
{
    protected $_name = "Marcas";

    public $abmTitle = 'Marcas propias';

    protected $_permanentValues = array('Propia' => 1);
    
    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_defaultValues = array (
        'Propia'     => '1',
        'Produccion' => '1'
    );
}