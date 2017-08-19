<?php

/**
 *
 * Ordenes Compras
 *
 * Detalle de la cabecera de la tabla
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 * @class 		Facturacion_Model_DbTable_OrdenesDeCompras
 * @extends		Facturacion_Model_DbTable_Comprobantes
 *
 */
class Facturacion_Model_DbTable_OrdenesDeCompras extends Facturacion_Model_DbTable_Comprobantes {

    /**
     * Valores Default tomados del modelo y no de la base
     */
    protected $_defaultSource = self::DEFAULT_CLASS;
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '3'
     * 'Punto'             => 1
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => 3,
        'Punto' => 1
    );
    protected $_calculatedFields = array(
        'EstadoRecibido' => "fEstadoRelHijo(Comprobantes.Id) COLLATE utf8_general_ci "
    );
    /**
     * Validadores
     *
     * Numero 		-> valor unico
     * ValorDivisa	-> no negativo
     * Punto		-> no vacio
     * FechaEmision -> no vacia
     * DepositoEntrega  -> no vacio
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
                'Persona = {Persona} AND Punto = {Punto} AND TipoDeComprobante = {TipoDeComprobante} AND Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el Punto de Numero.',
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
        ),
        'DepositoEntrega' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar el deposito de entrega.')
        )
    );
    /**
     * Mapa de Referencias de la clase
     * @var array
     */
    protected $_referenceMap = array(
        'Depositos' => array(
            'columns' => 'DepositoEntrega',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Propio',
            'refTable' => 'Direcciones',
            'refColumns' => 'Id'
        ),
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        ),			
        'Proveedores' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'TiposDeDivisas' => array(
            'columns' => 'Divisa',
            'refTableClass' => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeDivisas',
            'refColumns' => 'Id',
        ),
        'TransportistasEntrego' => array(
            'columns' => 'TransportistaEntregoEnDestino',
            'refTableClass' => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        )
    );
    protected $_dependentTables = array("Facturacion_Model_DbTable_OrdenesDeComprasArticulos");

    /**
     * Valores Default
     *
     * 	'Divisa' 	  => La divisa Local,
     * 	'ValorDivisa' => '1',
     *  'FechaEmision' => Hoy
     *
     */
    public function init() {
        $config = Rad_Cfg::get();
        $this->_defaultValues = array(
            'Divisa' => $config->Base->divisaLocal,
            'ValorDivisa' => '1',
            'FechaEmision' => date('Y-m-d'),
            'Cerrado' => '0',
            'Despachado' => '0',
            'Anulado' => '0'
        );
        $this->_calculatedFields['MontoTotal'] = "fComprobante_Monto_Total(Comprobantes.Id)";        
        parent:: init();
    }

    /**
     * Inserta un registro autonumerandolo
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();

            $Punto = 1;
            $TipoDeComprobante = 3;

            $data['Numero'] = $this->recuperarProximoNumero($Punto, $TipoDeComprobante);

            $id = parent::insert($data);
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Update
     *
     * @param array $data 	Valores que se cambiaran
     * @param array $where 	Registros que se deben modificar
     *
     */
    public function update($data, $where) {
        $this->_db->beginTransaction();
        try {
            $R = $this->fetchAll($where);

            $M_OCA = new Facturacion_Model_DbTable_OrdenesDeComprasArticulos(array(), false);

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {

                $whereRow = ' Id = ' . $row->Id;

                // Controles
                $this->salirSi_estaCerrado($row->Id);

                // Veo si la divisa es la local
                if (isset($data['Divisa']) && $data['Divisa'] != $row->Divisa) {
                    $config = Rad_Cfg::get();
                    if ($data['Divisa'] == $config->Base->divisaLocal) {
                        $data['ValorDivisa'] = 1;
                    }
                }

                parent::update($data, $whereRow);

                if ((isset($data['Divisa']) && $data['Divisa'] != $row->Divisa) ||
                        (isset($data['ValorDivisa']) && $data['ValorDivisa'] != $row->ValorDivisa)) {

                    $M_OCA->recalcularPrecioUnitario($row->Id);
                }
            }
            $this->_db->commit();
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
    public function delete($where) {
        $this->_db->beginTransaction();
        try {
            $R_OC = $this->fetchAll($where);

            foreach ($R_OC as $row) {
                $this->salirSi_estaCerrado($row->Id);
            }
            parent::delete($where);

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite cerrar una Orden de Compra y los comprobantes Hijos
     *
     * @param int $idFactura 	identificador de la factura a cerrar
     *
     */
    public function cerrar($idOrdenDeCompra) {
        try {
            $this->_db->beginTransaction();

            // Controles
            $this->salirSi_NoExiste($idOrdenDeCompra);
            $this->salirSi_EstaCerrado($idOrdenDeCompra);
            $this->salirSi_NoTieneDetalle($idOrdenDeCompra);
            $this->salirSi_tieneDetalleConValorCero($idOrdenDeCompra);


            // Cierro la Orden de Compra
            parent::cerrar($idOrdenDeCompra);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    public function fetchAsociadosYFaltantesDeRecibir($where = null, $order = null, $count = null, $offset = null) {
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
        $select->having("(EstadoRecibido in ('Nada','Parcialmente') OR checked = 1)");
        $select->where("Comprobantes.Cerrado = 1 and Anulado = 0");
        return self::fetchAll($select);
    }

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
}
