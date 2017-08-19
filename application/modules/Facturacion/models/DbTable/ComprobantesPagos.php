<?php

/**
 * @class 		Facturacion_Model_DbTable_ComprobantesPagos
 * @extends		Facturacion_Model_DbTable_Comprobantes
 *
 *
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * Id                   -> Identificador Unico
 * Persona              -> Cliente al que se le realiza el Cobro
 * TipoDeComprobante    -> (cte) = 5, 6, 7, 8 y 9
 * Punto                -> (cte) = 1
 * Numero               -> Numero del Recibo
 * FechaEmision         -> Fecha de generacion del Recibo
 * Divisa               -> Moneda en que esta expresada la factura
 * ValorDivisa          -> Valor de cambio de la divisa en el caso que este expresado en otra moneda
 * Cerrado              -> Indica si la factura es modificable o no.
 * Observaciones        -> Obs. internas
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 */
class Facturacion_Model_DbTable_ComprobantesPagos extends Facturacion_Model_DbTable_Comprobantes
{
    /**
     * Evento que se publicara cuando se borre un registro
     * @var string
     */
    protected $_onDeletePublish = 'Facturacion_CP_preBorrar';

    protected $_permanentValues = array(
        'TipoDeComprobante' => array(5, 6, 7, 8, 9, 48, 58)
    );

    /**
     * Indica si se debe verificar que no haya otro comprobante de pagos abierto antes de crear uno nuevo
     * @var boolean
     */
    protected $unicoAbierto = true;

    public function init()
    {
        parent::init();
        $this->_calculatedFields['MontoTotal'] = "fCompPago_Monto_aPagar(Comprobantes.Id)";
    }

