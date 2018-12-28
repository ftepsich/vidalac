<?php
require_once('Rad/Db/Table.php');

/**
 * Almacenes_Model_DbTable_ArticulosStock
 *
 * Control de Stock por cantidades, solo almacena las cantidades en una tabla por articulo.
 * Es un control de stock simple para cuando no es necesario llevar registro de los almacenes
 *
 * Los articulos que tengan este tipo de control de stock no pueden ser usados para produccion ni generan trazabilidad
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 * @class 	Almacenes_Model_DbTable_ArticulosStock
 * @extends	Rad_Db_Table
 */
class Almacenes_Model_DbTable_ArticulosStock extends Rad_Db_Table
{
    protected $_name = 'ArticulosStock';

    protected $_referenceMap = array(
        'Articulos' => array(
            'columns'        => 'Articulo',
            'refTableClass'  => 'Base_Model_DbTable_Articulos',
            'refJoinColumns' => array("Descripcion"),
            'refTable'       => 'Articulos',
            'refColumns'     => 'Id'
        )
    );

    public function getStock($idArticulo, $fecha = null)
    {
        if (!$fecha) $fecha = date('Y-m-d H:i:s');

        $row = $this->fetchRow("Articulo = $idArticulo AND Fecha <= '$fecha'", array('Fecha Desc'));

        if ($row) return $row->Stock;
        return 0;
    }

    public function actualizarStock($row)
    {
        // Traemos el tipo de comprobante
        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes;
        $R_TC = $M_TC->find($row->TipoDeComprobante)->current();

        // Los ticket factura reducen stock directamente sin remito
        if ($row->getTable() instanceof Facturacion_Model_DbTable_TicketFacturas && $R_TC->Grupo == 6) {
            $this->quitaStock($row, $row->FechaCierre);
        } else { // analisamos si es un remito
            switch ($R_TC->Grupo) {
                // Entra mercaderia
                case 4:
                    $this->agregaStock($row, $row->FechaEntrega);
                    break;
                // Sale mercaderia
                case 10:
                    $this->quitaStock($row, $row->FechaCierre);
                    break;
            }
        }
    }

    public function anularStock($row)
    {
        // Traemos el tipo de comprobante
        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes;
        $R_TC = $M_TC->find($row->TipoDeComprobante)->current();

        switch ($R_TC->Grupo){
            // Entra mercaderia
            case 4:
                $this->quitaStock($row, $row->FechaEntrega);
                break;
            // Sale mercaderia
            case 10:
                $this->agregaStock($row, $row->FechaCierre);
                break;
        }
    }

    protected function agregaStock($comprobante, $fecha)
    {
        $rowset = $this->getArticulosComprobante($comprobante);
        foreach($rowset as $remitoArticulo) {

            $articulo = $remitoArticulo->findParentRow('Base_Model_DbTable_Articulos');

            if ($articulo->TipoDeControlDeStock == 2) {
                $this->agregarStockArticulo($articulo, $remitoArticulo->Cantidad, $fecha);
            }
        }
    }


    protected function quitaStock($comprobante, $fecha)
    {
        $rowset = $this->getArticulosComprobante($comprobante);

        foreach($rowset as $remitoArticulo) {
            $articulo = $remitoArticulo->findParentRow('Base_Model_DbTable_Articulos');
            if ($articulo->TipoDeControlDeStock == 2) {
                $this->quitarStockArticulo($articulo, $remitoArticulo->Cantidad, $fecha);
            }
        }
    }

    protected function agregarStockArticulo($articulo, $cantidad, $fecha = null)
    {
        $this->_db->beginTransaction();

        if (!$fecha) $fecha = date('Y-m-d H:i:s');

        $row = $this->fetchRow("Articulo = $articulo->Id and Fecha = '$fecha'");

        if (!$row) {
            $actual = $this->getStock($articulo->Id, $fecha);
            $this->insert(
                array(
                    'Articulo' => $articulo->Id,
                    'Stock'    => $actual+$cantidad,
                    'Fecha'    => $fecha
                )
            );
        } else {
            $row->Stock += $cantidad;
            $row->save();
        }
        $this->_db->query("UPDATE ArticulosStock set Stock = (Stock + $cantidad) where Fecha > '$fecha' and Articulo = $articulo->Id");

        $this->_db->commit();
    }

    protected function quitarStockArticulo($articulo, $cantidad, $fecha = null)
    {
        $this->_db->beginTransaction();

        if (!$fecha) $fecha = date('Y-m-d H:i:s');
        $row = $this->fetchRow("Articulo = $articulo->Id and Fecha = '$fecha'");
        if (!$row) {
            $actual = $this->getStock($articulo->Id, $fecha);
            $this->insert(
                array(
                    'Articulo' => $articulo->Id,
                    'Stock'    => $actual-$cantidad,
                    'Fecha'    => $fecha
                )
            );
        } else {
            $row->Stock -= $cantidad;
            $row->save();
        }
        $this->_db->query("UPDATE ArticulosStock set Stock = (Stock - $cantidad) where Fecha > '$fecha' AND Articulo = $articulo->Id");

        $this->_db->commit();
    }

    public function regenerarStock()
    {

    }

    protected function getArticulosComprobante($comprobante)
    {
        $modelRemitoArticulos = new Almacenes_Model_DbTable_RemitosArticulos();

        $rowset = $modelRemitoArticulos->fetchAll("Comprobante = $comprobante->Id");
        return $rowset;
    }
}