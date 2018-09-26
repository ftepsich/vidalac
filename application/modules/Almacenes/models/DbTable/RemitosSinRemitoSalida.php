<?php
require_once 'Rad/Db/Table.php';

/**
 * Ordenes de Baja de Mercaderia
 */
class Almacenes_Model_DbTable_RemitosSinRemitoSalida extends Almacenes_Model_DbTable_Remitos {

    protected $_permanentValues = array(
        'TipoDeComprobante' => 15
    );
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'Clientes' => array(
            'columns'        => 'Persona',
            'refTableClass'  => 'Base_Model_DbTable_Clientes',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'TransportistasRetiro' => array(
            'columns'        => 'TransportistaRetiroDeOrigen',
            'refTableClass'  => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'TransportistasEntrego' => array(
            'columns'        => 'TransportistaEntregoEnDestino',
            'refTableClass'  => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'FletesTiposDePagos' => array(
            'columns'        => 'FleteFormaPago',
            'refTableClass'  => 'Base_Model_DbTable_FletesFormasPagos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'FletesTiposDePagos',
            'refColumns'     => 'Id'
        ),
        'DepositoPropio' => array(
            'columns'        => 'DepositoSalida',
            'refTableClass'  => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/Propio',
            'refTable'       => 'Depositos',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'TiposDeComprobantes' => array(
            'columns'        => 'TipoDeComprobante',
            'refTableClass'  => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array("Descripcion"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/EsRemito',
            'refTable'       => 'TipoDeComprobante',
            'refColumns'     => 'Id'
        )
    );

    protected $_dependentTables = array("Almacenes_Model_DbTable_RemitosArticulosDeSalidas");

    //========================================================================================================================

    public function init() {

        $this->_defaultValues = array(
            'TipoDeComprobante' => 15,
            'Estado'            => 1,
            'Punto'             => 9999,
            'FechaEmision'      => date('Y-m-d'),
            'Despachado'        => 0,
            'Divisa'            => '1',
            'ValorDivisa'       => '1',
            'Cerrado'           => '0',
            'Anulado'           => 0
        );

        parent::init();

        $this->_validators['DepositoSalida'] = array(
            'allowEmpty' => false
        );

        // hardcodeo el Id de persona al de la propia empresa (Normalmente Id = 2)
        $this->_permanentValues['Persona'] = Rad_Cfg::get()->Base->idNuestraEmpresa;

        // no necesito el estado facturado en este modelo
        unset($this->_calculatedFields['EstadoFacturado']);
    }

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
    public function generarNumero($numero) {
        $this->_db->beginTransaction();
        try {

            $sql = "select  ifnull(MAX(R.Numero),0) as Numero
                    from    Comprobantes R
                    where   R.TipoDeComprobante = 15 for update";

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