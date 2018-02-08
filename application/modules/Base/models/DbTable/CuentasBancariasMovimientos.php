<?php

/**
 * Contable_DbTable_CajasMovimientos
 *
 * Detalle de los movimientos de las Cajas
 *
 * @copyright SmartSoftware Argentina
 * @class Contable_DbTable_CajasMovimientos
 * @extends Rad_Db_Table
 * @package Aplicacion
 * @subpackage Contable
 */
class Base_Model_DbTable_CuentasBancariasMovimientos extends Rad_Db_Table
{

    protected $_name = 'CuentasBancariasMovimientos';
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_sort = array('Fecha desc');

    protected $_referenceMap = array(
        'CuentasBancarias' => array(
            'columns' => 'CuentaBancaria',
            'refTableClass' => 'Base_Model_DbTable_VBancosCuentas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'VBancosCuentas',
            'refColumns' => 'CuentaBancariaId',
        ),
        'ComprobantesCheques' => array(
            'columns' => 'ComprobanteCheque',
            'refTableClass' => 'Facturacion_Model_DbTable_ComprobantesCheques',
            'refJoinColumns' => array('Comprobante'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ComprobantesCheques',
            'refColumns' => 'Id',
        ),
        'Comprobantes' => array(
            'columns' => 'Comprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_ComprobantesBancarios',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
        )
    );

    protected $_calculatedFields = array(
        'NumeroCompleto' => "fNumeroCompleto(ComprobantesCheques.Comprobante,'') COLLATE utf8_general_ci"
    );

    public function init()
    {
        $this->_defaultValues = array (
            'Fecha' => date('Y-m-d H:i:s')
        );

        parent::init();
    }

    /**
     * Borra los movimientos de cajas q no esten asociados a un comprobante detalle
     *
     * @param array $where 	Registros que se deben eliminar
     */
    public function delete ($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R_CM) {

                if(!$R_CM->Comprobante){
                    parent::delete('Id =' . $R_CM->Id);
                } else {
                    // Veo que el comprobante este cerrado
                    $M_C = new Facturacion_Model_DbTable_Comprobantes;

                    Rad_Log::debug($R_CM->Comprobante);

                    $M_C->salirSi_estaCerrado($R_CM->Comprobante);
                    // Si esta abierto borro
                    parent::delete('Id =' . $R_CM->Id);
                }
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Recupera el monto de una caja determinada
     *
     * @param int $idCaja Identificador de la caja
     * @return float
     *
     */
    public function saldoCuentaCorriente($idCuentaCorriente) {
        $sql = "select  ifnull(sum(Monto),0)
                from    CuentasBancariasMovimientos CM fc
                where   CM.CuentaBancaria = $idCuentaCorriente";

        return $this->_db->fetchOne($sql);
    }

    /**
     * Asienta un movimiento de una caja que provengan de un comprobante determinado
     *
     * @param array $data   Valores que se insertaran
     *
     */
    public function asentarMovimientoDesdeComprobante($rowComprobante) {

        if (!$rowComprobante->Id) {
            throw new Rad_Db_Table_Exception("No se localiza el identificador del comprobante.");
        } else {

            $M_C = new Facturacion_Model_DbTable_Comprobantes(array());
            $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array());

            // Averiguo el grupo del comprobante, esto solo es para Ordenes de Pago y para Recibos
            $grupo = $M_C->recuperarGrupoComprobante($rowComprobante);

            if ($grupo == 14 || $grupo == 15 || $grupo == 16) {
                $multiplicador = 1;

                // Si habia registros de este comprobante los elimino primero para no duplicarlos
                $this->quitarMovimientoDesdeComprobante($rowComprobante);

                if ($grupo == 16) {
                    // Todo OK, asiento los movimientos de la Cuenta
                    $sql = " SELECT CC.Id AS ComprobanteCheque,
                                    CC.Comprobante AS IdComprobante,
                                    C.Id AS IdCheque,
                                    C.Monto,
                                    C.Numero AS NumeroCheque
                             FROM   ComprobantesCheques CC
                                    INNER JOIN Cheques C ON C.Id = CC.Cheque
                             WHERE  CC.Comprobante = {$rowComprobante['Id']}
                            ";

                    $R = $this->_db->fetchAll($sql);
                    foreach ($R as $row) {
                        // Armo un array del movimiento en cuenta
                        $Renglon = array(
                            'Descripcion'           => $M_C->recuperarDescripcionComprobante($row['IdComprobante']),
                            'CuentaBancaria'        => $rowComprobante['CuentaBancaria'],
                            'Monto'                 => $row['Monto'] * $multiplicador,
                            'Fecha'                 => $rowComprobante['FechaEmision'],
                            'ComprobanteCheque'     => $row['ComprobanteCheque']
                        );
                        $this->insert($Renglon);
                    } // foreach ($R as $row)
                }  // if ($grupo == 16)

                // Todo OK, asiento los movimientos de la Cuenta
                $sqlComprobante = " SELECT  C.Id,
                                SUM(CD.PrecioUnitario) AS MontoDetalle,
                                SUM(CC.Monto) AS MontoConcepto
                         FROM   Comprobantes C
                                INNER JOIN ComprobantesDetalles CD ON C.Id = CD.Comprobante
                                -- OJO left por que no siempre viene
                                LEFT JOIN Comprobantes CC ON C.Id = CC.ComprobantePadre
                         WHERE  C.Id = {$rowComprobante['Id']}
                                                ";
                $R_C = $this->_db->fetchRow($sqlComprobante);
                // Armo un array del movimiento en cuenta
                $Renglon = array(
                    'Descripcion'           => $M_C->recuperarDescripcionComprobante($rowComprobante->Id),
                    'CuentaBancaria'        => $rowComprobante['CuentaBancaria'],
                    'Monto'                 => (($R_C['MontoDetalle'] + $R_C['MontoConcepto']) * (-1)),
                    'Fecha'                 => $rowComprobante['FechaEmision'],
                    'Comprobante'           => $rowComprobante->Id
                );
                $this->insert($Renglon);

            } // ($grupo == 14 || $grupo == 15 || $grupo == 16)
        }
        return true;
    }

    /**
     * Quitar movimiento de una cuenta bancaria que provengan de un comprobante determinado
     *
     * @param array $data   Valores que se insertaran
     *
     */
    public function quitarMovimientoDesdeComprobante($rowComprobante) {

        if (!$rowComprobante->Id) {
            throw new Rad_Db_Table_Exception("No se localiza el identificador del comprobante.");
        } else {
            try {
                $this->_db->beginTransaction();

                $M_CC = new Facturacion_Model_DbTable_ComprobantesCheques(array());
                $M_CC->eliminarRelacionesHijosCheques($rowComprobante);

                /*
                $R_CC = $M_CC->fetchAll("Comprobante = $rowComprobante->Id");
                if ($R_CC) {
                    foreach ($R_CC as $row) {
                        $this->delete("ComprobanteCheque = ".$row->Id);
                    }
                */

                parent::delete("Comprobante = ".$rowComprobante->Id);
                // puesto con parent por que el delete controla que este cerrado
                // y cuando se ejecuta esta funcion el comprobante esta cerrado y
                // debe poder ejecutarse
                // $this->delete("Comprobante = ".$rowComprobante->Id);
                $this->_db->commit();
                return true;
            } catch (Exception $e) {
                $this->_db->rollBack();
                throw $e;
            }
        }
    }


    /**
     * Asienta un movimiento de una cuenta bancaria que provengan de una Transaccion
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function asentarMovimientoDesdeTransaccion($rowTransaccion) {
       Rad_Log::debug("Paso2");
        $rt = $rowTransaccion;

        if ($rt->CtaOrigen || $rt->CtaDestino) {
            try {
                $this->_db->beginTransaction();
                // Veo que tenga el monto sino tiro error
                if ($rt->Monto < 0.0001) {
                    throw new Rad_Db_Table_Exception("No se ha ingresado el monto o el mismo es menor que cero.");
                }
                $Monto = $rt->Monto;
                $Cuenta = 0;
                // Veo que signo tiene que tener el monto basado en el tipo de Transaccion
                if ($rt->TipoDeMovimiento == 2 || $rt->TipoDeTransaccionBancaria == 3) {
                    $Monto = $Monto * (-1);
                    If( $rt->TipoDeTransaccionBancaria == 3){
                       $Descripcion = "Extraccion Bancaria (De Cuenta Propia a Caja Propia)";
                       $Cuenta = $rt->CtaDestino;
                    }
                    If( $rt->TipoDeTransaccionBancaria == 1){
                       $Descripcion = "Transferencia Saliente (De Cuenta Propia a Cuenta Proveedor)";
                       $Cuenta = $rt->CtaOrigen;
                    }
                } else {
                    If( $rt->TipoDeTransaccionBancaria == 2){
                       If( $rt->TipoDeMovimiento == 1){
                           $Descripcion = "Deposito Entrante (De Cliente a Cuenta Propia)";
                       }
                       If( $rt->TipoDeMovimiento == 3){
                           $Descripcion = "Deposito Entrante (De Caja Propia a Cuenta Propia)";
                       }

                    }
                    If( $rt->TipoDeTransaccionBancaria == 1){
                       $Descripcion = "Transferencia Entrante (De Cliente a Cuenta Propia)";
                    }
                    $Cuenta = $rt->CtaDestino;
                }

                if ($rt->Numero) $Descripcion = $Descripcion." N: ".$rt->Numero;

                // Armo un array del movimiento en caja
                $RenglonCuentaBancariaMovimiento = array(
                    'Descripcion'           => $Descripcion,
                    'CuentaBancaria'        => $Cuenta,
                    'Monto'                 => $Monto,
                    'Fecha'                 => $rt->Fecha,
                    'Observaciones'         => $rt->Observaciones,
                    'TransaccionBancaria'  => $rt->Id
                );

                //Inserto
                $id = $this->insert($RenglonCuentaBancariaMovimiento);

                $this->_db->commit();
                return true;
            } catch (Exception $e) {
                $this->_db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Quitar movimiento de una cuenta bancaria que provengan de una Transaccion
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function quitarMovimientoDesdeTransaccion($rowTransaccion) {

        $rt = $rowTransaccion;

        if ($rt->Id) {
            try {
                $this->_db->beginTransaction();
                $this->delete("TransaccionBancaria = $rt->Id ");
                $this->_db->commit();
                return true;
            } catch (Exception $e) {
                $this->_db->rollBack();
                throw $e;
            }
        }
    }

    /**
     *  Permite mover dinero de una cuenta a otra.
     *
     * @param array $CuentaOrigen
     * @param array $CuentaDestino
     * @param array $Descripcion
     * @param array $Monto
     * @param array $Transaccion
     * @param array $Fecha
     *
     */
    public function movimientosEntreCuentas($rowTransaccion) {
        try {
            $this->_db->beginTransaction();

            $rt = $rowTransaccion;

            $M_C = new Base_Model_DbTable_VBancosCuentas(array(), false);

            $R_CO = $M_C->find($rt->CtaOrigen)->current();

            if (!count($R_CO)) {
                throw new Rad_Db_Table_Exception("No se encontro la Cuenta Origen.");
            }

            $R_CD = $M_C->find($rt->CtaDestino)->current();

            if (!count($R_CD)) {
                throw new Rad_Db_Table_Exception("No se encontro la Cuenta Destino.");
            }

            if ($rt->Monto <= 0) {
                throw new Rad_Db_Table_Exception("El monto debe ser mayor a 0 (cero).");
            }

            $RenglonExtraccionCuenta = array(
                'Descripcion'           => 'Movimiento entre Cuentas Propias. Se transfirio a la Cuenta: '.$R_CD->Descripcion,
                'CuentaBancaria'        => $rt->CtaOrigen,
                'Monto'                 => $rt->Monto*(-1),
                'Fecha'                 => $rt->Fecha,
                'TransaccionBancaria'   => $rt->Id,
                'Observaciones'         => $rt->Observaciones
            );

            $this->insert($RenglonExtraccionCuenta);

            $RenglonIngresoCuenta = array(
                'Descripcion'           => 'Movimiento entre Cuentas Propias. Ingreso de la cuenta: '.$R_CO->Descripcion,
                'CuentaBancaria'        => $rt->CtaDestino,
                'Monto'                 => $rt->Monto,
                'Fecha'                 => $rt->Fecha,
                'TransaccionBancaria'   => $rt->Id,
                'Observaciones'         => $rt->Observaciones
            );
            $this->insert($RenglonIngresoCuenta);
            $this->_db->commit();

            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchDeEntrada($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "CajasMovimientos.Monto >= 0";
        $where = $this->_addCondition($where,$condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchDeSalida($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "CajasMovimientos.Monto < 0";
        $where = $this->_addCondition($where,$condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}
