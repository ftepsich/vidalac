<?php
require_once 'Rad/Db/Table.php';

class Base_Model_DbTable_CodigosActividadesAfip extends Rad_Db_Table
{

    // Tabla mapeada
    protected $_name = "CodigosActividadesAfip";
    // Relaciones
    protected $_referenceMap = array(
    );
    protected $_dependentTables = array('Model_DbTable_ProveedoresActividades', 'Model_DbTable_SubDiarioDeIva');

}