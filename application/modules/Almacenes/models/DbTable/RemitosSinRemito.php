<?php
require_once 'Rad/Db/Table.php';

class Almacenes_Model_DbTable_RemitosSinRemito extends Almacenes_Model_DbTable_Remitos {

    protected $_name = "Comprobantes";
    
    protected $_sort = "Id Desc";
	
    protected $_permanentValues = array(
        'TipoDeComprobante' => 17
    );
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
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
        'TransportistasRetiro' => array(
            'columns' => 'TransportistaRetiroDeOrigen',
            'refTableClass' => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
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
        ),
        'FletesTiposDePagos' => array(
            'columns' => 'FleteFormaPago',
            'refTableClass' => 'Base_Model_DbTable_FletesFormasPagos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'FletesTiposDePagos',
            'refColumns' => 'Id'
        ),
        'DepositoPropio' => array(
            'columns' => 'DepositoSalida',
            'refTableClass' => 'Base_Model_DbTable_Direcciones',
            'refJoinColumns' => array("Comentario"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Direcciones',
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
        ),
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsRemito',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        )
    );
    protected $_dependentTables = array("Almacenes_Model_DbTable_RemitosArticulosDeEntradas");
    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;

    //========================================================================================================================
    public function insert($data) {
        $this->_db->beginTransaction();
        try {

            $data['Numero'] = $this->generarNumero($data['Numero']);

            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    //========================================================================================================================

    public function init() {
        $this->_defaultValues = array(
            'TipoDeComprobante' => 17,
            'Estado' => 1,
            'Punto' => 9999,
            'FechaEmision' => date('Y-m-d'),
            'Despachado' => 0,
            'Divisa' => '1',
            'ValorDivisa' => '1',
            'Cerrado' => '0',
            'Anulado' => 0             
        );

        $this->_validators = array(
            'Persona' => array(
                'allowEmpty' => false
            ),
            'DepositoEntrega' => array(
                'allowEmpty' => false
            )
        );

        parent::init();
    }

    //========================================================================================================================
    public function generarNumero($numero) {
        $this->_db->beginTransaction();
        try {

            $sql = "select 	ifnull(MAX(R.Numero),0) as Numero
					from    Comprobantes R
					where   R.TipoDeComprobante = 17 for update";

            $RegR = $this->_db->query($sql);
            $FR = $RegR->fetchAll();

            $numero = $FR[0]["Numero"] + 1;

            $this->_db->commit();
            return $numero;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}