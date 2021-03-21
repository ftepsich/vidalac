<?php
require_once('Rad/Db/Table.php');

/**
 * @class 		Almacenes_Model_DbTable_Remitos
 * @extends		Facturacion_Model_DbTable_Comprobantes
 *
 *
 * Remitos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * 		Id						-> Identificador Unico
 * 		Persona 				-> Cliente o Proveedor segun el tipo de Remito
 * 		TipoDeComprobante		-> 14,15,16 y 17
 * 		Letra					-> Tipo de Factura (R o X)
 * 		Punto					-> Punto de Venta
 * 		Numero					-> Numero de la Remito
 * 		FechaEmision			-> Fecha de generacion del Remito
 * 		FechaEntrega			-> Fecha en que se entrego la mercaderia
 * 		CotCodigo				-> Codigo Cot
 * 		CotFechaValidez			-> Fecha en que pierde vigencia el codigo Cot
 * 		TransportistaRetiroDeOrigen		-> Transportista que retira la mercaderia
 * 		TransportistaEntregoEnDestino	-> Transportista que entrega la mercaderia
 * 		DepositoEntrega			-> Lugar donde se entregara la mercaderia
 * 		DepositoSalida			-> Lugar de donde sale la mercaderia
 * 		FleteFormaPago			-> Forma en que se pagara el flete
 * 		Despachado				-> Si/No Indica si se envio o no la mercaderia
 * 		ValorDeclarado			-> Valor estimado de la mercaderia transportada
 * 		Cerrado					-> Indica si el Remito es modificable o no.
 * 		Observaciones			-> Obs. internas
 * 		ObservacionesImpresas	-> Obs. que se imprimiran en el Remito
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 *
 */
class Almacenes_Model_DbTable_Remitos extends Facturacion_Model_DbTable_Comprobantes
{

    protected $_name = "Comprobantes";
    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;
    /**
     * Valores Default
     *
     *  'Despachado' 	  => 0,
     * 	'Letra'			  =>'R'
     *
     */
    protected $_defaultValues = array(
        'Despachado' => 0,
        'Letra' => 'R',
        'Divisa' => '1',
        'ValorDivisa' => '1',
        'Cerrado' => '0',
        'Despachado' => '0',
        'Anulado' => 0
    );
    /**
     * Validadores
     *
     * Numero 		-> valor unico
     * Punto		-> no vacio
     * FechaEmision -> no vacia
     *
     */
    protected $_validators = array(
        'Persona' => array(
            'allowEmpty' => false
        ),
        'Punto' => array(
            'allowEmpty' => false
        ),
        'Numero' => array(
            'allowEmpty' => false,
            array(
                'Db_NoRecordExists',
                'Comprobantes',
                'Numero',
                'Persona = {Persona} AND Punto = {Punto} AND Id <> {Id}'
            ),
            'messages' => array('El numero de Remito de ingreso ya existe para ese proveedor')
        )
    );
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'Persona' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
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
            'refTable' => 'FletesTiposDePagos',
            'refColumns' => 'Id'
        ),
        'DepositoPropio' => array(
            'columns' => 'DepositoSalida',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
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
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Direcciones',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'ComprobantesLetras' => array(
            'columns' => 'Letra',
            'refTableClass' => 'Facturacion_Model_DbTable_ComprobantesLetras',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ComprobantesLetras',
            'refColumns' => 'Id',
        )
    );
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '14,15,16,17'
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(14, 15, 16, 17)
    );

    protected $_dependentTables = array("Almacenes_Model_DbTable_RemitosArticulos");

    /**
     * Init
     */
    public function init()
    {
        $this->_calculatedFields['EstadoFacturado'] = "fEstadoRelHijo(Comprobantes.Id) COLLATE utf8_general_ci ";
        $this->_calculatedFields['NumeroCompleto']  = "fNumeroCompleto(Comprobantes.Id,'') COLLATE utf8_general_ci";
        parent::init();
    }

    /**
     * 	Update
     *
     * @param array $data 	Valores que se cambiaran
     * @param array $where 	Registros que se deben modificar
     *
     */
    public function update($data, $where)
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
                $this->salirSi_estaCerrado($row->Id);
            }