    /**
     * 	Insert
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function insert($data) {

        // reviso que no exista otro abierto al momento de cargar este

        if ($this->unicoAbierto) $this->salirSi_existeOtroComprobanteSinCerrar($data['Persona'],$data['TipoDeComprobante'],null);

        // inserto
        return parent::insert($data);
    }

    /**
     * 	Update
     *
     * @param array $data 		Valores que se cambiaran
     * @param array $where 	Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();

            $M_CD = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesDetalles');
            $M_C  = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {

                $whereRow = ' Comprobantes.Id = ' . $row->Id;

                // Controles
                $this->salirSi_estaCerrado($row->Id);

                // Veo si la divisa es la local
                if (isset($data['Divisa']) && $data['Divisa'] != $row->Divisa) {
                    $config = Rad_Cfg::get();
                    if ($data['Divisa'] == $config->Base->DivisaLocal) {
                        $data['ValorDivisa'] = 1;
                    }
                }

                // Si se modifica el libro de IVA Cambiar el Libro a sus hijos (conceptos)
                if ($data['LibroIVA']) {
                    $M_C->update(
                            array(
                                'LibroIVA' => $data['LibroIVA']
                                ),
                            ' Comprobantes.ComprobantePadre = ' . $row->Id);
                }

                // Si se modifica el cliente debo arancar todo de cero
                if ($data['Persona'] && $data['Persona'] == $row->Persona) {

                    parent::update($data, $whereRow);
                    $id = $row->Id;
                } else {
                    $row->setFromArray($data);

                    // Ojo... el nuevo se graba con el id del que se borra
                    $this->delete($whereRow);
                    $id = $this->insert($row->toArray());
                }

                // Si se modifica algun monto tengo que recalcular los conceptos impositivos
                // en teoria nunca va a llegar hasta aca una modificacion del NetoGravado.
                if ((isset($data['DescuentoEnMonto']) && $data['DescuentoEnMonto'] != $row->DescuentoEnMonto) ||
                        (isset($data['Divisa']) && $data['Divisa'] != $row->Divisa) ||
                        (isset($data['ValorDivisa']) && $data['ValorDivisa'] != $row->ValorDivisa)) {

                    if (isset($data['Divisa']) || isset($data['ValorDivisa'])) {
                        $M_CD->recalcularPrecioUnitario($row->Id);
                    }
                } else {
                    // Si se modifica el tipo de Factura debo ver si es del mismo tipo de Factura (AyM o ByC)
                    // en dicho caso agregar o borrar los conceptos
                    if ((isset($data['TipoDeComprobante']) && $data['TipoDeComprobante'] != $row->TipoDeComprobante)) {
                        $C_ant = $row->TipoDeComprobante;
                        $C_new = $data['TipoDeComprobante'];

                        if ($this->elComprobanteDiscriminaIVA($C_ant) != $this->elComprobanteDiscriminaIVA($C_new)) {
                            // Cambio el tipo de factura
                            $this->recalcularConceptosImpostivos($row->Id);
                        }
                    }
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
     * Delete
     *
     * @param array $where 	Registros que se deben eliminar
     *
     */
    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {

                $this->salirSi_estaCerrado($row->Id);
                // Borro los comprobantes relacionados
                $this->eliminarComprobantesHijos($row->Id);
                // Borro los registros del Detalle
                $this->eliminarDetalle($row->Id);
                // Borro los conceptos Hijos
                $BorrarModificados = 1;
                $this->_eliminarConceptosHijos($row->Id, $BorrarModificados);
                // Publico y Borro
                Rad_PubSub::publish( $this->_onDeletePublish, $row);
                parent::delete('Id =' . $row->Id);
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Retorna el monto total de las retenciones asociadas al Pago
     * @param int $id id de Comprobante
     */
    public function recuperarMontoRetencion($idComprobante)
    {
        $txtSQL = "SELECT fCompPago_Monto_Retencion($idComprobante)";
        return  $this->_db->fetchOne($txtSQL);
    }
	
    /**
     * Retorna el monto total de las facturas y notas asociadas al Pago
     * @param int $id id de Comprobante
     */
    public function recuperarMontoAPagar($idComprobante)
    {
        $txtSQL = "SELECT fCompPago_Monto_aPagar($idComprobante)";
        return  $this->_db->fetchOne($txtSQL);
    }

    /**
     * Retorna el monto total de las facturas y notas asociadas al Pago
     * @param int $id id de Comprobante
     */
    public function recuperarMontoNGAPagar($idComprobante)
    {
        $txtSQL = "SELECT fCompPago_MontoNG_aPagar($idComprobante)";
        return  $this->_db->fetchOne($txtSQL);
    }

    /**
     * Retorna el monto total de las facturas y notas asociadas al Pago
     * Funcion por pedido de martin
     * @param int $id id de Comprobante
     */
    public function recuperarMontoTotal($idComprobante)
    {
        $total = $this->recuperarMontoAPagar($idComprobante);
        if ($total < 0) $total = 0;
        return $total;
    }

    /**
     * Retorna el monto total de las facturas y notas asociadas al Pago
     * @param int $id id de Comprobante
     */
    public function recuperarMontoNGTotal($idComprobante)
    {
        $total = $this->recuperarMontoNGAPagar($idComprobante);
        if ($total < 0) $total = 0;
        return $total;
    }


    /**
     * Retorna el total de los pagos cargados en el Comprobante de pago
     * @param int $id id de Comprobante
     */
    public function recuperarTotalPagos($id)
    {
        $txtSQL = "SELECT fCompPago_Monto_Pagado($id)";
        return  $this->_db->fetchOne($txtSQL);
    }

    /**
     * Retorna el total de los pagos de los conceptos impositivos cargados en el Comprobante de pago
     * @param int $id id de Comprobante
     */
    public function recuperarTotalPagosConCI($id)
    {
        $txtSQL = "SELECT fCompPago_Monto_Pagado_con_CI($id)";
        return  $this->_db->fetchOne($txtSQL);
    }

    /**
     * Retorna el total a pagar restante
     * @param int $id id de Comprobante
     */
    public function recuperarDiferenciaEnMontos($idComprobante)
    {
        $MontoAPagar = $this->recuperarMontoAPagar($idComprobante);
        $MontoPagado = $this->recuperarTotalPagos($idComprobante);

        $MontoTotal = $MontoAPagar - $MontoPagado;
        return $MontoTotal;
    }

    /**
     * Comprueba si el comprobante tiene diferencia de monto de lo que se quiere pagar con lo que se esta pagando
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    public function tieneDiferenciasEnMontos($idComprobante)
    {
        $MontoTotal = $this->recuperarDiferenciaEnMontos($idComprobante);

        if (abs($MontoTotal) < 0.01) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Comprueba si el comprobante tiene un monto superior de lo que se tiene que pagar.
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    public function tienePagoExcedido($idComprobante) {
        $MontoAPagar = 0;
        $MontoPagado = 0;
        $MontoTotal  = 0;

        $MontoPagado = $this->recuperarTotalPagos($idComprobante);
        $MontoAPagar = $this->recuperarMontoAPagar($idComprobante);

        // controlar que $MontoAPagar sea > 0
        if ($MontoAPagar <= 0) {
            $MontoTotal = $MontoPagado + abs($MontoAPagar);
        } else {
            $MontoTotal = $MontoPagado - $MontoAPagar;
        }

        if ($MontoTotal > 0.99) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si el uno de los comprobantes hijos esta asociado a algun otro comprobante padre abierto.
     *
     * @param int $idComprobante 	identificador del comprobante padre
     *
     * @return boolean
     */
    public function tieneHijosEnComprobantesAbiertos($idComprobante) {
        if (!$idComprobante) {
            throw new Rad_Db_Table_Exception("Faltan parametros necesarios.");
        }

        // Busco los hijos del comprobante
        $M_CR = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesRelacionados');
        $M_Comprobantes = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

        // Recupero todos los comprobantes hijos de la Relacion
        $R_C_H = $M_CR->fetchAll("ComprobantePadre = $idComprobante");

        // Agrego al join en enlace con la tabla comprobante para poder tomar solo los padres que esten abiertos
        $M_CR->addAutoJoin('Comprobantes', 'Comprobantes.Id = ComprobantesRelacionados.ComprobantePadre and Comprobantes.Cerrado <> 1');

        // Si tiene comprobante hijos (debe tener al menos uno) los recorro
        if (count($R_C_H)) {
            foreach ($R_C_H as $row) {
                 // Recupero los padres de este comprobante asi puedo recuperar cuanto se pago del mismo.
                $R_OtroPadreAbierto  = $M_CR->fetchAll("ComprobanteHijo = $row->ComprobanteHijo AND ComprobantePadre = $row->ComprobantePadre");
                if ($R_OtroPadreAbierto) {
                    // Tiene otro padre abierto
                    return true;
                }
            }
        }
        // No tiene otro padre abierto
        return false;
    }

    /**
     * Sale si el comprobante tiene algun comprobante hijo relacionado a otro comprobante padre abierto
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_tieneHijosEnComprobantesAbiertos($idComprobante) {
        if ($this->tieneHijosEnComprobantesAbiertos($idComprobante)) {
            // todo: indicar cuales son los comprobantes que tienen un padre abierto
            throw new Rad_Db_Table_Exception("Uno de los comprobantes que se desea pagar o cobrar se encuentra anexado a otro documento que aun no se cerro.");
        }
        return $this;
    }

    /**
     * Rearma los conceptos impositivos de una factura.
     *
     * @param int $idFactura 		identificador de la factura
     *
     * @return boolean
     */
    public function recalcularConceptosImpostivos($idComp)
    {

        if (!$this->esComprobanteAoM($idComp)) {
            // Es una factura B o C, Borro todos los conceptos por las dudas
            $BorrarModificados = 1;
            $this->_eliminarConceptosHijos($idComp, $BorrarModificados);
        } else {
            $BorrarModificados = 0;
            $this->_eliminarConceptosHijos($idComp, $BorrarModificados);
            // Calculo los conceptos que no son IVA
            $Operacion = "Pago";
            $this->recalcularConceptosParaPagos($idComp);
        }
    }

    /**
     * Rearma los conceptos impositivos de un Pago
     *
     * @param int 		$idOrdenDePago	identificador de la Orden de Pago
     *
     * @return boolean
     */
    public function recalcularConceptosParaPagos($idComprobante)
    {
        // Recupero el registro Padre
        $R_C = $this->find($idComprobante)->current();
        if (!$R_C) {
            throw new Rad_Db_Table_Exception('El Comprobante Padre no existe.');
        }

        $idPersona      = $R_C->Persona;
        $fechaEmision   = $R_C->FechaEmision;
        $LibroIVA       = $R_C->LibroIVA;

        $M_C = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');
        //$M_CI = new Facturacion_Model_DbTable_ComprobantesImpositivos(array(), false);
        $M_CR = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesRelacionados');
        $M_CI = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesImpositivos');

        // Recupero el Monto Total de los comprobantes que se estan pagando
        //die("a".$idComprobante);
        $MontoPorPagar = $this->recuperarMontoAPagar($idComprobante);

        $sql = "
				select 	CI.Descripcion,
						ifnull(PCI.Porcentaje,ifnull(CI.PorcentajeActual,0)) as Porcentaje,
						CI.Id as Concepto,
						ifnull(PCI.MontoNoImponible,ifnull(CI.MontoMinimo,0)) as MontoMinimo,
						CI.TipoDeMontoMinimo
				from	ConceptosImpositivos CI
						inner join PersonasConceptosImpositivos PCI on CI.Id = PCI.ConceptoImpositivo
				where	PCI.Persona 	= $idPersona
				and		CI.ParaPago 	= 1
				and		CI.EsIVA 		= 0
				and		CI.FechaAlta <= '" . $fechaEmision . "'
				and		IFNULL(CI.FechaBaja,'2999-12-31') >= '" . $fechaEmision . "'
				order by CI.Descripcion
				";

        // Recorro los conceptos impositivos
        $R_IMP = $this->_db->fetchAll($sql);
        if (count($R_IMP)) {
            foreach ($R_IMP as $row) {

                $idConcepto = $row["Concepto"];

                if ($M_CI->esIB($idConcepto)){
                   $MontoPorPagar = $this->recuperarMontoNGTotal($idComprobante);
                }

                // Si tiene monto minimo el impuesto veo cuanto queda disponible para calcular el impuesto
                $MMIsinUsar = $this->recuperarMMIdisponiblePagosyCobros($idConcepto, $idComprobante);

                if ($MMIsinUsar > 0.00001 && $MMIsinUsar >= $MontoPorPagar) {
                    $MontoImponible = 0;
                } else {
                    $MontoImponible = $MontoPorPagar - $MMIsinUsar;
                }

                if ($MontoImponible > 0) {

                    $Monto = $MontoImponible * $row['Porcentaje'] / 100;

                    // Cargo los datos que voy a necesitar despues
                    $Renglon = array(
                        'Persona' => $idPersona,
                        'ComprobantePadre' => $idComprobante,
                        'TipoDeComprobante' => '13',
                        'Numero' => $this->recuperarProximoNumero(0, 13),
                        'FechaEmision' => date('Y-m-d'),
                        'Divisa' => 1,
                        'ValorDivisa' => 1,
                        'LibroIVA' => $LibroIVA,
                        'ConceptoImpositivo' => $row['Concepto'],
                        'ConceptoImpositivoPorcentaje' => $row['Porcentaje'],
                        'Observaciones' => $row['Descripcion'],
                        'MontoImponible' => $MontoImponible,
                        'Monto' => $Monto
                    );

                    $R_H = $this->recuperarConceptoAsignado($idComprobante, $idConcepto);
                    // Si el concepto ya esta creado lo updateo sino lo inserto
                    if ($R_H) {
                        $idCI = $R_H->Id;
                        // Si se modifico manualmente no lo updateo
                        if (!$R_H->Modificado) {
                            $M_C->update($Renglon, "Id = $idCI");
                            $this->reasignarCIcomoFormaDePago($idComprobante, $idCI, $Monto);
                        }
                    } else {
                        $idCI = $M_C->insert($Renglon);
                        $this->reasignarCIcomoFormaDePago($idComprobante, $idCI, $Monto);
                    }
                } else {
                    $R_H = $this->recuperarConceptoAsignado($idComprobante, $idConcepto);
                    // Si el concepto ya esta creado lo borro
                    if ($R_H) {
                        $idCI = $R_H->Id;
                        // Si existe como Detalle de pago lo borro primero de ahi
                        $M_OPD = Service_TableManager::get('Facturacion_Model_DbTable_OrdenesDePagosDetalles');
                        $M_OPD->delete("ComprobanteRelacionado = $idCI and Comprobante = $idComprobante");
                        // Ahora puedo borrar el Comprobante
                        $M_C->delete("Id = $idCI");
                    }
                }
            }
        }
    }

    /**
     * Arma los conceptos Impositivos en el wizard
     *
     * @param int $idComp 		identificador de la factura
     *
     */
    public function insertarConceptosDesdeControlador($idComp)
    {   $this->salirSi_NoExiste($idComp);
        $this->salirSi_estaCerrado($idComp);
        $this->salirSi_noTieneComprobantesHijos($idComp);
        //die("a".$idComp);
        $this->recalcularConceptosImpostivos($idComp);
    }

    /**
     * sale si el comprobante tiene diferencias en monto de lo q se quiere pagar con lo que se esta pagando
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_tieneDiferenciasEnMontos($idComprobante)
    {
        if ($this->tieneDiferenciasEnMontos($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante tiene diferencias en el monto que se quiere Pagar.");
        }
        return $this;
    }

    /**
     * sale si el comprobante tiene diferencias en monto de lo q se quiere pagar con lo que se esta pagando
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_tienePagoExcedido($idComprobante)
    {
        if ($this->tienePagoExcedido($idComprobante)) {
            throw new Rad_Db_Table_Exception("El monto del comprobante es superior al monto que se quiere Pagar.");
        }
        return $this;
    }

    /**
     * Retorna el Monto imponible de un Concepto para un determinado Comprobante de Pago o Cobro.
     *
     * @param int $idConcepto 	    identificador del concepto impositivo
     * @param int $idComprobante    identificador del comprobante de Pago o cobro
     *
     * @return decimal
     */
    public function recuperarMontoImponiblePagosyCobros($idConcepto, $idComprobante)
    {

        $M_C  = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');
        $M_R  = Service_TableManager::get('Facturacion_Model_DbTable_Recibos');
        $M_CR = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesRelacionados');
        $M_CI = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesImpositivos');

        if ($M_CI->esIB($idConcepto)){
           $MontoTotal = $this->recuperarMontoNGTotal($idComprobante);
        } else {
           $MontoTotal = $this->recuperarMontoTotal($idComprobante);
        }

        // Si tiene monto minimo el impuesto veo cuanto queda disponible para calcular el impuesto
        $MMIsinUsar = $this->recuperarMMIdisponiblePagosyCobros($idConcepto, $idComprobante);

        if ($MMIsinUsar > 0.00001 && $MMIsinUsar >= $MontoTotal) {
            $MontoImponible = 0;
        } else {
            $MontoImponible = $MontoTotal - $MMIsinUsar;
        }
        
        //Rad_Log::debug(" recuperarMontoImponiblePagosyCobros -> Comprobante ID :  $idComprobante - Concepto ID : $idConcepto - Monto Imponible : $MontoImponible");

        return $MontoImponible;
    }

    /**
     * Retorna el Monto Minimo No Imponible que queda dMontoisponible para ese periodo
     * para un concepto determinado para un Cliente/Proveedor determinado.
     *
     * @param int $idConcepto 	    identificador del concepto impositivo
     * @param int $idPersona 	    identificador del Cliente/Proveedor
     * @param int $fechaEmision 	fecha de emision del comprobante padre
     *
     * @return decimal
     */
    public function recuperarMMIdisponiblePagosyCobros($idConcepto, $idComprobantePadre)
    {

        $M_C = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

        // Recupero los valores del Comprobante Padre
        $R_P = $M_C->find($idComprobantePadre)->current();
        if (!$R_P) {
            throw new Rad_Db_Table_Exception("No se encuentra el comprobante Padre.");
        }
        $Monto          = 0;
        $disponible     = 0;
        $idPersona      = $R_P->Persona;
        $MMI            = $M_C->recuperarMMIdelConcepto($idConcepto, $idPersona);

        // Si tienen MMi proceso, sino retorno 0
        if ($MMI > 0.0001) {

            $GrupoComprobante   = $M_C->recuperarGrupoComprobante($R_P);
            $PrincipioMes       = Rad_CustomFunctions::firstOfMonth($R_P->FechaEmision);
            $FinMes             = Rad_CustomFunctions::lastOfMonth($R_P->FechaEmision);

            $sql = "select  *
                    from    Comprobantes
                    where   FechaEmision >= '$PrincipioMes'
                    and     FechaEmision <= '$FinMes'
                    and     Persona      =  $idPersona
                    and     TipoDeComprobante in (select Id from TiposDeComprobantes where Grupo = $GrupoComprobante)
                    and     Id <> $idComprobantePadre
                    and     Cerrado = 1
                    and     Anulado = 0
                    ";

            // Recupero todos los demas comprobantes
            $R = $this->_db->fetchAll($sql);

            if (count($R)) {
                foreach ($R as $row) {
                    // Para cada comprobante recupero el Monto

                    // TODO: deberia ser montoPagado y no MontoAPagar ?????
                    // Asi es... como el comprobante ya se cerro es mas exacto el montoPagado.
                    // $Monto = $Monto + $this->recuperarMontoAPagar($row['Id']);
                    $Monto = $Monto + $this->recuperarTotalPagos($row['Id']);
                }
            }

            // Si queda algo lo informo como disponible
            if ($MMI > $Monto) {
                $disponible = $MMI - $Monto;
            }
        }
        return $disponible;
    }

    /**
     * elimina un comprobante que este asignado como detalle de pago
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarDetalleComprobanteRelacionado($idComprobante)
    {
        $M_CD = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesDetalles');

        $R_CD = $M_CD->fetchRow("ComprobanteRelacionado = $idComprobante");

        if (count($R_CD)) {
            $where = "Id = $R_CD->Id";
            $M_CD->delete($where);
        }
    }

    /**
     * Permite borrar los conceptos impositivos que sean hijos de un comprobante
     *
     * @param int $idComprobante 	identificador del comprobante
     * @param int $BorrarModificados 1/0 indica si se deben borrar los conceptos modificados manualmente
     *
     * @return boolean
     */
    protected function _eliminarConceptosHijos($idComprobante, $BorrarModificados)
    {
        $this->salirSi_estaCerrado($idComprobante);

        $M_CIH = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesImpositivos');
        $R_CIH = $M_CIH->fetchAll("ComprobantePadre=$idComprobante");

        // Si hay conceptos los borro
        if ($R_CIH) {
            foreach ($R_CIH as $row) {
                if ($this->_esConceptoImpositivo($row->TipoDeComprobante) &&
                        ($BorrarModificados || !$row->Modificado)) {
					// Borro los registros del Detalle
                    $this->eliminarDetalleComprobanteRelacionado($row->Id);
                    Rad_PubSub::publish('CoI_preBorrarConcepto', $row);
                    $M_CIH->delete("Id = $row->Id");
                    Rad_PubSub::publish('CoI_posBorrarConcepto', $row);
                }
            }
        }
    }

    /**
     * Permite cerrar un comprobante de Pago o Cobro y los comprobantes Hijos
     *
     * @param int $idComprobante 	identificador de la Orden de Pago o Recibo
     *
     */
    public function cerrar($idComprobante)
    {
        try {
            $this->_db->beginTransaction();

            // Controles
            $this->salirSi_NoExiste($idComprobante);
            $this->salirSi_EstaCerrado($idComprobante);
            //$this->salirSi_NoTieneDetalle($idComprobante);

            if ($this->tienePagoExcedido($idComprobante)) {
                if (!Rad_Confirm::confirm( "El monto de pago es superior a los comprobantes seleccionados. Desea continuar igualmente ?", FILE_._LINE, array('includeCancel' => false)) == 'yes') {
                    $this->_db->rollBack();
                    return false;                
                } else {
                    $this->ponerExcesoPagoEnCuentaCorriente($idComprobante);
                }
            }

            // Cierro los conceptos hijos
            $this->_cerrarConceptosHijos($idComprobante);
            //Rad_Log::debug("Paso");
            // Actualizo el valos de los montosAsociados de la ComprobantesRelacionados
            $M_CR = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesRelacionados');
            $M_CR->updatearMontoAsignado($idComprobante);

            // Cierro el comprobante de Pago o Cobro
            parent::cerrar($idComprobante);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

/**
     * Crea una NC llamada Credito en Cuenta Corriente para agregar lo que esta pagado en forma excesiva.
     *
     * Recibos          -> NCE
     * Orden de Pago    -> NDE
     *
     * @param int $idComprobante
     *
     */
    public function ponerExcesoPagoEnCuentaCorriente($idComprobante){
        $this->_db->beginTransaction();
        try {
            if($idComprobante && $this->tienePagoExcedido($idComprobante)){

                $MontoAPagar    = 0;
                $MontoPagado    = 0;
                $Monto          = 0;

                $M = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

                $MontoPagado = $this->recuperarTotalPagos($idComprobante);
                $MontoAPagar = $this->recuperarMontoAPagar($idComprobante);

                $generaNota = false;

                if ($MontoAPagar > 0) {
	                $Monto 			= $MontoPagado - $MontoAPagar;
	                $generaNota 	= true;
                } 
                /*
                else {
                	if ($MontoPagado > 0) {
                		$Monto 		= $MontoPagado;
                		$generaNota = true;	
                	} 
                }
				*/
                if ($generaNota) {

                    //recupero el comprobante que quiero compensar
                    $R_C = $M->find($idComprobante)->current();
                    if(!$R_C) throw new Rad_Db_Table_Exception('No se encontro el comprobante.');

                    $NumeroComprobantePago = $M->recuperarDescripcionComprobante($idComprobante);

                    // veo si viene de un recibo o una orden de pago
                    $TC = Service_TableManager::get('Facturacion_Model_DbTable_TiposDeComprobantes');
                    $R_TC = $TC->find($R_C->TipoDeComprobante)->current();

                    // 9 OP, 11 Recibo
                    if($R_TC->Grupo == 9) {
                        // Crear TipoDeComprobante y plan de cuenta
                            $TipoDeComprobante  = 66;
                            $CuentaCasual       = 30;
                    } else {
                        if ($R_TC->Grupo == 11) {
                            $TipoDeComprobante  = 65;
                            $CuentaCasual       = 102;
                        } else {
                            // error
                        }
                    }

                    // Armo un array de la nota
                    $RenglonComprobante = array(
                        'Persona'               => $R_C->Persona,
                        'Punto'                 => 7,
                        'Numero'                => $R_C->Numero,
                        'LibroIVA'              => $R_C->LibroIVA,
                        'Cerrado'               => 0,
                        'TipoDeComprobante'     => $TipoDeComprobante,
                        'FechaEmision'          => $R_C->FechaEmision,
                        'CondicionDePago'       => 1,
                        'ComprobantePadre'      => $idComprobante
                    );

                    //creo la nota
                    $idCCC = $M->insert($RenglonComprobante);

                    // Armo el array con el Detalle
                    $M_CD = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesDetalles');
                    $RenglonComprobanteDetalle = array(
                        'Comprobante'           => $idCCC,
                        'Cantidad'              => 1,
                        'PrecioUnitario'        => $Monto,
                        'Observaciones'         => "Credito en Cuenta Corriente de $NumeroComprobantePago",
                        'CuentaCasual'          => $CuentaCasual
                    );              

                    $idNCD = $M_CD->insert($RenglonComprobanteDetalle);

                    // Cierro la nota
                    $M->cerrar($idCCC);
                }
            } else {
                throw new Rad_Db_Table_Exception('No viene el comprobante.');
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

     /**
     * Permite anular un comprobante de Pago y desasociar los detalles
     *
     * @param int $idComprobante identificador de la Orden de Pago o Recibo
     *
     */
    public function anular($idComprobante)
    {
        try {
            $this->_db->beginTransaction();
            // Controles
            $this->salirSi_NoExiste($idComprobante);
            // Borro los registros del Detalle
            $this->eliminarDetalle($idComprobante);

            // Anulo la Factura
            parent::anular($idComprobante);

            $this->_db->commit();

            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Recuperar de un comprobante de pago el monto que se pago con cheques propios
     *
     * @param int 	Identificador del Comprobante
     *
     * @return float
     */
    public function montoChequesPropios($idComprobante)
    {
        $R = 0;
        // Recupero los registos
        $sql = "select  SUM(IFNULL(CD.PrecioUnitario,0)) as Monto
				from    ComprobantesDetalles CD
                inner join Cheques CH on CH.Id = CD.Cheque
				where 	CD.Comprobante = $idComprobante
                and     CH.TipoDeEmisorDeCheque = 1
                ";

        $R = $this->_db->fetchOne($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de pago el monto que se pago con cheques de Terceros o de Proveedores
     *
     * @param int 	Identificador del Comprobante
     *
     * @return float
     */
    public function montoChequesTerceros($idComprobante)
    {
        $R = 0;
        // Recupero los registos
        $sql = "select  SUM(ifnull(CD.PrecioUnitario,0)) as Monto
				from    ComprobantesDetalles CD
                inner join Cheques CH on CH.Id = CD.Cheque
				where 	CD.Comprobante = $idComprobante
                and     CH.TipoDeEmisorDeCheque in (1,2)
                ";

        $R = $this->_db->fetchOne($sql);
        return $R;
    }


    /**
     * SOLO se usa PARA FILTRAR LAS ORDENES DE PAGOS QUE PAGAN LA FACTURA INDICADA POR $_POST['factura']
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {

        if ($_POST['custom']) {
            $idF = $this->_db->quote($_POST['custom'], 'INTEGER');

            $idOrdenesDePago = $this->_db->fetchCol("SELECT ComprobantePadre FROM `ComprobantesRelacionados` where ComprobanteHijo = $idF");
            $idOrdenesDePago = ($idOrdenesDePago) ? implode(',', $idOrdenesDePago) : '0';
            $where = $this->_addCondition($where, "Comprobantes.Id in ($idOrdenesDePago)");
        }
        return parent:: fetchAll($where, $order, $count, $offset);
    }
}
