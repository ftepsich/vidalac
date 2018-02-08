<?php

require_once 'Rad/Db/Table.php';

class Almacenes_Model_DbTable_LotesDeTerceros extends Almacenes_Model_DbTable_Lotes
{

    /**
     * Valores Permanentes
     *
     * 'Propio' => '1'
     *
     */
    protected $_permanentValues = array(
        'Propio' => 0
    );

    // Tabla mapeada
    protected $_name = "Lotes";
   
}