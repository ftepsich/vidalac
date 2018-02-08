<?php
/**
 * Ticket Factura
 *
 * Ticket Facturas para puntos de ventas con impresoras Fiscal
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_Model_DbTable_TicketFactura
 * @extends     Facturacion_Model_DbTable_FacturasVentas
 */
class Facturacion_Model_DbTable_TicketFacturas extends Facturacion_Model_DbTable_FacturasVentas
{
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(54, 56, 29, 30, 24, 25)
    );

    public function init()
    {
        // Por defecto la condicion de pago del ticket es contado
        $this->_defaultValues['CondicionDePago'] = 2;
        $this->_referenceMap['TiposDeComprobantes'] = array(
            'columns'        => 'TipoDeComprobante',
            'refTableClass'  => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array(
                'Descripcion',
                'MontoSigno' => '(TiposDeComprobantes.Multiplicador * Comprobantes.Monto)'
            ),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/PuntoDeVenta',
            'refTable'       => 'TipoDeComprobante',
            'refColumns'     => 'Id'
        );
        parent::init();
    }

    public function cerrar($id)
    {
        try {
            $this->_db->beginTransaction();
            $factura = $this->find($id)->current();

            $tipoComp = $factura->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

            if ($factura->CondicionDePago != 1 && $tipoComp->Grupo == 6) {
                $idRecibo = $this->getIdComprobantePago($id);
                $recibosModel = new Facturacion_Model_DbTable_RecibosFicticios;
                $recibosModel->cerrar($idRecibo);
            }

            $r = parent::cerrar($id);
            $this->_db->commit();
        }
        catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

        return $r;
    }

    public function insert($data)
    {
        $this->_db->beginTransaction();
        try {
            $id = parent::insert($data);

            $factura = $this->find($id)->current();

            $tipoComp = $factura->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

            if ($factura->CondicionDePago != 1 && $tipoComp->Grupo == 6) {
                $this->_agregarComprobantePago($factura);
            }

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            $recibosModel = new Facturacion_Model_DbTable_RecibosFicticios;

            $facturas = $this->fetchAll($where);
            foreach($facturas as $factura) {
                $tipoComp = $factura->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
                // cambio de cuenta corriente a contado, agrego el comp de pago
                if ($tipoComp->Grupo == 6) {
                    if ($factura->CondicionDePago == 1 && $data['CondicionDePago'] == 2) $this->_agregarComprobantePago($factura);
                    else if($factura->CondicionDePago == 2 && $data['CondicionDePago'] == 1) {
                        $idRecibo = $this->getIdComprobantePago($factura->Id);
                        if ($idRecibo) $recibosModel->delete("Id = $idRecibo");
                    }
                }
            }
            $id = parent::update($data, $where);
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    protected function _agregarComprobantePago($factura)
    {
        $recibosModel = new Facturacion_Model_DbTable_RecibosFicticios;

        $recibo = $recibosModel->createRow();

        $recibo->Persona      = $factura->Persona;
        $recibo->Punto        = $factura->Punto;
        $recibo->Numero       = $recibosModel->generarNumeroRecibo($recibo->Punto, 58);
        $recibo->FechaEmision = $factura->FechaEmision;

        $idRecibo = $recibo->save();

        $reciboFactura = new Facturacion_Model_DbTable_RecibosFicticiosFacturas;
        $relacionFacturaRecibo = $reciboFactura->createRow();
        $relacionFacturaRecibo->ComprobantePadre = $idRecibo;
        $relacionFacturaRecibo->ComprobanteHijo  = $factura->Id;
        $relacionFacturaRecibo->save();
    }

    /**
     * retorna el Id de recibo con el que se realiza el pago
     * @param  int $idFactura id factura
     * @return int            id recibo
     */
    public function getIdComprobantePago($idFactura)
    {

        $reciboFactura = new Facturacion_Model_DbTable_RecibosFicticiosFacturas;
        $db = $reciboFactura->getAdapter();
        $idFactura = $db->quote($idFactura, 'INTEGER');
        $r = $reciboFactura->fetchRow("ComprobanteHijo = $idFactura");

        if (!$r) return null;
        return $r->ComprobantePadre;
    }

    /**
     *  Permite anular una factura y los comprobantes Hijos si corresponde
     *
     * @param int $idFactura    identificador de la factura a cerrar
     *
     */
    public function anular($idFactura)
    {
        throw new Exception('Se debe realizar una nota de credito para la anulacion de este tipo de comprobantes');
        $factura = $this->find($idFactura)->current();

        if (!$factura) throw new Rad_Db_Table_Exception("No se encontro la factura que desea anular");

        Facturacion_Model_DbTable_Facturas::cerrar($idFactura);
        parent::anular($idFactura);

        $idRecibo = $this->getIdComprobantePago($idFactura);
        if ($idRecibo) {
            $recibosModel = new Facturacion_Model_DbTable_RecibosFicticios;

            $recibosModel->delete("Id = $idRecibo");
        }
    }

    public function compensarFacturasConNotas($idComprobante) {
        parent::compensarFacturasConNotas($idComprobante);
        $idRecibo = $this->getIdComprobantePago($idFactura);

        if ($idRecibo) {
            $recibosModel = new Facturacion_Model_DbTable_RecibosFicticios;

            $recibosModel->anular($idRecibo);
        }
    }
}