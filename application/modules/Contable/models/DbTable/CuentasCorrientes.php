<?php

require_once 'Rad/Db/Table.php';

/**
 * Contable_Model_DbTable_CuentasCorrientes
 *
 * Cuenta corrientes
 * Este modelo captura el publish del cierre de comprobantes y hace los asientos
 * correspondientes en la Cta. Cte.
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_Model_DbTable_CuentasCorrientes
 * @extends Rad_Db_Table
 */
class Contable_Model_DbTable_CuentasCorrientes extends Rad_Db_Table {

    /**
     * Tabla mapeada de la DB
     * @var string
     */
    protected $_name = "CuentasCorrientes";
    protected $_sort = array('FechaComprobante ASC');

    /**
     * Mapa de referencias
     * @var array
     */
    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
        ),
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeComprobantes',
            'refColumns' => 'Id',
        ),
        'Comprobantes' => array(
            'columns' => 'Comprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array(''),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
        )
    );

    /**
     * Modelos dependientes
     * @var array
     */
    protected $_dependentTables = array();

    public function _getDescripcionComprobante($row) {
        $comprobantes = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

        $puntosDeVenta = new Base_Model_DbTable_PuntosDeVentas(array(), false);

        if (!$comprobantes->esComprobanteEntrada($row)) {
            $R_PV = $puntosDeVenta->find($row->Punto)->current();
            $punto = $R_PV->Numero;
            if (!$R_PV) {
                throw new Rad_Db_Table_Exception('No se encuentra el punto indicado.');
            }
        } else {
            $punto = $row->Punto;
        }

        return str_pad($punto, 5, "0", STR_PAD_LEFT) . '-' . str_pad($row->Numero, 8, "0", STR_PAD_LEFT);
    }

    /**
     * 	Asienta comprobantes escuchando el publish
     *
     *  @param Zend_Db_Table_Row $row El Row del comprobante que se quiere asentas
     */
    public function asentarComprobante($row) {
        $asiento = $this->createRow();
        $asiento->Persona = $row->Persona;
        $asiento->Comprobante = $row->Id;
        $asiento->FechaDeCarga = date('Y-m-d H:i:s');
        $asiento->FechaComprobante = date('Y-m-d', strtotime($row->FechaEmision));

        $TC = $row->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        switch ($TC->Grupo) {

            case 1: // Factura de Compra (H)
                $asiento->DescripcionComprobante = 'FC: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe = 0;
                break;

            case 6: // Factura de Venta (D)
                $asiento->DescripcionComprobante = 'FV: ' . $this->_getDescripcionComprobante($row);
                // 1: Cta Cte - 2 Contado, de ser contado se debe compensar la cta cte
                $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                if ($row->CondicionDePago == 2) {
                    $asiento->Haber = $asiento->Debe;
                } else {
                    $asiento->Haber = 0;
                }
                break;

            case 7: // Notas de Credito Emitidas (H)
                $asiento->DescripcionComprobante = 'NCE: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe = 0;
                if ($row->EsProveedor == 1) {
                    $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Haber = 0;
                }
                break;

            case 8: // Notas de Credito Recibida (D)
                $asiento->DescripcionComprobante = 'NCR: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                if ($row->EsCliente == 1) {
                    $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Haber = 0;
                }
                break;

            case 9: // Orden de Pago (D)
                $asiento->DescripcionComprobante = 'OP: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                // Rad_Log::debug(get_class($row->getTable()));
                $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                // Rad_Log::debug( $asiento->toArray());
                break;

            case 11: // Recibos (H)
                $asiento->DescripcionComprobante = 'RC: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe = 0;
                break;

            case 12: // Notas de Debito Emitidas (D)
                $asiento->DescripcionComprobante = 'NDE: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                if ($row->EsProveedor == 1) {
                    $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Debe = 0;
                }
                break;

            case 13: // Notas de Debito Recibida (H)
                $asiento->DescripcionComprobante = 'NDR: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe = 0;
                if ($row->EsCliente == 1) {
                    $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Debe = 0;
                }
                break;

            case 21: // Comprobantes sin IVA (H)
                $asiento->DescripcionComprobante = 'CSI: ' . $row->NumeroSinIVA;
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe = 0;
                break;

            case 22: // Orden de Pago (D)
                $asiento->DescripcionComprobante = 'OPSI: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                break;

        }

        $this->delete("Comprobante = $row->Id");

        if ($asiento->Haber || $asiento->Debe) {
            $asiento->TipoDeComprobante = $row->TipoDeComprobante;
            $asiento->save();
        }
    }

    /**
     *  Asienta Compensaciones en Recibos y Ordenes de Pago
     *
     *  @param Zend_Db_Table_Row $row El Row del comprobante que se quiere asentas
     */
    public function asentarCompensaciones($row) {

        $tipoDeComprobante = $row->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        if ($tipoDeComprobante->Id == 7) { // Orden de Pago

            $sql = "SELECT IFNULL(SUM(CR.MontoAsociado),0) 
                    FROM ComprobantesRelacionados CR 
                    JOIN Comprobantes C ON CR.ComprobanteHijo = C.Id 
                    WHERE CR.ComprobantePadre = $row->Id
                      AND C.TipoDeComprobante IN ( 24, 25, 26, 27, 28 ) 
                      AND C.Cerrado           = 1
                      AND C.Anulado           = 0";

            $TotalMontoCompensacion = $this->_db->fetchOne($sql);
            if ($TotalMontoCompensacion > 0) {

                // Compensación Ventas en Orden de Pago
                $asiento = $this->createRow();
                $asiento->Persona = $row->Persona;
                $asiento->Comprobante = $row->Id;
                $asiento->FechaDeCarga = date('Y-m-d H:i:s');
                $asiento->FechaComprobante = date('Y-m-d', strtotime($row->FechaEmision));
                $asiento->DescripcionComprobante = 'OP: ' . $this->_getDescripcionComprobante($row);
                $asiento->Debe = 0;
                $asiento->Haber = $TotalMontoCompensacion;
                $asiento->TipoDeComprobante = 67;
                $asiento->save();

                // Compensación Compras en Orden de Pago
                $asiento = $this->createRow();
                $asiento->Persona = $row->Persona;
                $asiento->Comprobante = $row->Id;
                $asiento->FechaDeCarga = date('Y-m-d H:i:s');
                $asiento->FechaComprobante = date('Y-m-d', strtotime($row->FechaEmision));
                $asiento->DescripcionComprobante = 'OP: ' . $this->_getDescripcionComprobante($row);
                $asiento->Debe = $TotalMontoCompensacion;
                $asiento->Haber = 0;
                $asiento->TipoDeComprobante = 68;
                $asiento->save();
            }
        }

        if ($tipoDeComprobante->Id == 48) { // Recibo
            $sql = "SELECT ABS(IFNULL(SUM(TC.Multiplicador*CR.MontoAsociado),0))
                    FROM ComprobantesRelacionados CR
                    JOIN Comprobantes C ON CR.ComprobanteHijo = C.Id
                    JOIN Tiposdecomprobantes TC ON C.TipoDeComprobante = TC.Id
                    WHERE CR.ComprobantePadre = $row->Id
                      AND TC.Grupo IN (1,8,13)
                      AND C.EsCliente = 0
                      AND C.Cerrado   = 1
                      AND C.Anulado   = 0";

            $TotalMontoCompensacion = $this->_db->fetchOne($sql);

            if ($TotalMontoCompensacion <> 0) {

                // Compensación Ventas en Recibo
                $asiento = $this->createRow();
                $asiento->Persona = $row->Persona;
                $asiento->Comprobante = $row->Id;
                $asiento->FechaDeCarga = date('Y-m-d H:i:s');
                $asiento->FechaComprobante = date('Y-m-d', strtotime($row->FechaEmision));
                $asiento->DescripcionComprobante = 'RC: ' . $this->_getDescripcionComprobante($row);
                $asiento->Debe = $TotalMontoCompensacion;
                $asiento->Haber = 0;
                $asiento->TipoDeComprobante = 67;
                $asiento->save();

                // Compensación Compras en Recibo
                $asiento = $this->createRow();
                $asiento->Persona = $row->Persona;
                $asiento->Comprobante = $row->Id;
                $asiento->FechaDeCarga = date('Y-m-d H:i:s');
                $asiento->FechaComprobante = date('Y-m-d', strtotime($row->FechaEmision));
                $asiento->DescripcionComprobante = 'RC: ' . $this->_getDescripcionComprobante($row);
                $asiento->Debe  = 0;
                $asiento->Haber = $TotalMontoCompensacion;
                $asiento->TipoDeComprobante = 68;
                $asiento->save();
            }
        }

    }

    public function fetchCuentaCorriente($where = null, $order = null, $count = null, $offset = null) {
        $condicion = "CuentasCorrientes.TipoDeComprobante not in (67, 68)"; // No presentar los asientos de compensaciones sobre Orden de Pago.
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    /**
     * 	Quita un comprobante de la cuenta corriente
     *
     *  @param Zend_Db_Table_Row $row El Row del comprobante que se quiere quitar de la cuenta corriente
     */
    public function quitarComprobante($row) {
        if ($row->Anulado == 1) {
            $this->delete('Comprobante = ' . $row->Id);
        }
    }

    /**
     *  Quita un comprobante de la cuenta corriente
     *
     *  @param Int $idPersona id de la persona
     */
    public function getSaldo($idPersona, $aFecha = null) {

        $select = $this->select();

        $select->from($this, array('IfNull(Sum(Debe),0) AS saldoDebe', 'IfNull(Sum(Haber),0) AS saldoHaber'));

        $select->where("Persona = $idPersona AND TipoDeComprobante not in (67,68)");

        if ($aFecha)
            $select->where("FechaComprobante <= '$aFecha'");

        return ( $this->fetchRow($select)->saldoDebe - $this->fetchRow($select)->saldoHaber );

    }

    public function fetchCuentaCorrienteComoCliente($where = null, $order = null, $count = null, $offset = null) {
        $condicion = "( 
            (TiposDeComprobantes.Grupo IN (6,7,11,12,19) AND TiposDeComprobantes.Id NOT IN (65,66) AND Comprobantes.EsProveedor = 0) 
            OR (TiposDeComprobantes.Grupo IN (1,8,13,15) AND Comprobantes.EsCliente = 1) 
            OR (fNumeroCompleto(CuentasCorrientes.Comprobante,'S') COLLATE utf8_general_ci LIKE '%Saldo s/Recibo%') 
            )";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
        }
        
        public function fetchCuentaCorrienteComoProveedor($where = null, $order = null, $count = null, $offset = null) {
        $condicion = "(
             (TiposDeComprobantes.Grupo IN (1,8,9,13,15,20,21,22) AND Comprobantes.EsCliente = 0)
             OR (TiposDeComprobantes.Grupo in (6,7,12) AND Comprobantes.EsProveedor = 1) 
             OR (fNumeroCompleto(CuentasCorrientes.Comprobante,'S') COLLATE utf8_general_ci LIKE '%Saldo s/Orden de Pago%') 
             )";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
        }
}
