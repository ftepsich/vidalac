<?php
/**
 * Facturas Ventas
 * englobando FV, NDE y NCE
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * Id                   -> Identificador Unico
 * Persona              -> Proveedor al que se le realiza la compra
 * TipoDeComprobante    -> (cte) = 3
 * Letra                -> Tipo de Factura (A,B,C,M)
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
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 * @class 		Facturacion_Model_DbTable_FacturasVentas
 * @extends		Facturacion_Model_DbTable_Facturas
 */
class Facturacion_Model_DbTable_FacturasVentas extends Facturacion_Model_DbTable_Facturas
{
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
     * 	'Divisa'      => '1',
     * 	'ValorDivisa' => '1',
     * 	'Descuento'   => '0',
     * 	'MontoEstado' => '1',
     * 	'MontoPagado' => '0'
     *
     */
    protected $_defaultValues = array(
        'Punto'         => '1',
        'Divisa'        => '1',
        'ValorDivisa'   => '1',
        'Descuento'     => '0',
        'MontoEstado'   => '1',
        'MontoPagado'   => '0',
        'Cerrado'       => '0',
        'Despachado'    => '0',
        'Modificado'    => '0',
        'Anulado'       => '0'

    );
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '3'
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(24, 25, 27, 29, 30, 31, 37, 38, 39, 59, 61, 67, 68)
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
            array(
                'Db_NoRecordExists',
                'Comprobantes',
                'Numero',
                'Punto = {Punto} AND Numero = {Numero} AND TipoDeComprobante = {TipoDeComprobante} AND Id <> {Id} And Numero <> 0 AND Anulado <> 1'
            ),
            'messages' => array(
                'Falta ingresar el Número',
                'El numero %value% de Factura de venta ya existe'
            )
        ),
        'ValorDivisa' => array(
            array('GreaterThan', 0),
            'messages' => array('El valor de la divisa no puede ser menor a 0')
        ),
        'Punto' => array(
            'NotEmpty',
            'messages' => array('Falta ingresar el Punto de Venta.')
        ),
        'FechaEmision' => array(
            'NotEmpty',
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
            'comboSource' => 'datagateway/combolist/fetch/FacturasVentasNotasEmitidas',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        ),
        'Pesonas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'Punto' => array(
            'columns' => 'Punto',
            'refTableClass' => 'Base_Model_DbTable_PuntosDeVentas',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'PuntosDeVentas',
            'refColumns' => 'Id'
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
        'ComprobantesRelacionadosFV' => array(
            'columns' => 'ComprobanteRelacionado',
            'refTableClass' => 'Facturacion_Model_DbTable_FacturasVentas',
            'refJoinColumns' => array("Numero"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/FacturasDeVentas',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'ListasDePrecios' => array(
            'columns' => 'ListaDePrecio',
            'refTableClass' => 'Base_Model_DbTable_ArticulosListasDePrecios',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ArticulosListasDePrecios',
            'refColumns' => 'Id',
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

    public function init ()
    {
        $this->_defaultValues['Punto'] = $this->recuperarPuntoDefault();
        $this->_defaultValues['CondicionDePago'] = 1;
        $this->_defaultValues['Numero'] = $this->recuperarProximoNumero($this->_defaultValues['Punto'], null);
        $this->_defaultValues['FechaEmision'] = date('Y-m-d');
        parent::init();
        /* Debe ir despues del parent::init para que no me pise con el formato del Padre*/
        $this->_calculatedFields['NumeroCompleto'] = "fNumeroCompleto(Comprobantes.Id,'C') COLLATE utf8_general_ci";

    }

    private function recuperarPuntoDefault ()
    {
        $cfg = Rad_Cfg::get();
        return $cfg->Facturacion->puntoventadefault;
    }

    public function insert($data)
    {

        /**
         * Verifico q si el Punto de venta genera numero, el Numero de la factura sea 0
         */

        // Si es electronica el numero va a ser el que me de la DGI
        $adaptador = $this->_getAdaptadorPunto($data['Punto']);
        if ($adaptador->getGeneraNumero()) {
            $data['Numero'] = '0';
        } else {
            // Si no viene el numero lo pongo en 0
            if (!$data['Numero']) {
                throw new Rad_Db_Table_Exception('No se ingreso el numero del comprobante.');
            }

            //Controla que no se cargue una factura con fecha anterior a una factura ya impresa
            if ($data['FechaEmision']) {
                $sql = "select  FV.Id as Id
                        from    Comprobantes FV
                        where   FV.FechaEmision    		>  '" . $data['FechaEmision'] . "'
                        and     FV.TipoDeComprobante    = " . $data['TipoDeComprobante'] . "
                        and     FV.Punto                = " . $data['Punto'] . "
                        and     FV.Numero               < " . $data['Numero'] . "
                        -- and     FV.Anulado		    = 0
                        and     FV.Cerrado 		        = 1";

                $R1 = $this->_db->fetchRow($sql);
                if ($R1['Id']) {
                    throw new Rad_Db_Table_Exception('No se puede generar la Factura con fecha anterior a otra ya impresa con numero posterior.');
                }
            }
        }

        return  parent::insert($data);
    }

    /**
     * Setea el numero de factura segun lo q retorna un controlador fiscal.
     * NO USAR ESTA FUNCION!!!
     * SOLO ES PARA LOS ADAPATADORES DE FISCALIZACION
     *
     * @param int $num
     * @param int $id
     */
    public function setNumeroFactura_Fiscalizador ($num, $id)
    {
        $row = $this->find($id)->current();
        $data = $row->toArray();
        $data['Numero'] = $num;
        Rad_Db_Table::update(
            $data,
            'Id = ' . $id
        );
        $row2   = $this->find($id)->current();
        $TC     = $row2->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
        $M_CC   = Service_TableManager::get('Contable_Model_DbTable_CuentasCorrientes');
        $data   = array();
        switch ($TC->Grupo) {
            case 6: // Factura de Venta
                $data['DescripcionComprobante'] = 'FV: ' . $M_CC->_getDescripcionComprobante($row2);
                break;
            case 7: // Notas de Credito Emitidas
                $data['DescripcionComprobante'] = 'NCE: ' . $M_CC->_getDescripcionComprobante($row2);
                break;
            case 12: // Notas de Debito Emitidas
                $data['DescripcionComprobante'] = 'NDE: ' . $M_CC->_getDescripcionComprobante($row2);
                break;
        }
        try {
            $M_CC->update($data, 'Comprobante = '. $id);
        } catch (Exception $e) {
            Rad_Log::debug("Comprobante : $id presenta inconvenientes al momento de fiscalizarlo.");
        }
    }

    /**
     * 	Update
     *
     * @param array $data 	Valores que se cambiaran
     * @param array $where 	Registros que se deben modificar
     *
     */
    public function update ($data, $where)
    {
        try {
            $this->_db->beginTransaction();

            $M_CD = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesDetalles');

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {

                //Controla que no se cargue una factura con fecha anterior a una factura ya impresa
                //Recupera y graba el Libro de IVA del mes y año en que se emite la factura
                if ($data['FechaEmision']) {
                    if ($data['TipoDeComprobante']) {
                        $Tipo = $data['TipoDeComprobante'];
                    } else {
                        $Tipo = $row['TipoDeComprobante'];
                    }

                    if ($data['Punto']) {
                        $Punto = $data['Punto'];
                    } else {
                        $Punto = $row['Punto'];
                    }

                    if ($data['Numero']) {
                        $Numero = $data['Numero'];
                    } else {
                        $Numero = $row['Numero'];
                    }

                    $sql = "select FV.Id as Id
    					from    Comprobantes FV
    					where   FV.FechaEmision    		>  '" . $data['FechaEmision'] . "'
    					and     FV.TipoDeComprobante    = " . $Tipo . "
    					and     FV.Punto                = " . $Punto . "
    					and     FV.Numero               < " . $Numero . "
    					and     FV.Anulado		        = 0
    					and     FV.Cerrado 		        = 1
					";
                    //Rad_Log::debug($sql);
                    $R1 = $this->_db->fetchRow($sql);

                    If ($R1['Id']) {
                        throw new Rad_Db_Table_Exception('No se puede generar la Factura con fecha anterior a otra ya impresa con numero posterior.');
                    }

                    $LibroIVA = $this->seleccionarLibroIVA($data['FechaEmision']);
                    $data['LibroIVA'] = $LibroIVA;
                }

                // verificamos si cambia el numero si el fiscalizador permite esto, de no ser asi lo cancelo
                if ($data['Numero'] && $data['Numero'] != $row->Numero) {
                    $adaptador = $this->_getAdaptadorPunto($row->Punto);

                    // Si el adaptador genera numero no permitimos el cambio
                    if ($adaptador->getGeneraNumero()) {
                        throw new Rad_Db_Table_Exception('El punto de venta genera numeracion, no puede cambiar el número');
                    }
                }

                $whereRow = ' Comprobantes.Id = ' . $row->Id;

                // Controles
                $this->salirSi_estaCerrado($row->Id);

                //controla que no permita camibiar la lista de precio si tiene articulos cargados
                if ($data['ListaDePrecio'] && $data['ListaDePrecio'] != $row->ListaDePrecio) {
                    if($this->tieneDetalle($row->Id)){
                        throw new Rad_Db_Table_Exception("Para cambiar la lista de precio debera eliminar los articulos cargados en el comprobante.");
                    }

                }

                // Veo si la divisa es la local
                if (isset($data['Divisa']) && $data['Divisa'] != $row->Divisa) {
                    $config = Rad_Cfg::get();
                    if ($data['Divisa'] == $config->Base->DivisaLocal) {
                        $data['ValorDivisa'] = 1;
                    }
                }


                // Si se modifica el proveedor debo arancar todo de cero
                if (!$data['Persona'] || ($data['Persona'] && $data['Persona'] == $row->Persona)) {

                    Rad_PubSub::publish('FV_preUpdate', $R_FC);
                    parent::update($data, $whereRow);
                    Rad_PubSub::publish('FV_posUpdate', $R_FC);

                    $id = $row->Id;
                } else {

                    throw new Rad_Db_Table_Exception("No se puede cambiar la Persona. Elimine y vuelva a crear el Comprobante");
       
                }

                if (isset($data['DescuentoEnMonto']) && $data['DescuentoEnMonto'] > 0.001) {
                    $this->salirSi_TieneDobleDescuento($row->Id);
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
                    // Si se modifica la Letra debo ver si es del mismo tipo de Factura (AyM o ByC)
                    // en dicho caso agregar o borrar los conceptos
                    if ((isset($data['Letra']) && $data['Letra'] != $row->Letra)) {
                        $L_ant = $row->Letra;
                        $L_new = $data['Letra'];
                        $FacAoM = array(1, 2);

                        if (( in_array($L_Ant, $FacAoM) && !in_array($L_New, $FacAoM)) ||
                                ( in_array($L_New, $FacAoM) && !in_array($L_Ant, $FacAoM))) {
                            // Cambio el tipo de factura

                            $this->recalcularConceptosImpostivos($row->Id);
                        }
                    }
                }
            }

            // Si se modifica el libro de IVA Cambiar el Libro a sus hijos (conceptos)
            // Creo que no va (Martin)
            if ($data['LibroIVA']) {
                $M_C = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');
                $M_C->update(
                        array(
                            'LibroIVA' => $data['LibroIVA']
                            ),
                        ' Comprobantes.ComprobantePadre = ' . $row->Id);
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
     * @param int $idFactura 	identificador de la factura a cerrar
     *
     */
    public function cerrar ($idFactura)
    {

        // Controles
        $this->salirSi_NoExiste($idFactura)
                ->salirSi_EstaCerrado($idFactura)
                ->salirSi_NoTieneDetalle($idFactura)
                ->salirSi_tieneDetalleConValorCero($idFactura);

        $factura = $this->find($idFactura)->current();


        try {
            // Inicio despues la transaccion ya q el fiscalizador debe poder modificar datos sin q despues se realice un rollback
            $this->_db->beginTransaction();
            // Cierro los conceptos hijos
            $this->_cerrarConceptosHijos($idFactura);
            // Cierro la Factura
            parent::cerrar($idFactura);
            $sql = "SELECT C.Id FROM ComprobantesRelacionados CD
                        LEFT JOIN Comprobantes C ON CD.ComprobanteHijo = C.Id
                        LEFT JOIN TiposDeComprobantes TDC ON C.TipoDeComprobante = TDC.Id
                    WHERE CD.ComprobantePadre = $idFactura AND TDC.Grupo = 10";
            $R = $this->_db->fetchAll($sql);
            // Fiscalizamos la factura
            $fiscalizador = new Facturacion_Model_Fiscalizar();
            $fiscalizador->fiscalizar($factura);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    protected function _getAdaptadorPunto ($punto)
    {
        // Obtengo la clase del adaptador
        $sql = "SELECT  ad.Class
                FROM    AdaptadoresFiscalizaciones as ad
                        JOIN PuntosDeVentas AS p ON p.Adaptador = ad.Id
                and     p.Id = $punto
                LIMIT 1";

        $class = $this->_db->fetchOne($sql);

        if ($class == '') throw new Rad_Exception('Error al obtener el adaptador del punto '.$punto);

        return new $class();
    }

    public function getAdaptadorPunto ($punto)
    {
        return $this->_getAdaptadorPunto($punto);
    }

    /**
     * Compensa una FV en su totalidad por una NC
     *
     * @param int $idComprobante
     *
     */
    public function compensarFacturasConNotas($idComprobante){
        $this->_db->beginTransaction();
        try {
            if($idComprobante){
                //recupero el comprobante que quiero compensar
                $R_C = $this->find($idComprobante)->current();
                if(!$R_C){
                    throw new Rad_Db_Table_Exception('No se encontro el comprobante.');
                }

                //controlo q el comprobante no este cerrado
                $this->salirSi_noEstaCerrado($idComprobante);

                //controlo q el comprobante se pueda compensar por una nota
                $this->salirSi_noPermiteCompensarPorNota($R_C->TipoDeComprobante);

                //controlo q el comprobante no este compensado
                $this->salirSi_EstaCompensado($idComprobante);

                //recupero el tipo de comprobante por el cual se compensa la factura
                $tipoDeNota = $this->compensaPorNota($R_C->TipoDeComprobante);

                // Armo un array de la nota
                $RenglonComprobante = array(
                    'Persona'               => $R_C->Persona,
                    'Punto'                 => $R_C->Punto,
                    'Cerrado'               => 0,
                    'TipoDeComprobante'     => $tipoDeNota,
                    'FechaEmision'          => date('Y-m-d'),
                    'FechaVencimiento'      => $R_C->FechaVencimiento,
                    'LibroIVA'              => $R_C->LibroIVA,
                    'Divisa'                => $R_C->Divisa,
                    'ValorDivisa'           => $R_C->ValorDivisa,
                    'DescuentoEnMonto'      => $R_C->DescuentoEnMonto,
                    'DescuentoEnPorcentaje' => $R_C->DescuentoEnPorcentaje,
                    'CondicionDePago'       => $R_C->CondicionDePago
                );

                //creo la nota
                $idNC = $this->insert($RenglonComprobante);

                if($R_C->TipoDeComprobante == 27){
                    $M_CE = new Facturacion_Model_DbTable_ComprobantesDeExportaciones();
                    $R_CE = $M_CE->fetchRow("ComprobantesDeExportaciones.Comprobante = ".$idComprobante);

                    // Armo un array de la nota para exportacion
                    $RenglonComprobanteExportacion = $R_CE->toArray();
                    $RenglonComprobanteExportacion['Comprobante'] = $idNC;
                    unset($RenglonComprobanteExportacion['Id']);

                    //creo la nota
                    $idNCE = $M_CE->insert($RenglonComprobanteExportacion);
                }
                //relaciono la Nota recien creada con la factura
                $R_NC = $this->fetchRow("Id = $idNC");
                if(!$R_NC){
                    throw new Rad_Db_Table_Exception('No se encontro la nota creada.');
                }
                $R_NC->ComprobanteRelacionado = $idComprobante;
                $R_NC->save();

                //Recupero el detalle de la factura para duplicarlo y relacionarlo con la nota
                $M_CD = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesDetalles');
                $R_CD = $M_CD->fetchAll("Comprobante = $idComprobante");

                if($R_CD){
                    foreach ($R_CD as $rowDetalle) {
                        // Armo un array del detalle de la nota
                        $RenglonComprobanteDetalle = array(
                            'Comprobante'                   => $idNC,
                            'Articulo'                      => $rowDetalle->Articulo,
                            'CuentaCasual'                  => $rowDetalle->CuentaCasual,
                            'Cantidad'                      => $rowDetalle->Cantidad,
                            'PrecioUnitario'                => $rowDetalle->PrecioUnitario,
                            'PrecioUnitarioMExtranjera'     => $rowDetalle->PrecioUnitarioMExtranjera,
                            'DescuentoEnMonto'              => $rowDetalle->DescuentoEnMonto,
                            'DescuentoEnPorcentaje'         => $rowDetalle->DescuentoEnPorcentaje,
                            'ConceptoImpositivo'            => $rowDetalle->ConceptoImpositivo
                        );

                        //creo el detalle de la nota
                        $idNCD = $M_CD->insert($RenglonComprobanteDetalle);
                    }
                }

                //Recupero los conceptos de la factura para duplicarlo y relacionarlo con la nota
                $M_CI = Service_TableManager::get('Facturacion_Model_DbTable_ComprobantesImpositivos');
                $R_CI = $M_CI->fetchAll("ComprobantePadre = $idComprobante");

                if($R_CI){
                    foreach ($R_CI as $rowImpositivo) {
                        // Armo un array del detalle de la nota
                        $RenglonComprobanteImpositivo = array(
                            'Persona'                       => $rowImpositivo->Persona,
                            'Punto'                         => $rowImpositivo->Punto,
                            'Numero'                        => $rowImpositivo->Numero,
                            'Cerrado'                       => 0,
                            'Monto'                         => $rowImpositivo->Monto,
                            'TipoDeComprobante'             => $rowImpositivo->TipoDeComprobante,
                            'FechaEmision'                  => $rowImpositivo->FechaEmision,
                            'LibroIVA'                      => $rowImpositivo->LibroIVA,
                            'Divisa'                        => $rowImpositivo->Divisa,
                            'ValorDivisa'                   => $rowImpositivo->ValorDivisa,
                            'ComprobantePadre'              => $idNC,
                            'ConceptoImpositivo'            => $rowImpositivo->ConceptoImpositivo,
                            'ConceptoImpositivoPorcentaje'  => $rowImpositivo->ConceptoImpositivoPorcentaje
                        );

                        //creo los conceptos impositivos de la nota
                        $idNCI = $M_CI->insert($RenglonComprobanteImpositivo);
                    }
                }
                $this->cerrar($idNC);
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
     * Permite generar una recibo automaticamente cuando la factura se paga al contado
     *
     * @param int $idFactura    identificador de la factura a cerrar
     *
     */
    public function generarRecibo($idComprobante,$caja)
    {
        if ($idComprobante) {
            //activo temporalmente a los campos calculados
            $temp = $this->setFetchWithCalcFields;
            $this->setFetchWithCalcFields = true;

            //recupero la factura a la que quiero generarle el recibo
            $R_FC = $this->fetchAll("Comprobantes.Id = ".$idComprobante)->current();

            if ($R_FC->CondicionDePago != 2) {
                throw new Rad_Db_Table_Exception('Este comprobante no es al contado, no se le puede generar automaticamente un recibo.');
            }

            //Verifico que el comprobantes sea solo de  factura ventas
            $R_TC = $R_FC->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

            if( $R_TC->Grupo <> 6 ) {
                throw new Rad_Db_Table_Exception('No se puede generar una Orden de Pago para este tipo de Comprobante.');
            }
            //vuelvo a dejar como estaba la activacion de los campos calculados
            $this->setFetchWithCalcFields = $temp;

            if (!count($R_FC)) {
                throw new Rad_Db_Table_Exception('No se encontro la factura.');
            }

            // Controlo que ya no este pagada.
            $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);
            $R_CR = $M_CR->fetchAll("ComprobantesRelacionados.ComprobanteHijo = ".$idComprobante)->current();

            if ($this->tieneComprobantesPadres($idComprobante)) {
                throw new Rad_Db_Table_Exception('La Factura ya se encuentra Pagada.');
            }


            $M_Recibo = new Facturacion_Model_DbTable_Recibo;

            // Armo un array de la orden de pago
            $RenglonRecibo = array(
                'Persona'               => $R_FC->Persona,
                'FechaEmision'          => $R_FC->FechaEmision,
                'LibroIVA'              => $R_FC->LibroIVA
            );

            //creo la orden de pago
            $idRecibo = $M_Recibo->insert($RenglonRecibo);

            //relaciono la Factura de compra con la Orden de Pago recien creada
            $RenglonReciboFacturaVenta = array(
                'ComprobantePadre'      => $idRecibo,
                'ComprobanteHijo'       => $idComprobante,
                'MontoAsociado'         => $R_FC->MontoTotal
            );

            //creo la relacion de Recibo y Factura venta
            $M_RFacturas = new Facturacion_Model_DbTable_RecibosFacturas;
            $M_RFacturas->insert($RenglonReciboFacturaVenta);

            //Inserto los conceptos correspondientes a la Orden de Pago
            $M_Recibo->insertarConceptosDesdeControlador($idRecibo);

            //calculo el monto para el detalle de la orden de pago restando los conceptos ya cargados
            $M_CP = new Facturacion_Model_DbTable_ComprobantesPagos;
            $monto = $R_FC->MontoTotal - $M_CP->recuperarTotalPagos($idRecibo);

            // Armo un array del detalle de la orden de pago
            $RenglonReciboDetalle = array(
                'Comprobante'           => $idRecibo,
                'PrecioUnitario'        => $monto,
                'Observaciones'         => 'Efectivo',
                'Caja'                  => $caja
            );

            //creo el detalle de la orden de pago
            $M_OPD = new Facturacion_Model_DbTable_RecibosDetalles;
            $M_OPD->insert($RenglonReciboDetalle);

            //por ultimo cierro la orden de pago
            $M_Recibo->cerrar($idRecibo);
        } else {
            throw new Rad_Db_Table_Exception('No viene la factura de compra.');
        }
    }

    /**
     * Sugiere un numero de factura correlativa
     *
     * @param int $tipoDeComprobante
     * @param int $punto
     */
    public function recuperarProximoNumero ($punto, $tipo)
    {
        if (!$punto) {
            $punto = 1;
        }

        if (!$tipo) {
            $tipo = 24;
        }

        // Obtengo la clase del adaptador
        $sql = "SELECT ad.Class FROM AdaptadoresFiscalizaciones as ad
                JOIN PuntosDeVentas AS p ON p.Adaptador = ad.Id and p.Id = $punto
                LIMIT 1";

        $class = $this->_db->fetchOne($sql);

        $adaptador = $this->_getAdaptadorPunto($punto);

        // Si el adaptador genera numero entonces retornamos 0
        if ($adaptador->getGeneraNumero()) {
            return 0;
        }

        $M_TC = Service_TableManager::get('Facturacion_Model_DbTable_TiposDeComprobantes');
        $R_TC = $M_TC->find($tipo)->current();

        $sql = "SELECT  C.Numero
                FROM    Comprobantes AS C
                    INNER JOIN TiposDeComprobantes          AS TC   ON C.TipoDeComprobante = TC.Id
                    INNER JOIN TiposDeGruposDeComprobantes  AS TGC  ON TC.Grupo = TGC.Id
                WHERE   C.Punto = $punto
                and     TC.TipoDeLetra = $R_TC->TipoDeLetra
				and     TC.Grupo in (6,7,12)
                ORDER BY C.Punto DESC, C.Numero DESC
                LIMIT 1";

        $ultimoNumero = $this->_db->fetchOne($sql);

        //return '123';
        return ( $ultimoNumero ) ? $ultimoNumero + 1 : 1;
    }

    /**
     * Elimina el detalle de una factura
     *
     * @param int $idFactura 	identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarDetalle ($idFactura)
    {
        $this->_db->beginTransaction();
        try {
            $M_FVA = new Facturacion_Model_DbTable_FacturasVentasArticulos(array(), false);

            $R_FV = $this->find($idFactura)->current();

            $R_FVA = $R_FV->findDependentRowset(
                            'Facturacion_Model_DbTable_FacturasVentasArticulos',
                            'FacturaVenta'
            );

            if ($R_FVA) {
                foreach ($R_FVA as $row) {
                    $where = "Id=" . $row['Id'];
                    $M_FVA->delete($where);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    public function fetchFaltantesDeEnviar($where = null, $order = null, $count = null, $offset = null)
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

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    public function fetchFacturasDeVentas ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Comprobantes.Cerrado = 1 and Comprobantes.Anulado = 0 and Comprobantes.TipoDeComprobante in (24,25,26,27,28)";
        $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }
}