//            foreach ($R_R as $row) {
//                parent::update($data, "Id = $row->Id");
//                // Publico y updateo
//                Rad_PubSub::publish('R_Borrar', $R_R);
//            }
            parent::update($data, $where);
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
    public function delete($where)
    {
        $this->_db->beginTransaction();
        try {
            $R_R = $this->fetchAll($where);

            foreach ($R_R as $row) {
                if ($this->esComprobanteEntrada($row->Id)) {
                    // si hay mmis generados no permitimos q borre
                    $mmis = $this->recuperarMmis($row->Id);
                    if (count($mmis)) {
                        throw new Rad_Db_Table_Exception('El remito posee palets asociados y no puede ser borrado.');
                    }
                } else {
                    $this->salirSi_estaCerrado($row->Id);
                }

            }

            if (count($R_R)) $modelFCR = new Facturacion_Model_DbTable_ComprobantesRelacionados();

            foreach ($R_R as $row) {
                Rad_PubSub::publish('R_PreBorrar', $row);

                $this->eliminarDetalle($row->Id);
//                $this->_eliminarConceptosHijos($R->Id, $BorrarModificados);
                // Borro los Comprobantes relacionados
                $modelFCR->eliminarRelacionesHijos($row);

                parent::delete("Id = $row->Id");

                $tipoComprobante = $row->findParentRow("Facturacion_Model_DbTable_TiposDeComprobantes");
                Rad_Log::user("Borrado comprobante $idComprobante ($tipoComprobante->Descripcion $row->Numero)");

                // Publico que ya borre el Remito
                Rad_PubSub::publish('R_Borrar', $row);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function recuperarMmis($id)
    {
        $remito = $this->find($id)->current();

        if (!$remito) {
            throw new Rad_Db_Table_Exception('No se encontro el remito al que intenta buscarle los MMis');
        }

        $tipoDeComprobante = $remito->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        $id = $this->_db->quote($id, 'INTEGER');

        // Si es de entrada
        if ($tipoDeComprobante->Grupo == 4) {
            $sql = "Select M.* FROM Mmis M inner join ComprobantesDetalles RA on RA.Id = M.RemitoArticulo and RA.Comprobante = $id";
        } else {
            $sql = "Select M.* FROM Mmis M inner join ComprobantesDetalles RA on RA.Id = M.RemitoArticuloSalida and RA.Comprobante = $id";
        }

        return $this->_db->fetchAll($sql);
    }

    /**
     * 	Permite cerrar un Remito
     *
     * @param int $idRemito 	identificador del remito a cerrar
     *
     */
    public function cerrar($idRemito)
    {
        try {
            $this->_db->beginTransaction();

            // Controles
            $this->salirSi_NoExiste($idRemito);
            $this->salirSi_EstaCerrado($idRemito);
            $this->salirSi_NoTieneDetalle($idRemito);

            // Cierro el Remito
            parent::cerrar($idRemito);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        if ($_POST['factura']) {
            $idF = $this->_db->quote($_POST['factura'], 'INTEGER');

            $idOrdenesDePago = $this->_db->fetchCol("SELECT ComprobanteHijo FROM ComprobantesRelacionados where ComprobantePadre = $idF");

            $idOrdenesDePago = implode(',', $idOrdenesDePago);

            $where = $this->_addCondition($where, "Comprobantes.Id in ($idOrdenesDePago)");
        }
        if ($_POST['ordencompra']) {
            $idF = $this->_db->quote($_POST['ordencompra'], 'INTEGER');

            $idRemitos = $this->_db->fetchCol("SELECT ComprobantePadre FROM ComprobantesRelacionados where ComprobanteHijo = $idF");

            $idRemitos = implode(',', $idRemitos);

            $where = $this->_addCondition($where, "Comprobantes.Id in ($idRemitos)");
        }
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchDespachado($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "Comprobantes.Despachado = 1 AND Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0");

        return parent:: fetchAll($where, $order, $count, $offset);
    }
}
