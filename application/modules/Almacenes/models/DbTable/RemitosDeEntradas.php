<?php

require_once('Rad/Db/Table.php');

/**
 *
 * Remitos de Entrada
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 * @class 	Almacenes_Model_DbTable_RemitosDeEntradas
 * @extends	Almacenes_Model_DbTable_Remitos
 */
class Almacenes_Model_DbTable_RemitosDeEntradas extends Almacenes_Model_DbTable_Remitos
{

    protected $_name = "Comprobantes";
    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(14, 46, 17)
    );
    protected $_sort = array(
        'FechaEmision DESC'
    );
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'Proveedores' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array("RazonSocial"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'TransportistasRetiro' => array(
            'columns' => 'TransportistaRetiroDeOrigen',
            'refTableClass' => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns' => array("RazonSocial"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'TransportistasEntrego' => array(
            'columns' => 'TransportistaEntregoEnDestino',
            'refTableClass' => 'Base_Model_DbTable_Transportistas',
            'refJoinColumns' => array("RazonSocial"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'FletesTiposDePagos' => array(
            'columns' => 'FleteFormaPago',
            'refTableClass' => 'Base_Model_DbTable_FletesFormasPagos',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'FletesFormasPagos',
            'refColumns' => 'Id'
        ),
        'DepositoPropio' => array(
            'columns' => 'DepositoSalida',
            'refTableClass' => 'Base_Model_DbTable_Direcciones',
            'refJoinColumns' => array("Comentario"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Direcciones',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'DepositoTercero' => array(
            'columns' => 'DepositoEntrega',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
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

    public function update ($data, $where)
    {
        $this->_db->beginTransaction();
        try {
            $R_R = $this->fetchAll($where);
            unset($data['Despachado']); // Este valor jamas debe ser seteado directamente

            /**
             * TODO: Esto se puede optimizar para no hacer una peticion por cada registro
             * Simplemente agregando Cerrada = 1 en el where y viendo si retorna algun row
             */
            foreach ($R_R as $row) {
                // Me fijo si se esta cambiando un Remito sin remito a remito, permito el update
                if ($data['TipoDeComprobante'] != $row->TipoDeComprobante && $row->TipoDeComprobante == 17) {
                    $data = $this->_unsetNoModificables($data, $row);
                    continue;
                }
                $this->salirSi_estaCerrado($row->Id);
            }

            Rad_Db_Table::update($data, $where);
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    protected function _unsetNoModificables ($data, $row)
    {
        $data['Persona'] = $row->Persona;
        return $data;
    }

    /**
     * Validadores
     *
     * Numero 		-> valor unico
     * Cliente		-> no vacio
     *
     */
    protected function _setValidadores ()
    {
        $this->_validators = array(
            'Persona' => array(
                'allowEmpty' => false
            ),
            'DepositoEntrega' => array(
                'allowEmpty' => false
            ),
            'FechaEntrega' => array(
                'allowEmpty' => false
            ),
            'Numero' => array(
                array(
                    'Db_NoRecordExists',
                    'Comprobantes',
                    'Numero',
                    'Punto = {Punto} AND Numero = {Numero} AND TipoDeComprobante = {TipoDeComprobante} AND Persona = {Persona} AND Id <> {Id}'
                ),
                'messages' => array('El Numero %value% ya existe en otro remito')
            )
        );
    }

    public function init ()
    {
        $this->_setValidadores();
        $this->_defaultValues = array(
            'Despachado' => 0,
            'TipoDeComprobante' => 14,
            'Divisa' => '1',
            'ValorDivisa' => '1',
            'Despachado' => '0',            
            'Cerrado' => '0',
            'Anulado' => 0 
        );
        parent::init();
    }

    private function recuperarPuntoDefault ()
    {
        return 1;
    }

    /**
     * Genera el numero proximo de Remito
     *
     * @param $id INTEGER id del remito a despachar
     *
     * @return int
     */
    private function generarNumeroRemito ($punto)
    {
        $R_R = $this->fetchRow("TipoDeComprobante in(14, 46) and Punto = $punto", array("Punto desc", "Numero desc"));

        $ultimo_RN = $R_R->Numero;

        if (!$ultimo_RN) {
            $ultimo_RN = 1;
        } else {
            $ultimo_RN++;
        }
        return $ultimo_RN;
    }

    /**
     * Marca el remito como palitizado (usa el campo despachado)
     *
     * @param $idremito INTEGER id del remito
     *
     * @return int
     */
    public function marcarRemitoPaletizadoTotal($idremito)
    {  
        $M_RAE = new Almacenes_Model_DbTable_RemitosArticulosDeEntradas();
        $M_C = new Facturacion_Model_DbTable_Comprobantes();

        $R_RAE = $M_RAE->fetchAll("Comprobante = $idremito");

        $paletizado = 1;

        foreach ($R_RAE as $row) { 
            $sql = "SELECT sum(m.CantidadOriginal) 
                        FROM Mmis m 
                        WHERE m.RemitoArticulo = $row->Id";

            
            $cantidadpaletizada = $this->_db->fetchOne($sql); 

            if(!$cantidadpaletizada){
                $cantidadpaletizada = 0;
            }

            if($row->Cantidad != $cantidadpaletizada){
                $paletizado = 0;
            }
        }

        $where = " Id = $idremito ";
        $data['Despachado'] = $paletizado;
        // uso modelo comprobante para saltear la logica de los padres del modelo remito de entrada
        $M_C->update($data,$where);
    }

    /**
     * Desmarca el remito como no palitizado (usa el campo despachado)
     *
     * @param $idremito INTEGER id del remito
     *
     * @return int
     */
    public function desmarcarRemitoPaletizadoTotal($idremito)
    {
        $M_C = new Facturacion_Model_DbTable_Comprobantes();
        $where = " Id = $idremito ";
        $data['Despachado'] = 0;
        // uso modelo comprobante para saltear la logica de los padres del modelo remito de entrada
        $M_C->update($data,$where);
    }    

    public function fetchCerradosAPartirDePuestaEnMarchaAlmacenes ($where = null, $order = null, $count = null, $offset = null)
    {
        $condition = "Comprobantes.Cerrado = 1 and Comprobantes.Despachado = 0 and Comprobantes.FechaEmision >= '2013-02-06'";
        $where = $this->_addCondition($where, $condition);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchCerrados ($where = null, $order = null, $count = null, $offset = null)
    {
        $condition = "Comprobantes.Cerrado = 1";
        $where = $this->_addCondition($where, $condition);
        return self::fetchAll($where, $order, $count, $offset);
    }    

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

    public function fetchAsociadosYFaltantesDeFacturar ($where = null, $order = null, $count = null, $offset = null)
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
}

