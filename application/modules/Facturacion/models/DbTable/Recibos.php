<?php

/**
 * Recibos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * Id                   -> Identificador Unico
 * Persona              -> Cliente al que se le realiza el Cobro
 * TipoDeComprobante    -> (cte) = 8 y 9
 * Punto                -> (cte) = 1
 * Numero               -> Numero del Recibo
 * FechaEmision         -> Fecha de generacion del Recibo
 * Divisa               -> Moneda en que esta expresada la factura
 * ValorDivisa          -> Valor de cambio de la divisa en el caso que este expresado en otra moneda
 * Cerrado              -> Indica si la factura es modificable o no.
 * Observaciones        -> Obs. internas
 *
 * @class       Facturacion_Model_DbTable_Recibos
 * @extends     Facturacion_Model_DbTable_Comprobantes
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_Recibos extends Facturacion_Model_DbTable_ComprobantesPagos
{

    protected $_defaultSource = self::DEFAULT_CLASS;
    /**
     * Evento que se publicara cuando se borre un registro
     * @var string
     */
    protected $_onDeletePublish = 'Facturacion_Recibo_Borrado';
    protected $_name = 'Comprobantes';
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '8,9'
     * 'Punto'             => 1
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(5, 6, 8, 9, 48, 58),
        //'Punto' => 1
    );

    /*
      

     */
    /**
     * Validadores
     *
     * Numero       -> valor unico
     * ValorDivisa  -> no negativo
     * FechaEmision -> no vacia
     *
     */
    protected $_validators = array(
        'Numero' => array(
            'NotEmpty',
            array(
                'Db_NoRecordExists',
                'Comprobantes',
                'Numero',
                'Punto = {Punto} AND TipoDeComprobante = {TipoDeComprobante} AND Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el Número.',
                'El numero del Recibo ya existe'
            )
        ),
        'ValorDivisa' => array(
            array('GreaterThan', 0),
            'messages' => array('El valor de la divisa no puede ser menor a 0')
        ),
        'FechaEmision' => array(
            'NotEmpty',
            'messages' => array('Falta ingresar la Fecha de Emision.')
        )
    );
    protected $_referenceMap = array(
        'Clientes' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Clientes',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10,
            'relLink' => array('module' => 'administrarClientes', 'grid' => 'PGrillaPadreAfdsfsdfc342')
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
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => '/datagateway/combolist/fetch/Abiertos',
            'refTable' => 'LibrosIVA',
            'refColumns' => 'Id'
        ),
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array(
                'Descripcion',
                'MontoSigno' => '(TiposDeComprobantes.Multiplicador * Comprobantes.Monto)'
            ),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsRecibo',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        )
    );
    
    protected $_dependentTables = array("Facturacion_Model_DbTable_RecibosDetalles");

    /**
     * Valores Default
     *
     *  'Divisa'      => La divisa Local,
     *  'ValorDivisa' => '1',
     *  'FechaEmision' => Hoy
     *
     */
    public function init()
    {
        $config = Rad_Cfg::get();

        $this->_defaultValues['Divisa'] = $config->Base->divisaLocal;
        $this->_defaultValues['ValorDivisa']  = '1';
        $this->_defaultValues['Cerrado']      = '0';
        $this->_defaultValues['Despachado']   = '0';
        $this->_defaultValues['Anulado']      = '0';
        $this->_defaultValues['FechaEmision'] = date('Y-m-d');
        $this->_defaultValues['Punto']  = $this->recuperarPuntoDefault();
        $this->_defaultValues['Numero'] = $this->generarNumeroRecibo($this->_defaultValues['Punto'], null);
        $this->_calculatedFields['MontoTotal'] = "fCompPago_Monto_aPagar(Comprobantes.Id)";
        parent:: init();
    }

    /**
     *  Insert
     *
     * @param array $data   Valores que se insertaran
     */
    public function insert($data)
    {
        // Selecciono el libro de iva correcto
        if (!$data['LibroIVA']) {
            $data['LibroIVA'] = $this->seleccionarLibroIVA($data['FechaEmision']);
        }

        // inserto
        $id = parent::insert($data);
        return $id;
    }

    public function recuperarMultiplicadorComprobante($ComprobanteHijo)
    {
        $Multiplicador = parent::recuperarMultiplicadorComprobante($ComprobanteHijo);
        $Multiplicador = $Multiplicador * (-1);
        return $Multiplicador;
    }

    private function recuperarPuntoDefault()
    {
        return 1;
    }

    public function generarNumeroRecibo($punto, $tipo)
    { // Solo si  no se usa impresora fiscal
        if (!$punto) {
            $punto = 1;
        }
        if (!$tipo) {
            $tipo = 5;
        }
        $R = $this->fetchRow("1=1 and Punto = $punto and TipoDeComprobante = $tipo", array("Punto desc", "Numero desc"));

        $ultimoNro = $R->Numero;

        if (!$ultimoNro) {
            $ultimoNro = 1;
        } else {
            $ultimoNro++;
        }

        return $ultimoNro;
    }

    /**
     * Permite cerrar un comprobante de Pago o Cobro y los comprobantes Hijos
     *
     * @param int $idComprobante    identificador de la Orden de Pago o Recibo
     *
     */
    public function old__cerrar($idComprobante)
    {
        try {
            $this->_db->beginTransaction();

            // Controles
            $this->salirSi_NoExiste($idComprobante);
            $this->salirSi_EstaCerrado($idComprobante);
            // $this->salirSi_tienePagoExcedido($idComprobante);

            if ($this->tienePagoExcedido($idComprobante)) {
                if (!Rad_Confirm::confirm( "El monto de pago es superior a los comprobantes seleccionados. Desea continuar igualmente ?", FILE_._LINE, array('includeCancel' => false)) == 'yes') {
                    $this->_db->rollBack();
                    return false;                
                } else {
                    $this->ponerExcesoPagoEnCuentaCorriente($idComprobante);
                }
            }

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
     * @param int $idComprobante
     *
     */
    public function old__ponerExcesoPagoEnCuentaCorriente($idComprobante){
        $this->_db->beginTransaction();
        try {
            if($idComprobante){

                $MontoAPagar = 0;
                $MontoPagado = 0;
                $Monto  = 0;

                $M = Service_TableManager::get('Facturacion_Model_DbTable_Comprobantes');

                $MontoPagado = $this->recuperarTotalPagos($idComprobante);
                $MontoAPagar = $this->recuperarMontoAPagar($idComprobante);

                $Monto = $MontoPagado - $MontoAPagar;

                //recupero el comprobante que quiero compensar
                $R_C = $M->find($idComprobante)->current();
                if(!$R_C){
                    throw new Rad_Db_Table_Exception('No se encontro el comprobante.');
                }

                $NumeroRecibo = $M->recuperarDescripcionComprobante($idComprobante);

                //controlo q el comprobante no este cerrado

                // Armo un array de la nota
                $RenglonComprobante = array(
                    'Persona'               => $R_C->Persona,
                    'Punto'                 => 7,
                    'Numero'                => $R_C->Numero,
                    'LibroIVA'              => $R_C->LibroIVA,
                    'Cerrado'               => 0,
                    'TipoDeComprobante'     => 65,
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
                    'Observaciones'         => "Credito en Cuenta Corriente de $NumeroRecibo",
                    'CuentaCasual'          => 102
                );              

                $idNCD = $M_CD->insert($RenglonComprobanteDetalle);

                // Cierro la nota
                $M->cerrar($idCCC);
            } else {
                throw new Rad_Db_Table_Exception('No viene el comprobante.');
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}
