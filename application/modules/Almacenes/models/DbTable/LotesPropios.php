<?php

require_once 'Rad/Db/Table.php';

class Almacenes_Model_DbTable_LotesPropios extends Almacenes_Model_DbTable_Lotes
{

    /**
     * Valores Permanentes
     *
     * 'Propio' => '1'
     *
     */
    protected $_permanentValues = array(
        'Propio' => 1
    );

    // Tabla mapeada
    protected $_name = "Lotes";
    // Relaciones
    protected $_referenceMap = array(
        'Articulos' => array(
            'columns' => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_ArticulosGenericos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Articulos',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        )
    );
}