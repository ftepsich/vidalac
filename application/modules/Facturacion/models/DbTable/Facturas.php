<?php

/**
 * Facturacion_Model_DbTable_Facturas of Facturas
 *
 * @author Martin
 * @package     Aplicacion
 * @subpackage  Facturacion
 */
class Facturacion_Model_DbTable_Facturas extends Facturacion_Model_DbTable_Comprobantes
{

    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => '19, 20, 21, 22, 23'
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(19, 20, 21, 22, 23, 33, 34, 35, 36, 41, 42, 43, 44, 24, 25, 26, 27, 28, 29, 30, 31, 32, 37, 38, 39, 40, 47, 49, 50, 51, 52, 53, 54, 55, 56, 57,65,66)
    );

    protected $_calculatedFields = array(
        'EstadoPagado' => "fEstadoRelHijoPago(Comprobantes.Id) COLLATE utf8_general_ci ",
        'EstadoRecibido' => "fEstadoRelPadre(Comprobantes.Id) COLLATE utf8_general_ci",
        'MontoTotal' => "fComprobante_Monto_Total(Comprobantes.Id)",
        'MontoDisponible' => "fComprobante_Monto_Disponible(Comprobantes.Id)"
    );

    protected $_referenceMap = array(
        'TiposDeComprobantes' => array(
            'columns' => 'TipoDeComprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array(
                'Descripcion',
                'MontoSigno' => '(TiposDeComprobantes.Multiplicador * Comprobantes.Monto)'
            ),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/FacturasComprasNotasRecibidas',
            'refTable' => 'TipoDeComprobante',
            'refColumns' => 'Id'
        ),
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array("RazonSocial"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
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
        'ComprobantesRelacionadosFC' => array(
            'columns' => 'ComprobanteRelacionado',
            'refTableClass' => 'Facturacion_Model_DbTable_Facturas',
            'refJoinColumns' => array("Numero"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        )
    );

    /**
     * Init
     */
    public function init()
    {
        $this->_calculatedFields['EstadoPagado']    = "fEstadoRelHijoPago(Comprobantes.Id) COLLATE utf8_general_ci ";
        $this->_calculatedFields['EstadoRecibido']  = "fEstadoRelPadre(Comprobantes.Id) COLLATE utf8_general_ci";
        $this->_calculatedFields['MontoTotal']      = "fComprobante_Monto_Total(Comprobantes.Id)";

        // NroCompleto se pisa en los hijos FC y FV (Ingreso de Comprobantes y Emision de comprobantes)
        $this->_calculatedFields['NumeroCompleto']  = "fNumeroCompleto(Comprobantes.Id,'CG') COLLATE utf8_general_ci";

        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('TiposDeComprobantes')
              ->joinRef('TiposDeGruposDeComprobantes',array('Grupo' => 'Codigo'));
        }
    }

    /**
     * Delete
     *
     * @param array $where  Registros que se deben eliminar
     *
     */
    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R) {
                if ($this->esComprobanteEntrada($R->Id)) {
                    $comprobante = Facturacion_Model_DbTable_OrdenesDePagosFacturas::retornarComprobantePago($R->Id);

                    if (!empty($comprobante)) {
                        throw new Rad_Db_Table_Exception("La factura que intenta borrar se encuentra relacionada con la orden de pago Número: {$comprobante['Numero']}.");
                    }

                    // Verificar si tiene LibroIVAcerrado
                    $M_LI = new Contable_Model_DbTable_LibrosIVA(array(), false);
                    if ($M_LI->estaCerrado($R->LibroIVA)) {
                        throw new Rad_Db_Table_Exception("El comprobante se encuentra registrado en un libro de iva cerrado y no puede modificarse. Debe realizar los comprobantes cancelatorios que indica la ley.");
                    }
                } else {
                    $this->salirSi_estaCerrado($R->Id);
                }

                // Publico y Borro
                Rad_PubSub::publish('Facturacion_F_preBorrar', $R);

                // Borro los registros del Detalle
                $this->eliminarDetalle($R->Id);
                // Borro los conceptos Hijos
                $BorrarModificados = 1;

                $this->_eliminarConceptosHijos($R->Id, $BorrarModificados);
                // Borro los Comprobantes Hijos
                $this->eliminarComprobantesHijos($R->Id);

                parent::delete('Comprobantes.Id =' . $R->Id);
                $tipoComprobante = $R->findParentRow("Facturacion_Model_DbTable_TiposDeComprobantes");
                // Log Usuarios
                if ( $R->Numero == 0 ) {
                    Rad_Log::user("Borró comprobante ($tipoComprobante->Descripcion ID $R->Id)");
                } else {
                    Rad_Log::user("Borró comprobante ($tipoComprobante->Descripcion N° $R->Numero)");
                }
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *  Insert
     *
     * @param array $data   Valores que se insertaran
     * addAutoJoin
     */
    public function insert($data)
    {
        // reviso que no exista otro abierto al momento de cargar este
        // solo si no es consumidor final generico (para permitir varios Puntos de ventas simultaneos facturar)
        if ($data['Persona'] != 1) {
            $this->salirSi_existeOtroComprobanteSinCerrar($data['Persona'], $data['TipoDeComprobante'], null);
        }

        // Selecciono el libro de iva correcto
        if (!$data['LibroIVA']) {
            $data['LibroIVA'] = $this->seleccionarLibroIVA($data['FechaEmision']);
        }

        // inserto
        return parent::insert($data);
    }

    /**
     *  Permite anular una factura y los comprobantes Hijos si corresponde
     *
     * @param int $idFactura    identificador de la factura a cerrar
     *
     */
    public function anular($idFactura)
    {
        try {
            $this->_db->beginTransaction();

            // Controles
            $this->salirSi_noExiste($idFactura)
                 ->salirSi_noEstaCerrado($idFactura);

            // TODO: Falta ver que se hace si el Libro de IVA esta cerrado con los comprobantes hijos
            parent::anular($idFactura);
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

//    //TODO: Este codigo se puede mejorar y hay mucho repetido. Arreglado a los apurones para arreglar falla en comprobantes B
//    /**
//     * Retorna el total del iva de la factura
//     *
//     * @param int         $idFactura      identificador de la factura
//     *
//     * @return float
//     */
//    public function getTotalesIva($idFactura)
//    {
//        // Recupero el registro Padre
//        $R_FV = $this->find($idFactura)->current();
//
//        // Recupero el NG
//        $NetoGravado = $this->recuperarNetoGravado($idFactura);
//
//        $sql = "SELECT
//                    sum(CD.Cantidad*CD.PrecioUnitario) as NetoGravadoArticulo,
//                    CI.afip as codAfip,
//                    CD.DescuentoEnMonto,
//                    CD.DescuentoEnPorcentaje,
//                    CD.ConceptoImpositivo as ConceptoIVA,
//                    CI.PorcentajeActual as Porcentaje,
//                    ifnull(CD.DescuentoEnMonto,0) as DescuentoGeneral
//            FROM  ComprobantesDetalles CD
//                            INNER JOIN ConceptosImpositivos CI ON CD.ConceptoImpositivo = CI.Id   and CI.esIVA = 1
//                            INNER JOIN Comprobantes C ON CD.Comprobante = C.Id
//            WHERE CD.Comprobante = $idFactura
//            GROUP BY CD.ConceptoImpositivo";
//
//        $R_IVA = $this->_db->fetchAll($sql);
//
//        $ivas = array();
//        if (count($R_IVA)) {
//
//            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
//            foreach ($R_IVA as $row) {
//                $tmp = array();
//                // Cargo los datos que voy a necesitar despues
//
//                $NGArticulo = 0;
//
//                if ($row['DescuentoGeneral'] > 0) {
//                    $NGArticulo = $row['NetoGravadoArticulo'] - ($row['NetoGravadoArticulo'] * $row['DescuentoGeneral'] / $NetoGravado);
//                } else {
//                    $NGArticulo = $row['NetoGravadoArticulo'];
//                    // Le hago el descuento si el mismo esta cargado en el articulo
//                    if ($row['DescuentoEnMonto'] > 0) {
//                        $NGArticulo = $NGArticulo - $row['DescuentoEnMonto'];
//                    }
//                    if ($row['DescuentoEnPorcentaje'] > 0) {
//                        $NGArticulo = $NGArticulo - ($NGArticulo * $row['DescuentoEnPorcentaje'] / 100);
//                    }
//                }
//
//                $totalIva += $NGArticulo * $row['Porcentaje'] / 100;
//                $tmp['MontoImponible'] = $NGArticulo;
//                $tmp['Monto'] = $NGArticulo * $row['Porcentaje'] / 100;
//                $tmp['codAfip'] = $row['codAfip'];
//                $ivas[] = $tmp;
//            }
//        }
//        return $ivas;
//    }

    /**
     * Rearma los conceptos de IVA de una Factura
     *
     * @param int       $idFactura      identificador de la factura
     *
     * @return boolean
     */
    public function recalcularIVA($idFactura)
    {

        // Recupero el registro Padre
        $R_FV = $this->find($idFactura)->current();

        // Recupero el NG
        $NetoGravado = $this->recuperarNetoGravado($idFactura);

        $sql = "SELECT
                fComprobante_NetoGravado_xCI($idFactura,CD.ConceptoImpositivo) as NGporCI,
                sum(CD.Cantidad*CD.PrecioUnitario) as NetoGravadoArticulo,
                CD.DescuentoEnMonto,
                CD.DescuentoEnPorcentaje,
                CD.ConceptoImpositivo as ConceptoIVA,
                CI.PorcentajeActual as Porcentaje,
                ifnull(CD.DescuentoEnMonto,0) as DescuentoGeneral
            FROM ComprobantesDetalles CD
                 INNER JOIN ConceptosImpositivos CI ON CD.ConceptoImpositivo = CI.Id and CI.esIVA = 1
                 INNER JOIN Comprobantes C ON CD.Comprobante = C.Id
            WHERE CD.Comprobante = $idFactura
            GROUP BY CD.ConceptoImpositivo";

        $R_IVA = $this->_db->fetchAll($sql);

        if (count($R_IVA)) {

            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
            foreach ($R_IVA as $row) {

                // Cargo los datos que voy a necesitar despues

                $NGArticulo = 0;

                if ($row['DescuentoGeneral'] > 0) {
                    $NGArticulo = $row['NetoGravadoArticulo'] - ($row['NetoGravadoArticulo'] * $row['DescuentoGeneral'] / $NetoGravado);
                } else {
                    $NGArticulo = $row['NetoGravadoArticulo'];
                    // Le hago el descuento si el mismo esta cargado en el articulo
                    if ($row['DescuentoEnMonto'] > 0) {
                        $NGArticulo = $NGArticulo - $row['DescuentoEnMonto'];
                    }
                    if ($row['DescuentoEnPorcentaje'] > 0) {
                        $NGArticulo = $NGArticulo - ($NGArticulo * $row['DescuentoEnPorcentaje'] / 100);
                    }
                }

                $monto = $NGArticulo * $row['Porcentaje'] / 100;
                // conceptos con monto 0 (cero) no se generan...

                // Comentado pq el IVA 0 va (Martin 01/03/2017)
                // if($monto == 0){
                //     continue;
                // }

                $Renglon = array(
                    'Persona' => $R_FV->Persona,
                    'ComprobantePadre' => $R_FV->Id,
                    'TipoDeComprobante' => '10',
                    'Numero' => '0',
                    'FechaEmision' => date('Y-m-d'),
                    'Divisa' => 1,
                    'ValorDivisa' => 1,
                    'ConceptoImpositivo' => $row['ConceptoIVA'],
                    'ConceptoImpositivoPorcentaje' => $row['Porcentaje'],
              //      'MontoImponible' => $NGArticulo,
                    'MontoImponible' => $row['NGporCI'],
                    'Monto' =>  $row['NGporCI'] * $row['Porcentaje'] / 100
              //      'Monto' => $monto
                );

                $R_H = $M_C->recuperarConceptoAsignado($idFactura, $row['ConceptoIVA']);
                // Si el concepto ya esta creado lo updateo sino lo inserto
                if ($R_H) {
                    // Si se modifico manualmente no lo updateo
                    if (!$R_H->Modificado) {
                        $M_C->update($Renglon, 'Id = ' . $R_H->Id);
                    }
                } else {
                    $M_C->insert($Renglon);
                }
            }
        }
    }

    /**
     * sale si el comprobante tiene doble descuento
     *
     * @param int $idComprobante    identificador del comprobante a verificar
     *
     */
    public function salirSi_tieneDobleDescuento($idFactura)
    {
        if ($this->tieneDobleDescuento($idFactura)) {
            throw new Rad_Db_Table_Exception("La factura tiene descuentos por articulos y se intenta ingresar uno general.");
        }
    }

    /**
     * Verifica si la Factura tiene descuentos generales y particulares al mismo tiempo
     *
     * @param int       $idFactura      identificador de la factura
     *
     * @return boolean
     */
    public function tieneDobleDescuento ($idFactura)
    {
        $sql = "select  sum(ifnull(DescuentoEnPorcentaje,0)) rta
                from    ComprobantesDetalles
                where   Comprobante = $idFactura";

        $Cant = $this->_db->fetchOne($sql);

        return ($Cant > 0.001) ? true : false;
    }

    /**
     * Arma los conceptos Impositivos en el wizard
     *
     * @param int       $idFactura      identificador de la factura
     *
     * @return boolean
     */
    public function insertarConceptosDesdeControlador ($idFactura)
    {
        $this->salirSi_NoExiste($idFactura);
        $this->salirSi_estaCerrado($idFactura);
        $this->salirSi_noTieneDetalle($idFactura);
        //$this->salirSi_tieneDetalleConValorCero($idFactura);
        $this->recalcularConceptosImpostivos($idFactura);
    }

    /**
     * Rearma los conceptos impositivos de una Factura que no sean IVA
     *
     * @param int       $idFactura      identificador de la factura
     *
     * @return boolean
     */
    public function recalcularConceptosNoIVA ($idFactura)
    {
        // Recupero el registro Padre
        $R_C = $this->find($idFactura)->current();
        if (!$R_C) {
            throw new Rad_Db_Table_Exception('El Comprobante Padre no existe.');
        }

        $idPersona = $R_C->Persona;
        $fechaEmision = $R_C->FechaEmision;
        $filtro = "ParaCompra";

        $this->recalcularConceptosFacturacion($idFactura, $idPersona, $fechaEmision, $filtro);

        return true;
    }

    /**
     * Rearma los conceptos impositivos de una factura.
     *
     * @param int $idFactura        identificador de la factura
     *
     * @return boolean
     */
    public function recalcularConceptosImpostivos ($idFactura)
    {

        if (!$this->esComprobanteAoM($idFactura)) {
            // Es una factura B o C, Borro todos los conceptos por las dudas
            $BorrarModificados = 1;
            $this->_eliminarConceptosHijos($idFactura, $BorrarModificados);
        } else {
            $BorrarModificados = 0;
            $this->_eliminarConceptosHijos($idFactura, $BorrarModificados);

            // Calculo el IVA
            $this->recalcularIVA($idFactura);

            // Calculo los conceptos que no son IVA
            $this->recalcularConceptosNoIVA($idFactura);

            // Calculo como Agente las Percepciones IB
            $this->recalcularComoAgentePercepcionesIB($idFactura);
        }
    }

    /**
     * Fech Impagas
     *
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchImpagas ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion  = "Estado in (1,2)";
        $where      = $this->_addCondition($where, $condicion);

        return self::fetchAll($where, $order, $count, $offset);
    }

    /**
     * fetch Asociados Y Faltantes De Pagar
     * Se utiliza en Ordenes de Pagos
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchAsociadosYFaltantesDePagar ($where = null, $order = null, $count = null, $offset = null)
    {
        if ($where instanceof Zend_Db_Table_Select) {
            $select = $where;
        } else {
            $select = $this->select();
            if (!is_null($where)) {
                $this->_where($select, $where);
            }
        }

        $condicion  = " Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0 ";
        // Los gastos bancarios no deben poder asociarse a una Orden de Pago NUNCA ! --> Grupo 14, 15 y 16
        $condicion2 = " Comprobantes.TipoDeComprobante not in (select Id from TiposDeComprobantes where Grupo in (14,15,16))";

        $where      = $this->_addCondition($where, $condicion);
        $where      = $this->_addCondition($where, $condicion2);

        //$this->_where($select, $where);

        if (!is_null($order)) {
            $this->_order($select, $order);
        }

        if ( !is_null($count) || !is_null($offset) ) {
            $select->limit($count, $offset);
        }

        $select->having("EstadoPagado in (('Nada') COLLATE utf8_general_ci, ('Parcialmente') COLLATE utf8_general_ci) OR checked = 1");

        return self::fetchAll($select);
    }

    /**
     * fetch Asociados Y Faltantes De Cobrar
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchAsociadosYFaltantesDeCobrar ($where = null, $order = null, $count = null, $offset = null)
    {
        if ($where instanceof Zend_Db_Table_Select) {
            $select = $where;
        } else {
            $select = $this->select();
            if (!is_null($where)) {
                $this->_where($select, $where);
            }
        }

        $this->_where($select, "Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0");

        // Busco todos los Id de GB por venta de facturas que tengan algo disponible junto con los comprobantes de los
/*
        $sql = "
                select  distinct(Comprobantes.Id) as Id
                from    Comprobantes
                inner   join TiposDeComprobantes TC on TC.Id = Comprobantes.TipoDeComprobante
                where   Comprobantes.Cerrado = 1
                AND     Comprobantes.Anulado = 0
                and     ((
                        TC.Grupo = 15
                        and fComprobante_Monto_Disponible(Comprobantes.Id) >= 0.001
                        )
                        or
                        (
                        ".implode(" ",$select->getPart('where'))."
                        ))
                ";
*/
        $sql = "
                SELECT  DISTINCT(Comprobantes.Id) AS Id
                FROM    Comprobantes
                WHERE   ".implode(" ",$select->getPart('where'))."
                UNION
                SELECT  DISTINCT(Comprobantes.Id) AS Id
                FROM    Comprobantes
                INNER   JOIN TiposDeComprobantes TC ON TC.Id = Comprobantes.TipoDeComprobante  AND TC.Grupo = 15
                WHERE   Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0 AND fComprobante_Monto_Disponible(Comprobantes.Id) >= 0.001
                ";

        //Rad_Log::debug($sql);

        $select2 = $this->select();
        $select2->having("EstadoPagado in (('Nada') COLLATE utf8_general_ci, ('Parcialmente') COLLATE utf8_general_ci) OR checked = 1");

        $R = $this->_db->fetchAll($sql);
        $Ids_GBxVentaDeFacturas ='';
        $sep='';

        if (count($R)) {
            //Rad_Log::debug($R);
            foreach ($R as $row) {
                $Ids_GBxVentaDeFacturas .= $sep . $row['Id'];
                $sep = ', ';
            }
            $Ids_GBxVentaDeFacturas = "Comprobantes.Id in ($Ids_GBxVentaDeFacturas)";
            $select2->where("$Ids_GBxVentaDeFacturas");
        } else {
            $this->_where($select2, implode(" ",$select->getPart('where')));
        }

        if (!is_null($order)) {
            $this->_order($select2, $order);
        } else {
            $this->_order($select2, "Comprobantes.FechaEmision desc");
        }

        if ( !is_null($count) || !is_null($offset) ) {
            $select2->limit($count, $offset);
        }

        return self::fetchAll($select2);
    }


    /**
     * Devuelve las facturas nuestras que esten en condiciones de ser entregadas
     * a un Banco para un adelanto por la vta de factura
     *
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchParaAdelantoPorVtaDeFactura ($where = null, $order = null, $count = null, $offset = null)
    {
        if ($where instanceof Zend_Db_Table_Select) {
            $select = $where;
        } else {
            $select = $this->select();
            if (!is_null($where)) {
                $this->_where($select, $where);
            }
        }

        // Piso el orden por que es siempre para un combo
        $this->_order($select, 'FechaEmision desc');


        if ( !is_null($count) || !is_null($offset) ) {
            $select->limit($count, $offset);
        }

        $select->having("EstadoPagado in (('Nada') COLLATE utf8_general_ci)");
        $select->where("Comprobantes.Cerrado = 1 AND Comprobantes.Anulado = 0 AND Comprobantes.TipoDeComprobante in (24,25,26,27,28)");
        return self::fetchAll($select);
    }

    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    public function fetchFacturas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Comprobantes.Cerrado = 1 and Comprobantes.Anulado = 0 and Comprobantes.TipoDeComprobante in (19, 20, 21, 22, 23, 33, 34, 35, 36, 41, 42, 43, 44, 24, 25, 26, 27, 28, 29, 30, 31, 32, 37, 38, 39, 40)";
        $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}
