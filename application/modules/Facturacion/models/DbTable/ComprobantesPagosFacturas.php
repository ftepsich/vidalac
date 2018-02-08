<?php

require_once 'Rad/Db/Table.php';

/**
 * Comprobantes Relacionados para Pagos o Cobros
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_ComprobantesPagosFacturas extends Facturacion_Model_DbTable_ComprobantesRelacionados
{
    protected $noPermitirHijoAbierto = true;


    /**
     * Retorna las Ordenes de pagos o recibos
     * correspondientes al pago de la factura de compra/venta
     *
     * @param  int $idFactura    id de la factura
     * @return array             comprobantes de pagos
     */
    static public function retornarComprobantePago($idFactura)
    {
        $db = Zend_Registry::get('db');

        $db->quote($idFactura, 'INTEGER');

        $sql = "SELECT c.* FROM `ComprobantesRelacionados` cr
                inner join Comprobantes c on cr.ComprobantePadre = c.Id
                inner join TiposDeComprobantes tc on c.TipoDeComprobante = tc.Id
                where ComprobanteHijo = $idFactura and (tc.Grupo = 11 or tc.Grupo = 9)";
        $existe = $db->fetchAll($sql);

        return $existe;
    }

    /**
     * Retorna las facturas pagadas por un comprobante Recibo u Orden de pago
     *
     * @param  int $idFactura    id de la factura
     * @return array             comprobantes de pagos
     */
    static public function retornarComprobantePagado($idComprobantePago)
    {
        $db = Zend_Registry::get('db');
        $db->quote($idFactura, 'INTEGER');

        $sql = "SELECT c.* FROM `ComprobantesRelacionados` cr
            inner join Comprobantes c on cr.ComprobanteHijo = c.Id
            inner join TiposDeComprobantes tc on c.TipoDeComprobante = tc.Id
            where cr.ComprobantePadre = $idComprobantePago  and (tc.Grupo = 1 or tc.Grupo =  6)";
        $existe = $db->fetchAll($sql);

        return $existe;
    }

    /**
     * Inserta un registro en Comprobantes Relacionados y agrega lo disponible en OPFD o en el caso que este cerrada la Orden De Pago
     * asocia lo ingresado manualmente a la OPD con lo que este sin pagar del comprobante indicado.
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        $this->_db->beginTransaction();
        try {

            $idHijo     = $data['ComprobanteHijo'];
            $idCompPago = $data['ComprobantePadre'];

            if ($idHijo && $idCompPago) {

                $M_H = new Facturacion_Model_DbTable_Comprobantes();
                $M_H->salirSi_noExiste($idHijo);
                if ($this->noPermitirHijoAbierto) $M_H->salirSi_noEstaCerrado($idHijo);

                $this->salirSi_hijoYaAsignadoEnComprobanteAbierto($idCompPago, $idHijo);


                $id = parent::insert($data);

            } else {
                throw new Rad_Db_Table_Exception('Faltan datos necesarios.');
            }

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function hijoYaAsignadoEnComprobanteAbierto($id, $hijo)
    {
        /*$id   = $this->_db->quote($id,'INTEGER');
        $hijo = $this->_db->quote($hijo,'INTEGER');

        $select = $this->_db->select()
             ->from(array('c' => 'Comprobantes'),
                    array('Id'))
             ->join(array('cr' => 'ComprobantesRelacionados'),
                    'c.Id = cr.ComprobantePadre AND cr.ComprobanteHijo = '.$hijo)
             ->where("c.Id <> $id AND c.Cerrado <> 1");

        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();

        return ($result !== null);*/
    }

    public function salirSi_hijoYaAsignadoEnComprobanteAbierto($id, $hijo)
    {
        if ($this->hijoYaAsignadoEnComprobanteAbierto($id, $hijo)) {
            throw new Rad_Exception('El comprobante Ya se encuentra asignado a otro comprobante abierto');
        }
    }
}
