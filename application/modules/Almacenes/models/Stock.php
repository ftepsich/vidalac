<?php

class Almacenes_Model_Stock_Exception extends Rad_Exception
{

}

/**
 * Esta clase provee el manejo de stock del sistema.
 * Todo lo que figure en stock debe estar ingresado como mmi en un almacen.
 *
 * @package     Aplicacion
 * @subpackage  Almacenes
 * @class       Almacenes_Model_Stock
 *
 * @autor Martin A. Santangelo
 */
class Almacenes_Model_Stock
{

    /**
     * Retorna el Stock Actual del $articulo dado.
     * Opcionalmente puede ser filtrado por $almacen
     *
     * @param $articulo INT
     * @param $almacen INT (opcional)
     * return INT
     */
    public static function getStock($articulo, $almacen = null)
    {
        $modelArticulo = Service_TableManager::get('Base_Model_DbTable_Articulos');
        $rowArticulo   = self::getArticulo($articulo);

        if (!$rowArticulo) throw new Almacenes_Model_Stock_Exception('No se encontro el articulo');

        // Si el stock se lleva por las existencias de almacen
        switch($rowArticulo->TipoDeControlDeStock) {
            case 1:
                $db = Zend_Registry::get('db');

                $sql = "SELECT sum(CantidadActual) FROM
                                Mmis m
                                where m.Articulo = $articulo and m.FechaCierre is null";

                if ($almacen) {
                    $sql .= " AND m.Almacen = $almacen";
                }

                $cantidad = $db->fetchOne($sql);

                if (!$cantidad)
                    $cantidad = 0;

                return $cantidad;
            break;
            case 2:
                if ($almacen) throw new Almacenes_Model_Stock_Exception('Este articulo no lleva un control de stock que permita filtrar por almacen');
                $modelStockArticulos = Service_TableManager::get('Almacenes_Model_DbTable_ArticulosStock');
                return $modelStockArticulos->getStock($articulo);
            break;
            default:
                return 0;
        }
    }

    protected static function getArticulo($id)
    {
        $modelArticulo = Service_TableManager::get('Base_Model_DbTable_Articulos');
        return $modelArticulo->find($id)->current();
    }

    public static function getStockEnFecha($articulo, $fecha, $almacen = null)
    {
        $rowArticulo   = self::getArticulo($articulo);
        if ($rowArticulo->TipoDeControlDeStock == 2) {
            $modelStockArticulos = Service_TableManager::get('Almacenes_Model_DbTable_ArticulosStock');
            return $modelStockArticulos->getStock($articulo, $fecha);
        } else {
            $db = Zend_Registry::get('db');
            $sqlAmacen = "";
            if ($almacen) {
                $sqlAmacen = " AND m.Almacen = $almacen";
            }

            $sql = "SELECT
                sum(if (mm.Cantidad is null, m.CantidadOriginal, mm.Cantidad))
                FROM
                MmisMovimientos mm
                RIGHT OUTER JOIN Mmis m
                    on
                    mm.Mmi = m.Id AND
                    mm.Fecha = (SELECT max(Fecha) FROM MmisMovimientos WHERE Mmi = m.Id AND Fecha < '$fecha')

                where m.Articulo = $articulo $sqlAmacen and m.FechaIngreso <= '$fecha' and (m.FechaCierre is null or m.FechaCierre > '$fecha')
                order by m.Id, mm.Fecha desc";

            $cantidad = $db->fetchOne($sql);

            if (!$cantidad)
                $cantidad = 0;
            return $cantidad;
        }

    }

    /**
     *  Busca el stock de un producto calculado en la unidad de medida pasada
     *
     *  @param $producto INT
     *  @param $unidadDeMedida INT (opcional)
     *  return float
     */
    public static function getStockProducto($productoVerId, $unidadDeMedida, $almacen = null, $soloInsumos = false)
    {
        $db = Zend_Registry::get('db');
        $mArticulo = Service_TableManager::get('Base_Model_DbTable_Articulos');


        $sqlAmacen = "";
        if ($almacen) {
            $sqlAmacen = " AND m.Almacen = $almacen";
        }

        if ($soloInsumos) {
            $sqlAmacen .= " AND A.EsInsumo = 1";
        }

        $cantTotal = 0;

        // que articulos versiones tienen este producto version?
        $artVerIds = $mArticulo->getArticulosVersionesPorProductoVersion($productoVerId, $soloInsumos);

        foreach ($artVerIds as $artVer) {
            $sql = "SELECT sum(CantidadActual) FROM
                Mmis m
                Where m.FechaCierre is null $sqlAmacen and m.ArticuloVersion = $artVer";
            $cantidad = $db->fetchOne($sql);

            if (!$cantidad) $cantidad = 0;

            // traer la cantidad de producto que tiene este articulo version

            $cantTotal += $mArticulo->getCantidadProducto($artVer, $cantidad, $unidadDeMedida);
        }

        return $cantTotal;
    }

    /**
     * retorna el stock de producto desglosado por articulosversiones que lo contienen
     */
    public static function getStockProductoDesglosadoArticulo($productoVerId, $unidadDeMedida, $almacen = null, $soloInsumos = false)
    {
        $db = Zend_Registry::get('db');
        $mArticulo = Service_TableManager::get('Base_Model_DbTable_Articulos');


        $sqlAmacen = "";
        if ($almacen) {
            $sqlAmacen = " AND m.Almacen = $almacen";
        }

        if ($soloInsumos) {
            $sqlAmacen .= " AND A.EsInsumo = 1";
        }

        $cantTotal = array();

        // que articulos versiones tienen este producto version?
        $artVerIds = $mArticulo->getArticulosVersionesPorProductoVersion($productoVerId, $soloInsumos);

        foreach ($artVerIds as $artVer) {
            $sql = "SELECT sum(CantidadActual) FROM
                Mmis m
                Where m.FechaCierre is null $sqlAmacen and m.ArticuloVersion = $artVer";
            $cantidad = $db->fetchOne($sql);

            if (!$cantidad) $cantidad = 0;

            // traer la cantidad de producto que tiene este articulo version

            $cantTotal[$artVer] = $mArticulo->getCantidadProducto($artVer, $cantidad, $unidadDeMedida);
        }

        return $cantTotal;
    }

}