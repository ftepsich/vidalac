<?php

/**
 * Facturacion_Model_DbTable_ComprobantesSinIVA
 *
 * @author
 * @package     Aplicacion
 * @subpackage  Facturacion
 */
class Facturacion_Model_DbTable_ComprobantesSinIVA extends Facturacion_Model_DbTable_Comprobantes {

    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => 70, 71, 72, 73, 74, 75, 76, 77, 78
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(70, 71, 72, 73, 74, 75, 76, 77, 78)
    );
    protected $_sort = array(
        'FechaEmision DESC'
    );
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_defaultValues = array(
        'Punto' => '1',
        'Numero' => '0',
        'Divisa' => '1',
        'ValorDivisa' => '1',
        'Descuento' => '0',
        'MontoPagado' => '0',
        'Cerrado' => '0',
        'Despachado' => '0',
        'Anulado' => '0'
    );
    protected $_validators = array(
        'NumeroSinIVA' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            array(
                'Db_NoRecordExists',
                'Comprobantes',
                'NumeroSinIVA',
                'Persona = {Persona} AND NumeroSinIVA = \'{NumeroSinIVA}\' AND TipoDeComprobante = {TipoDeComprobante} AND PeriodoLiquidacionSinIVA = {PeriodoLiquidacionSinIVA} AND  Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el Numero de Comprobante.',
                'El Numero de Comprobante para el Periodo de Liquidacion Seleccionado Ya existe para ese Proveedor'
            )
        ),
        'ValorDivisa' => array(
            array('GreaterThan', 0),
            'messages' => array('El valor de la divisa no puede ser menor a 0')
        ),
        'FechaEmision' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la Fecha de Emision.')
        ),
        'FechaVencimiento' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la Fecha de Vencimiento.')
        )
    );
    protected $_calculatedFields = array(
        'EstadoPagado' => "fEstadoRelHijoPago(Comprobantes.Id) COLLATE utf8_general_ci ",
        'MontoTotal' => "fComprobante_Monto_Total(Comprobantes.Id)",
        'MontoDisponible' => "fComprobante_Monto_Disponible(Comprobantes.Id)"
    );
    protected $_referenceMap = array(
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array(
                'Descripcion',
                'MontoSigno' => '(TiposDeComprobantes.Multiplicador * Comprobantes.Monto)'
            ),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/ComprobantesSinIVA',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        ),
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'PeriodosLiquidacionSinIVA' => array(
            'columns' => 'PeriodoLiquidacionSinIVA',
            'refTableClass' => 'Contable_Model_DbTable_PeriodosLiquidacionSinIVA',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'PeriodosLiquidacionSinIVA',
            'refColumns' => 'Id'
        ),
        'PeriodosImputacionSinIVA' => array(
            'columns' => 'PeriodoImputacionSinIVA',
            'refTableClass' => 'Contable_Model_DbTable_PeriodosImputacionSinIVA',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Abiertos',
            'refTable' => 'PeriodosImputacionSinIVA',
            'refColumns' => 'Id'
        ),
        'PartidasPatentesSinIVA' => array(
            'columns' => 'PartidaPatenteSinIVA',
            'refTableClass' => 'Contable_Model_DbTable_PartidasPatentesSinIVA',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'PartidasPatentesSinIVA',
            'refColumns' => 'Id'
        ),
        'TiposDeDivisas' => array(
            'columns' => 'Divisa',
            'refTableClass' => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeDivisas',
            'refColumns' => 'Id'
        ),
        'CondicionesDePagos' => array(
            'columns' => 'CondicionDePago',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeCondicionesDePago',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeCondicionesDePago',
            'refColumns' => 'Id',
        )
    );

    /**
     * Init
     */
    public function init() {
        $this->_calculatedFields['EstadoPagado'] = "fEstadoRelHijoPago(Comprobantes.Id) COLLATE utf8_general_ci ";
        $this->_calculatedFields['MontoTotal'] = "fComprobante_Monto_Total(Comprobantes.Id)";
        $this->_defaultValues['CondicionDePago'] = 1;

        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('TiposDeComprobantes')
                    ->joinRef('TiposDeGruposDeComprobantes', array('Grupo' => 'Codigo'));
        }
    }

    /**
     *  Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();

            $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

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

                // Si se modifica el proveedor debo arancar todo de cero
                if ($data['Persona'] && $data['Persona'] == $row->Persona) {
                    parent::update($data, $whereRow);
                    $id = $row->Id;
                } else {
                    throw new Rad_Db_Table_Exception ("No se puede cambiar la Persona. Elimine y vuelva a crear el Comprobante");
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
     * @param array $where  Registros que se deben eliminar
     *
     */
    public function delete($where) {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R) {
                $this->salirSi_estaCerrado($R->Id);
                parent::delete('Comprobantes.Id =' . $R->Id);
                $tipoComprobante = $R->findParentRow("Facturacion_Model_DbTable_TiposDeComprobantes");
                // Log Usuarios
                if ($R->Numero == 0) {
                    Rad_Log::user("Borró comprobante ($tipoComprobante->Descripcion ID $R->Id)");
                } else {
                    Rad_Log::user("Borró comprobante ($tipoComprobante->Descripcion N° $R->Numero)");
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
     *  Insert
     *
     * @param array $data   Valores que se insertaran
     * addAutoJoin
     */
    public function insert($data) {
        // reviso que no exista otro abierto al momento de cargar este
        if ($data['Persona'] != 1) {
            $this->salirSi_existeOtroComprobanteSinCerrar($data['Persona'], $data['TipoDeComprobante'], null);
        }

        // Selecciono el periodo de imputacion sin iva correcto
        if (!$data['PeriodoImputacionSinIVA']) {
            $data['PeriodoImputacionSinIVA'] = $this->seleccionarLibroIVA($data['FechaEmision']);
        }

        // inserto
        return parent::insert($data);
    }


    /**
     *  Permite anular un comprobante sin iva
     *
     * @param int $idComprobante    identificador del comprobante a anular
     *
     */
    public function anular($idComprobante) {
        try {
            $this->_db->beginTransaction();
            $this->salirSi_noExiste($idComprobante)
                    ->salirSi_noEstaCerrado($idComprobante);
            parent::anular($idComprobante);
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite cerrar un comprobante sin iva
     *
     * @param int $idComprobante    identificador del comprobante a cerrar
     *
     */
    public function cerrar($idComprobante)
    {
        try {
            $this->_db->beginTransaction();
            // Controles
            $this->salirSi_NoExiste($idComprobante);
            // Si existe recupero el registro de Comprobantes
            $RowComprobante = $this->find($idComprobante)->current();
            $this->salirSi_EstaCerrado($RowComprobante);
            $this->salirSi_NoTieneDetalle($idComprobante);
            $this->salirSi_tieneDetalleConValorCero($idComprobante);
            // Cierro el comprobante
            parent::cerrar($idComprobante);
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

    }

   /**
     * Permite generar una orden de pago automaticamente cuando el comprobante se paga al contado
     *
     * @param int $idComprobante identificador del comprobante a cerrar
     *
     */
    public function generarOrdenDePagoSinIVA($idComprobante,$caja)
    {
        if ($idComprobante) {
            //activo temporalmente a los campos calculados
            $temp = $this->setFetchWithCalcFields;
            $this->setFetchWithCalcFields = true;
            //recupero el Comprobante Sin IVA al que quiero generarle una orden de pago
            $R_CSI = $this->fetchAll("Comprobantes.Id = ".$idComprobante)->current();
            //Verifico que el comprobantes sea solo de comprobantes sin iva
            $R_TC = $R_CSI->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
            if($R_TC->Grupo <> 21) {
                throw new Rad_Db_Table_Exception('No se puede generar una Orden de Pago para este Tipo de Comprobante.');
            }
            //vuelvo a dejar como estaba la activacion de los campos calculados
            $this->setFetchWithCalcFields = $temp;

            if (!count($R_CSI)) {
                throw new Rad_Db_Table_Exception('No se encontro el Comprobante Sin IVA.');
            }

            // Controlo que ya no este pagado.
            if ($this->tieneComprobantesPadres($idComprobante)) {
                throw new Rad_Db_Table_Exception('El comprobante ya se encuentra Pagado.');
            }

            $M_OPSI = new Facturacion_Model_DbTable_OrdenesDePagosSinIVA(array(), false);

            // Armo un array de la Orden de Pago
            $RenglonOrdenDePagoSinIVA = array(
                'Persona'               => $R_CSI->Persona,
                'FechaEmision'          => $R_CSI->FechaEmision
            );

            //creo la Orden de Pago
            $idOP = $M_OPSI->insert($RenglonOrdenDePagoSinIVA);
            //relaciono el comprobante sin iva con la Orden de Pago recien creada
            $RenglonOrdenDePagoSinIVAComprobante = array(
                'ComprobantePadre'      => $idOP,
                'ComprobanteHijo'       => $idComprobante
            );

            //creo la relacion de Orden de Pago y Comprobante Sin IVA
            $M_OPSIC = new Facturacion_Model_DbTable_OrdenesDePagosSinIVAComprobantes(array(), false);
            $M_OPSIC->insert($RenglonOrdenDePagoSinIVAComprobante);

            //Inserto los conceptos correspondientes a la Orden de Pago
            $M_OPSI->insertarConceptosDesdeControlador($idOP);

            //calculo el monto para el detalle de la orden de pago restando los conceptos ya cargados
            $M_CP = new Facturacion_Model_DbTable_ComprobantesPagosSinIVA(array(), false);
            $monto = $this->recuperarMontoTotal($idComprobante) - $M_CP->recuperarTotalPagos($idOP);

            // Armo un array del detalle de la Orden de Pago
            $RenglonOrdenDePagoDetalle = array(
                'Comprobante'           => $idOP,
                'PrecioUnitario'        => $monto,
                'Observaciones'         => 'Efectivo',
                'Caja'                  => $caja
            );

            //creo el detalle de la orden de pago
            $M_OPSID = new Facturacion_Model_DbTable_OrdenesDePagosSinIVADetalles(array(), false);
            $M_OPSID->insert($RenglonOrdenDePagoDetalle);

            //por ultimo cierro la orden de pago
            $M_OPSI->cerrar($idOP);
        } else {
            throw new Rad_Db_Table_Exception('No viene el Comprobante Sin IVA.');
        }
    }

    /**
     * fetch Asociados Y Faltantes De Pagar
     * Se utiliza en Ordenes de Pagos
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchAsociadosYFaltantesDePagar($where = null, $order = null, $count = null, $offset = null) {

        if ($where instanceof Zend_Db_Table_Select) {
            $select = $where;
        } else {
            $select = $this->select();
            if (!is_null($where)) {
                $this->_where($select, $where);
            }
        }

        $condicion = " Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0 ";

        $where = $this->_addCondition($where, $condicion);

        if (!is_null($order)) {
            $this->_order($select, $order);
        }

        if (!is_null($count) || !is_null($offset)) {
            $select->limit($count, $offset);
        }

        $select->having("EstadoPagado in (('Nada') COLLATE utf8_general_ci, ('Parcialmente') COLLATE utf8_general_ci) OR checked = 1");

        return self::fetchAll($select);
    }

    public function fetchComprobantesSinIVA($where = null, $order = null, $count = null, $offset = null) {
        $condicion = "Comprobantes.Cerrado = 1 and Comprobantes.Anulado = 0 and Comprobantes.TipoDeComprobante in (70, 71, 72, 73, 74, 75, 76, 77, 78)";
        $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}
