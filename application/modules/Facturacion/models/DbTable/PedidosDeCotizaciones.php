<?php

/**
 *
 * Facturacion_Model_DbTable_PedidosDeCotizaciones
 *
 * Pedidos de Cotizaciones
 *
 * @package     Aplicacion
 * @subpackage 	Facturacion
 * @class       Facturacion_Model_DbTable_PedidosDeCotizaciones
 * @extends     Facturacion_Model_DbTable_Comprobantes
 *
 */
class Facturacion_Model_DbTable_PedidosDeCotizaciones extends Facturacion_Model_DbTable_Comprobantes
{

    protected $_referenceMap = array(
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeComprobantes',
            'refColumns' => 'Id',
        ),
        'Personas' => array(
            'columns' => 'Persona',
            //'refTableClass' => 'Base_Model_DbTable_Personas',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'DepositoTercero' => array(
            'columns' => 'DepositoEntrega',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Propio',
            'refTable' => 'Direcciones',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        )
    );
    protected $_name = "Comprobantes";
    protected $_sort = array("Id desc");
    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_permanentValues = array(
        'TipoDeComprobante' => 18
    );
    protected $_defaultValues = array(
        'Punto' => '1',
        'Divisa' => '1',
        'ValorDivisa' => '1',
        'Cerrado' => '0',
        'Despachado' => '0',
        'Anulado' => '0'
        
    );
    
    /**
     * Validadores
     *
     * Numero 		-> valor unico
     * Punto		-> no vacio
     * FechaEmision     -> no vacia 
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
    

    protected $_dependentTables = array("Facturacion_Model_DbTable_PedidosDeCotizacionesArticulos");

    /**
     * Inserta un registro autonumerandolo
     *
     * @param array $data
     * @return mixed
     */
    public function insert ($data)
    {
        try {
            $this->_db->beginTransaction();

            $Punto = 1;
            $TipoDeComprobante = 18;

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
     * Delete
     *
     * @param array $where 	Registros que se deben eliminar
     *
     */
    public function delete ($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R_PC) {

                $this->salirSi_estaCerrado($R_PC->Id);
                // Borro los registros del Detalle
                $this->eliminarDetalle($R_PC->Id);

                // Publico y Borro
                parent::delete('Id =' . $R_PC->Id);
                Rad_PubSub::publish('Facturacion_PC_Borrado', $R_PC);
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}