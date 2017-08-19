<?php
require 'Abstract.php';

/**
 * Publica el cierre de una Factura
 */
class Task_AsentarEnCuentaCorriente extends Task_Abstract
{
    public function run($id) {

        if (!is_numeric($id)) die('Tiene que pasar un Id numérico');

        /* Debo buscar y pasar el tipo de row adecuado ya que los montosTotales se calculan dependiento de eso */

        // Inicio con los Comprobantes de Pago
        $M_CP = new Facturacion_Model_DbTable_ComprobantesPagos;
        $row = $M_CP->fetchRow("Id = $id");

        // Sigo con las facturas
        if (!count($row)) {
            $M_F = new Facturacion_Model_DbTable_Facturas;
            $row = $M_F->fetchRow("Id = $id");
        }

        // Termino con los comprobantes en General
        if (!count($row)) {
            $M_C = new Facturacion_Model_DbTable_Comprobantes;
            $row = $M_C->fetchRow("Id = $id");
        }

        if (count($row)) {
            $M_CC = new Contable_Model_DbTable_CuentasCorrientes;
            echo "Asentando comprobante $c->Id, número $c->Numero\n";
            $M_CC->asentarComprobante($row);
            echo "Comprobante $c->Id, número $c->Numero asentado\n";
        } else {
            echo "No se encuentra el comprobante a asentar en la cuenta corriente (identificador $id)";
        }
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Asienta un comprobante en la cuenta Corriente\n
              Debe pasarse un ID de comprobante\n
              Ej: cm AsentarEnCuentaCorriente 5";
    }
}