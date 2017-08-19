<?php
require_once 'Rad/Db/Table.php';

/**
 * Almacenes_Model_DbTable_ArticulosStockAlmacen
 *
 * Articulos
 *
 * @package     Aplicacion
 * @subpackage 	Almacenes
 * @class       Almacenes_Model_DbTable_ArticulosStockAlmacen
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Almacenes_Model_DbTable_ArticulosStockAlmacen extends Base_Model_DbTable_Articulos
{
    protected $_calculatedFields = array(
        'Stock' => "CASE Articulos.TipoDeControlDeStock WHEN 1 THEN fStockArticuloEsInsumo(Articulos.Id) WHEN 2 THEN fStockArticuloFechaXCantidad(Articulos.Id, now()) END"
    );
    
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.Tipo = 1";

        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
}