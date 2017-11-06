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
class Contable_Model_DbTable_CuentasCorrientes extends Rad_Db_Table
{
    /**
     * Tabla mapeada de la DB
     * @var string
     */
    protected $_name = "CuentasCorrientes";

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
            'refJoinColumns' => array('EsCliente'),
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

    public function _getDescripcionComprobante($row)
    {
        $M_C = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

        $M_PV = new Base_Model_DbTable_PuntosDeVentas(array(), false);

        if(!$M_C->esComprobanteEntrada($row)){
            $R_PV  = $M_PV->find($row->Punto)->current();
            $punto = $R_PV->Numero;
            if (!$R_PV) {
                throw new Rad_Db_Table_Exception('No se encuentra el punto indicado.');
            }
        } else {
            $punto = $row->Punto;
        }

        return str_pad($punto, 4, "0", STR_PAD_LEFT) . '-' . str_pad($row->Numero, 8, "0", STR_PAD_LEFT);
    }

    /**
     * 	Asienta comprobantes escuchando el publish
     *
     *  @param Zend_Db_Table_Row $row El Row del comprobante que se quiere asentas
     */
    public function asentarComprobante($row)
    {
        $asiento = $this->createRow();
        $asiento->Persona          = $row->Persona;
        $asiento->Comprobante      = $row->Id;
        $asiento->FechaDeCarga     = date('Y-m-d H:i:s');
        $asiento->FechaComprobante = date('Y-m-d', strtotime($row->FechaEmision));

        $TC = $row->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        switch ($TC->Grupo) {

            case 1: // Factura de Compra (H)
                $asiento->DescripcionComprobante = 'FC: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe  = 0;
                break;

            case 6: // Factura de Venta (D)
                $asiento->DescripcionComprobante = 'FV: ' . $this->_getDescripcionComprobante($row);
                // 1: Cta Cte - 2 Contado, de ser contado se debe compensar la cta cte
                $asiento->Debe  = $row->getTable()->recuperarMontoTotal($row->Id);
                if ($row->CondicionDePago == 2) {
                    $asiento->Haber = $asiento->Debe;
                } else {
                    $asiento->Haber = 0;
                }
                break;

            case 7: // Notas de Credito Emitidas (H)
                $asiento->DescripcionComprobante = 'NCE: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe  = 0;
                if ( $row->EsProveedor == 1 ) {
                    $asiento->Debe  = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Haber = 0;
                }
                break;

            case 8: // Notas de Credito Recibida (D)
                $asiento->DescripcionComprobante = 'NCR: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                $asiento->Debe  = $row->getTable()->recuperarMontoTotal($row->Id);
                if ( $row->EsCliente == 1 ) {
                    $asiento->Debe = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Haber  = 0;
                }
                break;

            case 9: // Orden de Pago (D)
                $asiento->DescripcionComprobante = 'OP: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                // Rad_Log::debug(get_class($row->getTable()));
                $asiento->Debe  = $row->getTable()->recuperarMontoTotal($row->Id);
                // Rad_Log::debug( $asiento->toArray());
                break;

            case 11: // Recibos (H)
                $asiento->DescripcionComprobante = 'RC: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe  = 0;
                break;

            case 12: // Notas de Debito Emitidas (D)
                $asiento->DescripcionComprobante = 'NDE: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = 0;
                $asiento->Debe  = $row->getTable()->recuperarMontoTotal($row->Id);
                if ( $row->EsProveedor == 1 ) {
                    $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Debe  = 0;
                }
                break;

            case 13: // Notas de Debito Recibida (H)
                $asiento->DescripcionComprobante = 'NDR: ' . $this->_getDescripcionComprobante($row);
                $asiento->Haber = $row->getTable()->recuperarMontoTotal($row->Id);
                $asiento->Debe  = 0;
                if ( $row->EsCliente == 1 ) {
                    $asiento->Haber  = $row->getTable()->recuperarMontoTotal($row->Id);
                    $asiento->Debe = 0;
                }
                break;

        }

        $this->delete("Comprobante = $row->Id");

        if ($asiento->Haber || $asiento->Debe) {
            $asiento->TipoDeComprobante = $row->TipoDeComprobante;
            $asiento->save();
        }
    }

	/**
     * 	Quita un comprobante de la cuenta corriente
     *
     *  @param Zend_Db_Table_Row $row El Row del comprobante que se quiere quitar de la cuenta corriente
     */
    public function quitarComprobante($row)
    {
        if ($row->Anulado == 1) {
            $this->delete('Comprobante = '.$row->Id);
        }
    }


    /**
     *  Quita un comprobante de la cuenta corriente
     *
     *  @param Int $idPersona id de la persona
     */
    public function getSaldo($idPersona, $aFecha = null)
    {

        $select = $this->select();
        $select->from($this->_name,'Sum(Debe)-Sum(Haber) AS saldo');

        $select->where("Persona = $idPersona");

        if ($aFecha) $select->where("FechaComprobante <= '$aFecha'");

        return $this->fetchRow($select)->saldo;
    }

    public function fetchCuentaCorrienteComoCliente ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion  = "CuentasCorrientes.TipoDeComprobante in (
                            SELECT      C.Id
                            FROM        Comprobantes C
                            WHERE (
                            
					(TiposDeComprobantes.Grupo IN (6,7,11,12) and TiposDeComprobantes.Id not in (65,66) and Comprobantes.EsProveedor = 0 )
                                        OR (TiposDeComprobantes.Grupo = 13 AND Comprobantes.EsCliente = 1)
                                        OR (fNumeroCompleto(CuentasCorrientes.Comprobante,'S') COLLATE utf8_general_ci like '%Saldo s/Recibo%')
                                   )     
                    )";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchCuentaCorrienteComoProveedor ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion  = "CuentasCorrientes.TipoDeComprobante in (
                            SELECT      C.Id
                            FROM        Comprobantes C
			    WHERE       (
                                        (TiposDeComprobantes.Grupo in (1,8,9,13) and Comprobantes.EsCliente = 0)
                                        OR (TiposDeComprobantes.Grupo = 7 AND Comprobantes.EsProveedor = 1)
                                        OR (fNumeroCompleto(CuentasCorrientes.Comprobante,'S') COLLATE utf8_general_ci like '%Saldo s/Orden de Pago%')
                                        )
                    )";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}