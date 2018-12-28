<?php

require_once 'Rad/Db/Table.php';

/**
 * Remitos de salida
 *
 * @package Aplicacion
 * @subpackage Almacenes
 * @class Almacenes_Model_DbTable_RemitosDeSalidas
 * @extends Almacenes_Model_DbTable_Remitos
 */
class Almacenes_Model_DbTable_RemitosDeSalidasYBajas extends Almacenes_Model_DbTable_RemitosDeSalidas
{

    protected $_permanentValues = array(
        'TipoDeComprobante' => array(16, 15, 45)
    );

}