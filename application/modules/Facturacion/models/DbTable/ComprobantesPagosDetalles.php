<?php
/**
 * @class 		Facturacion_Model_DbTable_ComprobantesPagosDetalles
 * @extends		Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
abstract class Facturacion_Model_DbTable_ComprobantesPagosDetalles extends Facturacion_Model_DbTable_ComprobantesDetalles
{
    /**
     * Inserta un registro
     * @param array $data
     */
    public function insert($data)
    {
        // $M_CP    = $this->getCabecera();
        // $montoTotal = $M_CP->recuperarMontoAPagar($data['Comprobante']);
        // $pagado     = $M_CP->recuperarTotalPagos($data['Comprobante']);
        // $exceso =  ($pagado + $data['PrecioUnitario']) - $montoTotal;

        // if ($exceso > 0.01) {
        //     throw new Rad_Db_Table_Exception('El pago excede el monto a pagar en $'.$exceso);
        // }

        return parent::insert($data);
    }

	protected function getCabecera() {
		return  new Facturacion_Model_DbTable_ComprobantesPagos();
	}

    /**
     * Permite agregar un pago por una transaccion bancaria (transferencia o deposito)
     *
     * @param int $idComprobante    Identificador del Recibo
     * @param int $idTransaccion    Identificadores de las Transacciones Bancarias que se usaran como Cobro
     *
     * @return unknown_type
     */
    public function insertPagosTransacciones($idComprobante, $idTransacciones) {
        try {
            $this->_db->beginTransaction();

            $M_T   = new Base_Model_DbTable_TransaccionesBancarias(array(), false);
            $M_TTB = new Base_Model_DbTable_TiposDeTransaccionesBancarias(array(), false);

            $R_T = $M_T->find($idTransacciones);


            foreach ($R_T as $row) {
                // Verifico si la transaccion ya fue utilizada como pago
                if ($row->Utilizado)
                    throw new Rad_Db_Table_Exception('La transaccion ' . $row->Numero . ' ya fue usada en un Cobro.');

                // La actualizacion del Row debe actualizarse antes de agregarlo como pago
                $M_T->marcarComoUsada($row->Id);
                $R_TTB = $M_TTB->find($row->TipoDeTransaccionBancaria)->current();


                // Agrego el pago
                $pago = $this->createRow();
                $pago->Observaciones       = $R_TTB->Descripcion." - ".$row->Numero;
                $pago->Comprobante         = $idComprobante;
                $pago->TransaccionBancaria = $row->Id;
                $pago->PrecioUnitario      = $row->Monto;
                $pago->save();

                $respuesta[] = $pago->toArray();
            }

            $this->_db->commit();
            return $respuesta;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite agregar un pago por una tarjeta de credito
     *
     * @param int $idComprobante    Identificador de la Orden de Pago
     * @param int $idCupon          Identificadores del cupon de pago
     *
     * @return unknown_type
     */
    public function insertPagosTarjeta($idComprobante, $idCupon)
    {
        try {
            $this->_db->beginTransaction();

            $M_T = new Facturacion_Model_DbTable_TarjetasDeCreditoCupones;
            $R_T = $M_T->find($idCupon);

            foreach ($R_T as $row) {
                // Verifico si la transaccion ya fue utilizada como pago
                if ($row->Utilizado) throw new Rad_Db_Table_Exception('El cupon ' . $row->NumeroCupon . ' ya fue usado en un pago.');

                // La actualizacion del Row debe actualizarse antes de agregarlo como pago
                $M_T->marcarComoUsado($idCupon);

                $tarjeta = $row->findParentRow('Facturacion_Model_DbTable_TarjetasDeCredito');
                $tipoTarjeta = $tarjeta->findParentRow('Facturacion_Model_DbTable_TarjetasDeCreditoMarcas');

                // Agrego el pago
                $pago = $this->createRow();
                $pago->Observaciones          = $tipoTarjeta->Descripcion;
                $pago->Comprobante            = $idComprobante;
                $pago->TarjetaDeCreditoCupon  = $row->Id;
                $pago->PrecioUnitario         = $row->Monto;
                $pago->save();

                $row->Utilizado = 1;
                $row->save();

                $respuesta[] = $pago->toArray();
            }

            $this->_db->commit();
            return $respuesta;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite agregar un Cheque como forma de pago
     *
     * @param int $idRecibo         Identificador de la Orden de Pago
     * @param array $idCheques          Identificador de los cheques que se usaran como pago
     *
     */
    public function insertPagosCheques($idRecibo, $idCheques) {
        try {
            /**
             * Esta funcion solo debe llamarse desde una clase hija que sete $estadoChequeUtilizado
             * y $estadoChequeUtilizable
             */

            if (!$this->estadoChequeUtilizable || !$this->estadoChequeUtilizado)
            {
                throw new Rad_Exception('El método insertPagosCheques no puede llamarse desde ComprobantesPagosDetalles');
            }

            $this->_db->beginTransaction();

            $M_CH = new Base_Model_DbTable_Cheques(array(), false);
            $R_CH = $M_CH->find($idCheques);

            $respuesta = array();

            foreach ($R_CH as $row) {

                // Verifico si el cheque ya se uso en un pago
                if($row->ChequeEstado != $this->estadoChequeUtilizable) {
                    throw Rad_Exception('El cheque no se encuentra disponible.');
                }
                // Agrego el pago
                $pago = $this->createRow();
                $pago->Observaciones  = sprintf('Cheque: %1$08d', $row->Numero);
                $pago->Comprobante    = $idRecibo;
                $pago->Cheque         = $row->Id;
                $pago->PrecioUnitario = $row->Monto;
                $pago->save();

                // Marco el cheque como utilizado
                $row->ChequeEstado = $this->estadoChequeUtilizado;
                $row->setReadOnly(false);
                $row->save();

                $respuesta[] = $pago->toArray();
            }
            $this->_db->commit();
            return $respuesta;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite agregar un pago en efectivo de una caja determinada
     *
     * @param int $idComprobante    Identificador de la Orden de Pago
     * @param int $idCaja           Identificador de la Caja que se utilizara
     * @param decimal $monto        Monto a retirar de la caja y asignar al pago
     *
     * @return unknown_type
     */
    public function insertPagoEfectivo($idComprobante, $Monto, $Caja)
    {
        try {
            $this->_db->beginTransaction();

            // Agrego el pago, en esta instancia no se controla si el monto de la caja es suficiente
            // esta tarea se realiza al cerrar el pago.
            $pago = $this->createRow();
            $pago->Observaciones  = 'Efectivo';
            $pago->Comprobante    = $idComprobante;
            $pago->Caja           = $Caja;
            $pago->PrecioUnitario = $Monto;
            $id = $pago->save();

            $this->_db->commit();
            return $pago->toArray();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete
     *
     * Borra los registros indicados
     *
     * @param array $where  Registros que se deben eliminar
     *
     */
    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();

            $Cheques = array();
            $TransaccionBancaria = array();

            $rows = $this->fetchAll($where);
            if (!$rows) {
                throw new Rad_Db_Table_Exception('No se encuentra el comprobante a eliminar.');
            }

            // Armo un array solo con los cheques y transacciones bancarias ya que los mismos
            // cambiaron de estado cuando los seleccionamos
            foreach ($rows as $row) {
                if ($row->Cheque) {
                    $Cheques[] = $row->Cheque;
                }
                if ($row->Caja) {
                    $Caja[] = $row->Id;
                }
                if ($row->TransaccionBancaria) {
                    $TransaccionBancaria[] = $row->TransaccionBancaria;
                }
                if ($row->TarjetaDeCreditoCupon) {
                    $CuponesTarjetas[] = $row->TarjetaDeCreditoCupon;
                }
                // Verifico que no sea Concepto Impositivo
                if ($row->ComprobanteRelacionado) {
                    $M_C = new Facturacion_Model_DbTable_Comprobantes();
                    $M_C->salirSi_esComprobanteImpositivo($row->ComprobanteRelacionado);
                }
            }

            // Borro los registros del detalle
            parent::delete($where);

            // Marco los cheques como disponibles
            if (!empty($Cheques)) {
                $M_CH = new Base_Model_DbTable_Cheques(array(), false);
                $M_CH->update(array('ChequeEstado' => $this->estadoChequeUtilizable), "Id in (" . implode(',', $Cheques) . ")");
            }

            // Marco las transacciones bancarias como sin usar
            if (!empty($TransaccionBancaria)) {
                $M_T = new Base_Model_DbTable_TransaccionesBancarias(array(), false);
                $M_T->update(array('Utilizado' => 0), "Id in (" . implode(',', $TransaccionBancaria) . ")");
            }

            // Marco los cupones de las tarjetas como sin usar
            if (!empty($CuponesTarjetas)) {
                $M_TCC = new Facturacion_Model_DbTable_TarjetasDeCreditoCupones;
                $M_TCC->update(array('Utilizado' => 0), "Id in (" . implode(',', $CuponesTarjetas) . ")");
            }

            // Borro los movimientos de caja correspondientes
            if (!empty($Caja)) {
                $M_C = new Contable_Model_DbTable_CajasMovimientos(array(), false);
                $M_C->delete("ComprobanteDetalle in (" . implode(',', $Caja) . ")");
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}