<?php

require_once 'Rad/Db/Table.php';

/**
 * Remitos de salida
 *
 * @package Aplicacion
 * @subpackage Almacenes
 * @class Almacenes_Model_DbTable_RemitosDeSalidas
 * @extends Almacenes_Model_DbTable_Remitos
 */
class Almacenes_Model_DbTable_RemitosDeSalidas extends Almacenes_Model_DbTable_Remitos
{
    protected $_name = "Comprobantes";
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_sort = array('FechaEmision Desc');

    protected $_permanentValues = array(
        'TipoDeComprobante' => array(16, 45)
    );

    protected $_referenceMap = array(
        'Cliente' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Clientes',
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
        'FletesFormasPagos' => array(
            'columns' => 'FleteFormaPago',
            'refTableClass' => 'Base_Model_DbTable_FletesFormasPagos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'FletesFormasPagos',
            'refColumns' => 'Id'
        ),
        'DepositoPropio' => array(
            'columns' => 'DepositoSalida',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Propio',
            'refTable' => 'Depositos',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'DepositoTercero' => array(
            'columns' => 'DepositoEntrega',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Direcciones',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsRemitoDeSalidaComun',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        ),
        'Punto' => array(
            'columns' => 'Punto',
            'refTableClass' => 'Base_Model_DbTable_PuntosDeRemitos',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'PuntosDeRemitos',
            'refColumns' => 'Id'
        )
    );
    protected $_dependentTables = array("Almacenes_Model_DbTable_RemitosArticulosDeSalidas");

    /**
     * Validadores
     *
     * Numero   -> valor unico
     * Cliente  -> no vacio
     * DepositoSalida -> no vacio
     *
     */
    protected function _setValidadores ()
    {
        $this->_validators = array(
            'Persona' => array(
                'allowEmpty' => false
            ),
            'DepositoSalida' => array(
                'allowEmpty' => false
            ),
            'Numero' => array(
                array(
                    'Db_NoRecordExists',
                    'Comprobantes',
                    'Numero',
                    'Punto = {Punto} AND Numero = {Numero} AND TipoDeComprobante = {TipoDeComprobante} AND Persona = {Persona} AND Id <> {Id}'
                ),
                'messages' => array('El Numero ya existe en otro remito')
            )
        );
    }

    public function init ()
    {
        $this->_setValidadores();
        $this->_defaultValues = array(
            'EstadoFacturado' => '1',
            'Divisa'          => '1',
            'ValorDivisa'     => '1',
            'Cerrado'         => '0',
            'Despachado'      => '0',
            'Anulado'         => '0'
        );
        $this->_defaultValues['Punto'] = $this->recuperarPuntoDefault();
        $this->_defaultValues['Numero'] = $this->generarNumeroRemito($this->_defaultValues['Punto']);

        parent::init();
    }

    public function updatearValorDeclarado ($idRemito, $monto)
    {
        $data['ValorDeclarado'] = $monto;
        Rad_Db_Table::update($data, 'Id = '.$idRemito);
    }

    private function recuperarPuntoDefault ()
    {
        return 1;
    }

    /**
     * Calcula el valor declarado de un remito a partir de los articulos declarados tomando el precio de la lista de precios de venta default.
     *
     * @param int 		$idRemito 		identificador del remito
     *
     * @return int
     */
    /*
	public function generarValorDeclarado($idRemito)
    {

        // Recupero el Detalle
		$M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $R_CD = $M_CD->find($idRemito)->current();

        if (count($R_CD)) {

            $M_A = new Base_Model_DbTable_Articulos(array(), false);
            foreach ($R_CD as $row) {


			}
		}
	}
	*/

    /**
     * Genera el numero proximo de Remito
     *
     * @param $id INTEGER id del remito a despachar
     *
     * @return int
     */
    private function generarNumeroRemito ($punto)
    {
        $R_R = $this->fetchRow("TipoDeComprobante = 16 and Punto = $punto", array("Punto desc", "Numero desc"));

        $ultimo_RN = $R_R->Numero;

        if (!$ultimo_RN) {
            $ultimo_RN = 1;
        } else {
            $ultimo_RN++;
        }
        return $ultimo_RN;
    }

    /**
     * Cierra el comprobante y publica el evento
     *
     * @param int $idComprobante 	identificador del comprobante a cerrar
     */
    public function cerrar ($idComprobante)
    {

        $this->imprimir($idComprobante);


        parent::cerrar($idComprobante);
    }

    public function imprimir($idComprobante)
    {

        $remito = $this->find($idComprobante)->current();

        if (!$remito)
            throw new Rad_Exception('No se encontro el remito que quiere cerrar');

        $punto = $remito->findParentRow('Base_Model_DbTable_PuntosDeRemitos');
        if ($punto->Imprime) {
            $imprimir = new Facturacion_Model_Fiscalizar_Preimpreso();
            $imprimir->fiscalizar($remito);
        }
    }

    /**
     * 	Despacha un remito
     *
     * 	@param $id INTEGER id del remito a despachar
     */
    public function despachar ($id)
    {
        $this->_db->beginTransaction();
        try {

            $id     = $this->_db->quote($id, 'INTEGER');
            $R_R    = $this->find($id)->current();

            if (!$R_R) throw new Rad_Db_Table_Exeption('No se encontro el remito');

            $this->salirSi_noEstaCerrado($id);

            if ($R_R->Despachado) throw new Rad_Db_Table_Exeption('El remito ya se encuentra despachado');

            $M_Mmis = new Almacenes_Model_DbTable_Mmis(array(), false);
            $M_Mmis->cerrarMmisRemitosSalidas($id);

            $remitoNro = $this->_db->fetchOne("Select fNumeroCompleto($id,'')");

            //$data = array();
            $data['Despachado'] = 1;
            Rad_Db_Table::update($data, "Id = $id");
            Rad_Log::user("Remitos: Despacho el Remito $remitoNro");

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchNoEnviados ($where = null, $order = null, $count = null, $offset = null)
    {
        $condition = "Comprobantes.Despachado = 0 AND Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0";
        $where = $this->_addCondition($where, $condition);
        return self::fetchAll($where, $order, $count, $offset);
    }

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================

    public function fetchFaltantesDeFacturar ($where = null, $order = null, $count = null, $offset = null)
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
        $select->having("EstadoFacturado in ('Nada','Parcialmente')");
        $select->where("Comprobantes.Cerrado = 1");
        return self::fetchAll($select);
    }

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    public function fetchAsociadosYFaltantesDeFacturarV ($where = null, $order = null, $count = null, $offset = null)
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
        $select->having("(EstadoFacturado in ('Nada','Parcialmente') OR checked = 1)");
        $select->where("Comprobantes.Cerrado = 1 and Comprobantes.Anulado = 0");
        return self::fetchAll($select);
    }

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================

}
