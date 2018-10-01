<?php

require_once 'Rad/Db/Table.php';

class Almacenes_Model_DbTable_Lotes extends Rad_Db_Table
{

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
        ),
        'Proveedor' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Persona',
            'refColumns' => 'Id',
            'comboPageSize' => 20			
        )
    );
    // protected $_dependentTables = array('Laboratorio_Model_DbTable_AnalisisMuestras', 'Almacenes_Model_DbTable_Mmis');

}