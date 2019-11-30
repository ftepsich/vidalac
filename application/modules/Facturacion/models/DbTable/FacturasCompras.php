<?php

/**
 * Facturas Compras
 * englobando FC, NDR, NCR, Liq Bancarias y Liq. de Facturas
 * Detalle de la cabecera de la tabla
 * Campos:
 * Id                   -> Identificador Unico
 * Persona              -> Proveedor al que se le realiza la compra
 * TipoDeComprobante    -> Posibles : 19,20,21,22,23
 * Punto                -> Punto de Venta
 * Numero               -> Numero de la Factura
 * FechaEmision         -> Fecha de generacion de la factura
 * FechaVencimiento     ->
 * Divisa               -> Moneda en que esta expresada la factura
 * ValorDivisa          -> Valor de cambio de la divisa en el caso que este expresado en otra moneda
 * DescuentoEnMonto     -> Valor de descuento que nos han realizado en la factura
 * Cerrada              -> Indica si la factura es modificable o no.
 * Observaciones        -> Obs. internas
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_FacturasCompras extends Facturacion_Model_DbTable_Facturas
{
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(19, 20, 21, 22, 23, 33, 34, 35, 36, 41, 42, 43, 44, 53, 55, 57, 64)
    );

    protected $_sort = array(
        'FechaEmision DESC'
    );
    /**
     * Valores Default tomados del modelo y no de la base
     *
     */
    protected $_defaultSource = self::DEFAULT_CLASS;
    /**
     * Valores Default
     *
     *  'Divisa'      => '1',
     *  'ValorDivisa' => '1',
     *  'Descuento'   => '0',
     *  'MontoEstado' => '1',
     *  'MontoPagado' => '0'
     *
     */
    protected $_defaultValues = array(
        'Punto' => '1',
        'Divisa' => '1',
        'ValorDivisa' => '1',
        'Descuento' => '0',
        'MontoEstado' => '1',
        'MontoPagado' => '0',
        'Cerrado' => '0',
        'Despachado' => '0',
        'Anulado' => '0'
      
    );
    /**
     * Validadores
     *
     * Numero       -> valor unico
     * ValorDivisa  -> no negativo
     * Punto        -> no vacio
     * FechaEmision -> no vacia
     * Letra        -> no vacio, valor valido
     *
     */


    protected $_validators = array(
        'Numero' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            array(
                'Db_NoRecordExists',
                'Comprobantes',
                'Numero',
                'Persona = {Persona} AND Punto = {Punto} AND Numero = {Numero} AND TipoDeComprobante = {TipoDeComprobante} AND  Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el Numero de Comprobante.',
                'El numero de Factura de compra ya existe para ese proveedor'
            )
        ),
        'ValorDivisa' => array(
            array('GreaterThan', 0),
            'messages' => array('El valor de la divisa no puede ser menor a 0')
        ),
        'Punto' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar el Punto de Venta.')
        ),
        'FechaEmision' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la Fecha de Emision.')
        )
    );

    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array(
                'Descripcion',
                'MontoSigno' => '(TiposDeComprobantes.Multiplicador * Comprobantes.Monto)'
            ),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/FacturasComprasNotasRecibidas',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        ),
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsClienteOEsProveedor',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'LibrosIVA' => array(
            'columns' => 'LibroIVA',
            'refTableClass' => 'Contable_Model_DbTable_LibrosIVA',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Abiertos',
            'refTable' => 'LibrosIVA',
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
        'ComprobantesRelacionadosFC' => array(
            'columns' => 'ComprobanteRelacionado',
            'refTableClass' => 'Facturacion_Model_DbTable_Facturas',
            'refJoinColumns' => array("Numero"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Facturas',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
            'comboPageSize' => 10
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


    // Se hereda
    public function init ()
    {
        $this->_defaultValues['CondicionDePago'] = 1;
    
        parent::init();
        /* Debe ir despues del parent::init para que no me pise con el formato del Padre*/
        $this->_calculatedFields['NumeroCompleto'] = "fNumeroCompleto(Comprobantes.Id,'C') COLLATE utf8_general_ci";
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

                // Si se modifica el libro de IVA Cambiar el Libro a sus hijos (conceptos)
                if ($data['LibroIVA']) {
                    parent::update(array('LibroIVA' => $data['LibroIVA']),
                            ' Comprobantes.ComprobantePadre = ' . $row->Id
                    );
                }

                // Si se modifica el proveedor debo arancar todo de cero
                if ($data['Persona'] && $data['Persona'] == $row->Persona) {

                    parent::update($data, $whereRow);
                    Rad_PubSub::publish('Facturacion_FC_Updateado', $row);
                    $id = $row->Id;
                } else {
                    throw new Rad_Db_Table_Exception ("No se puede cambiar la Persona. Elimine y vuelva a crear el Comprobante");
                    /*
                    $row->setFromArray($data);

                    // Ojo... el nuevo se graba con el id del que se borra
                    $this->delete($whereRow);
                    Rad_PubSub::publish('Facturacion_FC_Borrado', $row);
                    $id = $this->insert($row->toArray());
                    */
                }

                if (isset($data['DescuentoEnMonto']) && $data['DescuentoEnMonto'] > 0.001) {
                    $this->salirSi_TieneDobleDescuento($row->Id);
                }

                // Se modifico el Descuento General, no debe afectar los articulo pero si los impuestos (Creo),
                // Si se maneja como una bonificacion no deberia afectar nada sino que descontar sobre el final como
                // el caso de los servicios de internet.
                if ((isset($data['DescuentoEnMonto']) && $data['DescuentoEnMonto'] != $row->DescuentoEnMonto)) {
                    // TODO: 17/07/2012 Ver si hay que hacer algo sino quitar
                }


                // Si se modifica algun monto tengo que recalcular los conceptos impositivos
                // en teoria nunca va a llegar hasta aca una modificacion del NetoGravado.
                if  ((isset($data['Divisa']) && $data['Divisa'] != $row->Divisa) ||
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
                            //Rad_Log::debug('XX2');
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
     * Permite cerrar una factura y los comprobantes Hijos
     *
     * @param int $idFactura    identificador de la factura a cerrar
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

            $this->salirSi_NoTieneComprobanteRelacionado($RowComprobante); //--> en el caso que lo requiera

            if ($RowComprobante->ComprobanteRelacionado) {
                // Si tiene un comprobante relacionado verifico que no este relacionado a otro
                $this->salirSi_ElComprobanteRelacionadoYaEstaRelacionado($RowComprobante);
            }

            $this->salirSi_EstaCerrado($RowComprobante);
            $this->salirSi_NoTieneDetalle($idComprobante);
            $this->salirSi_tieneDetalleConValorCero($idComprobante);

            // Cierro los conceptos hijos
            $this->_cerrarConceptosHijos($idComprobante);

            // Cierro la Factura
            parent::cerrar($idComprobante);

            // Si la factura se paga al contado genero automaticamente una orden de pago
            // esta funcion se paso al mapper de facturas compras
            /*
            if($RowComprobante->CondicionDePago == 2){
                $this->generarOrdenDePago($idComprobante);
            }
            */
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite generar una orden de pago automaticamente cuando la factura se paga al contado
     *
     * @param int $idFactura    identificador de la factura a cerrar
     *
     */
    public function generarOrdenDePago($idComprobante,$caja)
    {
        if ($idComprobante) {
            //activo temporalmente a los campos calculados
            $temp = $this->setFetchWithCalcFields;
            $this->setFetchWithCalcFields = true;
            //recupero la factura a la que quiero generarle una orden de pago
            $R_FC = $this->fetchAll("Comprobantes.Id = ".$idComprobante)->current();
            //Verifico que el comprobantes sea solo de  factura compras
            $R_TC = $R_FC->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
            if($R_TC->Grupo <> 1) {
                throw new Rad_Db_Table_Exception('No se puede generar una Orden de Pago para este tipo de Comprobante.');
            }
            //vuelvo a dejar como estaba la activacion de los campos calculados
            $this->setFetchWithCalcFields = $temp;

            if (!count($R_FC)) {
                throw new Rad_Db_Table_Exception('No se encontro la factura de compra.');
            }

            // Controlo que ya no este pagada.
            if ($this->tieneComprobantesPadres($idComprobante)) {
                throw new Rad_Db_Table_Exception('La Factura Compra ya se encuentra Pagada.');
            }



            $M_OP = new Facturacion_Model_DbTable_OrdenesDePagos(array(), false);

            // Armo un array de la orden de pago
            $RenglonOrdenDePago = array(
                'Persona'               => $R_FC->Persona,
                'FechaEmision'          => $R_FC->FechaEmision,
                'LibroIVA'              => $R_FC->LibroIVA
            );

            //creo la orden de pago
            $idOP = $M_OP->insert($RenglonOrdenDePago);

            //relaciono la Factura de compra con la Orden de Pago recien creada
            $RenglonOrdenDePagoFacturaCompra = array(
                'ComprobantePadre'      => $idOP,
                'ComprobanteHijo'       => $idComprobante,
                'MontoAsociado'         => $R_FC->MontoTotal
            );

            //creo la relacion de Orden de Pago y Factura Compra
            $M_OPF = new Facturacion_Model_DbTable_OrdenesDePagosFacturas(array(), false);
            $M_OPF->insert($RenglonOrdenDePagoFacturaCompra);

            //Inserto los conceptos correspondientes a la Orden de Pago
            $M_OP->insertarConceptosDesdeControlador($idOP);

            //calculo el monto para el detalle de la orden de pago restando los conceptos ya cargados
            $M_CP = new Facturacion_Model_DbTable_ComprobantesPagos(array(), false);
            $monto = $R_FC->MontoTotal - $M_CP->recuperarTotalPagos($idOP);

            // Armo un array del detalle de la orden de pago
            $RenglonOrdenDePagoDetalle = array(
                'Comprobante'           => $idOP,
                'PrecioUnitario'        => $monto,
                'Observaciones'         => 'Efectivo',
                'Caja'                  => $caja
            );

            //creo el detalle de la orden de pago
            $M_OPD = new Facturacion_Model_DbTable_OrdenesDePagosDetalles(array(), false);
            $M_OPD->insert($RenglonOrdenDePagoDetalle);

            //por ultimo cierro la orden de pago
            $M_OP->cerrar($idOP);
        } else {
            throw new Rad_Db_Table_Exception('No viene la factura de compra.');
        }
    }

    /**
     * elimina el detalle de una factura
     *
     * @param int $idFactura    identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarDetalle($idFactura)
    {
        $this->_db->beginTransaction();
        try {
            $M_FCA = new Facturacion_Model_DbTable_FacturasComprasArticulos(array(), false);

            $R_FC = $this->find($idFactura)->current();

            $R_FCA = $R_FC->findDependentRowset('Facturacion_Model_DbTable_FacturasComprasArticulos',
                            'FacturaCompra');

            if ($R_FCA) {
                foreach ($R_FCA as $row) {
                    $M_FCA->forceDelete($row->Id);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    public function fetchFaltantesDeRecibir($where = null, $order = null, $count = null, $offset = null)
    {
        if ($where instanceof Zend_Db_Table_Select) {
            $select = $where;
        } else {
            $select = $this->select();
            if (!is_null($where)) {
                $this->_where($select, $where);
            }
        }

        if ($order !== null) {
            $this->_order($select, $order);
        }
        if ($count !== null || $offset !== null) {
            $select->limit($count, $offset);
        }
        $select->having("EstadoRecibido in ('Nada','Parcialmente')");
        $select->where("Comprobantes.Cerrado = 1");
        return self::fetchAll($select);
    }

    public function fetchFacturasDeCompras($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "Comprobantes.Cerrado = 1 and Comprobantes.Anulado = 0 and Comprobantes.TipoDeComprobante in (19,20,21,22,23)");
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        Rad_Log::debug('remito :'.$_POST['remito']);
        if ($_POST['remito']) {
            $idRemito = $this->_db->quote($_POST['remito'], 'INTEGER');
            $where = $this->_addCondition($where, "Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0 AND Comprobantes.Id IN ( SELECT ComprobantePadre FROM ComprobantesRelacionados WHERE ComprobanteHijo = $idRemito )");
        }
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}
