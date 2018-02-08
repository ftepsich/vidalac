<?php
require 'Abstract.php';

/**
 * Publica el cierre de una Factura
 */
class Task_PublicarCierre extends Task_Abstract
{
    public function run($id) {
        $fc = new Facturacion_Model_DbTable_FacturasCompras();
        $fv = new Facturacion_Model_DbTable_FacturasVentas();
        $fop = new Facturacion_Model_DbTable_OrdenesDePagos();
        $r = new Facturacion_Model_DbTable_Recibos();

        if (!is_numeric($id)) die('Tiene que pasar un Id numérico');

        $rows = $fc->fetchAll("Id = $id");

        if (count($rows) == 0) {
            $rows = $fv->fetchAll("Id = $id");
        }

        if (count($rows) == 0) {
            $rows = $fop->fetchAll("Id = $id");
        }

        if (count($rows) == 0) {
            $rows = $r->fetchAll("Id = $id");
        }

        foreach ($rows as $c) {
            echo "Publicando Cierre de comprobante $c->Id, número $c->Numero\n";
            Rad_PubSub::publish('Comprobante_Cerrar', $c);
        }
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Publica un cierre de comprobante\nDebe pasarse un ID de comprobante\nEj: cm PublicarCierre 5";
    }
}

