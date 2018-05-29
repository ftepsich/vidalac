<?php

/**
 * @class               Facturacion_Model_DbTable_OrdenesDePagos
 * @extend              Facturacion_Model_DbTable_ComprobantesPagos
 *
 * Ordenes de Pagos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *  Id                   -> Identificador Unico
 *  Persona              -> Cliente al que se le realiza el Pago
 *  TipoDeComprobante    -> (cte) = 7
 *  Punto                -> (cte) = 1
 *  Numero               -> Numero de Orden de Pago
 *  FechaEmision         -> Fecha de generacion de la Orden de Pago
 *  Divisa               -> Moneda en que esta expresada la factura
 *  ValorDivisa          -> Valor de cambio de la divisa en el caso que este expresado en otra moneda
 *  Cerrado              -> Indica si la factura es modificable o no.
 *  Observaciones        -> Obs. internas
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_OrdenesDePagos extends Facturacion_Model_DbTable_ComprobantesPagos
{

    protected $_onDeletePublish = 'Facturacion_OrdenPago_Borrado';
    protected $_name = 'Comprobantes';
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '7'
     * 'Punto'             => 1
     *
     */
    /**
     * Valores Default tomados del modelo y no de la base
     */
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_permanentValues = array(
        'TipoDeComprobante' => 7,
        'Punto' => 1
    );

    /**
     * Validadores
     *
     * Numero 		-> valor unico
     * ValorDivisa	-> no negativo
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
                'Persona = {Persona} AND Punto = {Punto} AND TipoDeComprobante = {TipoDeComprobante} AND Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el Punto de Numero.',
                'El numero %value% de Orden de Pago ya existe para ese proveedor'
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
        'Proveedores' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10,
            'relLink' => array('module' => 'administrarProveedores', 'grid' => 'PGrillaPadreAmohjadfuañdnfpal98ha')
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
            'comboSource' => 'datagateway/combolist/fetch/EsOrdenDePago',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        )
    );
    // fin  protected $_referenceMap -----------------------------------------------------------------------------
    protected $_dependentTables = array("Facturacion_Model_DbTable_OrdenesDePagosDetalles");

    /**
     * Valores Default
     *
     * 	'Divisa' 	  => La divisa Local,
     * 	'ValorDivisa' => '1',
     *  'FechaEmision' => Hoy
     *
     */
    public function init()
    {
        $config = Rad_Cfg::get();
        $this->_defaultValues['Divisa'] = $config->Base->divisaLocal;
        $this->_defaultValues['ValorDivisa'] = '1';
        $this->_defaultValues['Cerrado'] = '0';
        $this->_defaultValues['Despachado'] = '0';
        $this->_defaultValues['Anulado'] = '0';       
        $this->_defaultValues['TipoDeComprobante'] = 7; 
        $this->_defaultValues['FechaEmision'] = date('Y-m-d');
        $this->_defaultValues['Punto'] = 1;
        $this->_calculatedFields['MontoTotal'] = "fCompPago_Monto_aPagar(Comprobantes.Id)";
        parent:: init();
    }    

    /**
     * Inserta un registro autonumerandolo
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $Punto = 1;
            $TipoDeComprobante = 7;
			
	    $data['TipoDeComprobante'] = $TipoDeComprobante;
            $data['Punto'] = $Punto;
            $data['Numero'] = $this->recuperarProximoNumero($Punto, $TipoDeComprobante);

            // Selecciono el libro de iva correcto
            if (!$data['LibroIVA']) {
                $data['LibroIVA'] = $this->seleccionarLibroIVA($data['FechaEmision']);
            }

            $id = parent::insert($data);
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}
