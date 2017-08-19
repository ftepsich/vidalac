<?php

require_once 'FacturasVentasArticulos.php';

/**
 * Ticket Factura Articulos
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 */
class Facturacion_Model_DbTable_TicketFacturasArticulos extends Facturacion_Model_DbTable_FacturasVentasArticulos
{
//    public function insert ($data)
//    {
//        $this->_db->beginTransaction();
//        try {
//            $id = parent::insert($data);
//
//            $item = $this->find($id)->current();
//
//            $M_FV = Service_TableManager::get('Facturacion_Model_DbTable_TicketFacturas');
//
//            $factura = $M_FV->find($item->Comprobante)->current();
//
//            $fiscalizador = new Facturacion_Model_Fiscalizar();
//
//            $fiscalizador->agregarItem($item, $factura);
//
//            $this->_db->commit();
//            return $id;
//        } catch (Exception $e) {
//            $this->_db->rollBack();
//            throw $e;
//        }
//    }
//
//    public function update ($data, $where)
//    {
//        throw new Rad_Db_Table_Exception('No se puede modificar un ticket/factura fiscalizado');
//    }
//
//    public function delete($where)
//    {
//        throw new Rad_Db_Table_Exception('No se puede borrar un ticket/factura fiscalizado');
//    }
}