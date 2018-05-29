<?php

require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_PersonasRegistrosDePrecios
 *
 * Registra los ultimos Precios de los Proveedores y tambien de los Clientes
 * almacena el ultimos precio de compra o venta en el caso que hubiera cambiado el mismo
 * tambien se puede registrar los precios que nos informan
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_PersonasRegistrosDePrecios
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasRegistrosDePrecios extends Rad_Db_Table
{

    protected $_name = 'PersonasRegistrosDePrecios';

    protected $_sort = array('FechaPrecioUltimo desc', 'Articulo asc');

    protected $_referenceMap = array(
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosFinales',
            'refColumns'        => 'Id',
            'refJoinColumns'    => array('Descripcion','Codigo'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Articulos',
            'comboPageSize'     => 20
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'TiposDeDivisas' => array(
            'columns'           => 'TipoDeDivisa',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns'    => array('SimboloMonetario'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDivisas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'Comprobante' => array(
            'columns'           => 'Comprobante',
            'refTableClass'     => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns'    => array('Numero','Punto'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Comprobantes',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'ComprobanteDetalle' => array(
            'columns'           => 'ComprobanteDetalle',
            'refTableClass'     => 'Facturacion_Model_DbTable_ComprobantesDetalles',
            'refTable'          => 'Comprobantes',
            'refColumns'        => 'Id',
        ),
        'TiposDeRegistrosDePrecios' => array(
            'columns'           => 'TipoDeRegistroDePrecio',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeRegistrosDePrecios',
            'refJoinColumns'    => array('Descripcion','Codigo'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeRegistrosDePrecios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_permanentValues = array('TipoDeRegistroDePrecio' => array(1,2,3));


    /**
    * Al cerrar un comprobante registra los precios.
    * vendidos/comprados para un cliente/proveedor
    *
    * Se accede desde un publicador
    */
    public function reasignarPrecioArticulo($row)   {


        try {

            /* Debo ver el tipo de opercion... Compra o Venta */
            $M_TC   = new Facturacion_Model_DbTable_TiposDeComprobantes;
            $R_TC   = $M_TC->find($row->TipoDeComprobante)->current();

            /* Existe el Tipo de Comproabnte */
            if (!$R_TC) throw new Rad_Db_Table_Exception('No se encontro el registro del Tipo de Comprobante.');

            /* FC=1 , FV=6 y */
            if ($R_TC->Grupo != 1 && $R_TC->Grupo != 6) return; // si no es factura salgo y no hago nada

            // el begin va despues de el return del if pq sino quedan las transacciones abiertas
            $this->_db->beginTransaction();

            $M_CD     	= new Facturacion_Model_DbTable_FacturasComprasArticulos(array(), true); // uso fact compra articulos para que me traiga los joins
            $articulos	= $M_CD->fetchAll("Comprobante = $row->Id");


            // Iteramos sobre los articulos del comprobante y registramos los precios
            foreach($articulos as $item) {
                /* controlo que venga el articulo y que el precio sea mayor que 0 */
                if ($item->Articulo && (($item->PrecioUnitario && $item->PrecioUnitario > 0) || ($item->PrecioUnitarioMExtranjera && $item->PrecioUnitarioMExtranjera > 0)))
                {
                    /* Veo si esta en moneda extranjera */
                    if ($row->Divisa == 1) {
                        $pu 			= $item->PrecioUnitario ;
                        $puME 			= 0;
                        $valorDivisa 	= 1;
                    } else {
                        $pu 			= $item->PrecioUnitarioMExtranjera * $row->ValorDivisa;
                        $puME 			= $item->PrecioUnitarioMExtranjera;
                        $valorDivisa 	= $row->ValorDivisa;

                    }

                    if ($R_TC->DiscriminaImpuesto) $pu *= (1 + $item->ConceptosImpositivosPorcentajeActual / 100);

                    $TipoDeRegistro = 0;
                    /* FC=1 , FV=6 y */
                    switch ($R_TC->Grupo) {
                        case 1: // Precio de Compra
                            $TipoDeRegistro = 1;
                        break;
                        case 6: // Precio de Venta
                            $TipoDeRegistro = 2;
                        break;
                    }

                    // Si es el tipo de comprobante correcto (FC y FV) inserto, sino sigo sin decir nada
                    if ($TipoDeRegistro) {
                        /* Recupero el la ultima operacion de este tipo para ese cliente y articulo */
                        $R_PRP	= $this->fetchRow("	TipoDeRegistroDePrecio 	= {$TipoDeRegistro} and
                                                    Articulo 				= {$item->Articulo} and
                                                    Persona 				= {$row->Persona} and
                                                    FechaPrecioUltimo		> '{$row->FechaEmision}' and

                                                    Historico is not null
                                                ", "FechaPrecioUltimo desc");


                        // Si la info que estoy ingresando es mas vieja que la que existe ya (cargo una factura vieja)
                        // debo setearla como historica
                        if ($R_PRP) {
                            $Historico = 1;
                        } else {
                            $Historico = null;
                        }

                        // updateo a historico el valor anterior
                        // Ojo podria borrarlos pero asi queda un registro de cuando se le cambio el precio a un Cliente/Proveedor
                        $data 	= array('Historico' => 1);
                        $where 	= "	Articulo 				= {$item->Articulo} and
                                    TipoDeRegistroDePrecio 	= {$TipoDeRegistro} and
                                    Persona 				= {$row->Persona} and
                                    FechaPrecioUltimo		<= '{$row->FechaEmision}' and

                                    Historico is null";

                        $this->update($data,$where);

                        // Armo el array a insertar
                        $Renglon = array(
                            'Articulo' 						=> $item->Articulo,
                            'Persona' 						=> $row->Persona,
                            'FechaInforme'		 			=> $row->FechaEmision,
                            'FechaPrecioUltimo'		 		=> $row->FechaEmision,
                            'PrecioUltimo'					=> $pu,
                            'PrecioUltimoMonedaExtranjera'	=> $puME,
                            'Cantidad'						=> $item->Cantidad,
                            'Divisa' 						=> $row->Divisa,
                            'ValorDivisa'					=> $valorDivisa,
                            'Comprobante' 					=> $row->Id,
                            'ComprobanteDetalle'			=> $item->Id,
                            'TipoDeRegistroDePrecio'		=> $TipoDeRegistro,
                            'Historico'						=> $Historico

                            );

                        // inserto el nuevo registro
                        $nr = $this->insert($Renglon);
                    }

                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    /**
    * Cuando se elimina un articulo de una Factura de compra o vta quita el registro del valor del articulo
    *
    * Se accede desde un publicador
    */
    public function desasignarPrecioArticulo($row)
    {
        try {

            /* Debo ver el tipo de opercion... Compra o Venta */
            $M_TC   = new Facturacion_Model_DbTable_TiposDeComprobantes;
            $R_TC   = $M_TC->find($row->TipoDeComprobante)->current();

            /* Existe el Tipo de Comproabnte */
            if (!$R_TC) throw new Rad_Db_Table_Exception('No se encontro el registro del Tipo de Comprobante.');

            /* FC=1 , FV=6 y */
            if ($R_TC->Grupo != 1 && $R_TC->Grupo != 6) return; // si no es factura salgo y no hago nada

            $TipoDeRegistro = 0;
            /* FC=1 , FV=6 y */
            switch ($R_TC->Grupo) {
                case 1: // Precio de Compra
                    $TipoDeRegistro = 1;
                break;
                case 6: // Precio de Venta
                    $TipoDeRegistro = 2;
                break;
            }


            $this->_db->beginTransaction();

            // busco los articulos del comprobante
            $M_CD       = new Facturacion_Model_DbTable_FacturasComprasArticulos(array(), true); // uso fact compra articulos para que me traiga los joins
            $articulos  = $M_CD->fetchAll("Comprobante = $row->Id and Articulo is not null");

            $where = 'Comprobante = '.$row->Id;

            foreach ($articulos as $key => $articulo) {
                $this->_marcarUltimoNoHistorico($articulo, $row, $TipoDeRegistro);
            }

            // borro los registros
            $this->delete($where);

            $this->_db->commit();

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    protected function _marcarUltimoNoHistorico($articulo, $comprobante, $tipoDeRegistroDePrecio)
    {
        // busco el anterior para sacarle la marca de "historico"
        $ultimo = $this->fetchRow(
            "Articulo = {$articulo->Articulo} AND TipoDeRegistroDePrecio = {$tipoDeRegistroDePrecio} AND Persona = {$comprobante->Persona}",
            array("FechaPrecioUltimo Desc")
        );

        if ($ultimo && $ultimo->Historico) {
            $ultimo->Historico = 0;
            $ultimo->save();
        }
    }

    /**
    * Regenera todos los valores a partir de las facturas de compra y vta.
    *
    * Se accede desde un publicador
    *
    * ----------------------------------------------------------------------------
    * OJO... solo para llamar desde linea de comando (RegenerarRegistroDePrecios)
    * sino da timeout.
    * ----------------------------------------------------------------------------
    */
    public function regenerarPrecioArticulo()   {

        $this->_db->beginTransaction();
        try {

            // Elimino todos los registros viejos (ojo no borrar los informados)
            $this->delete("TipoDeRegistroDePrecio in (1,2)");

            $tipos = $this->_db->fetchCol("select Id from TiposDeComprobantes where Grupo in (1,6)");
            $tipos = implode(",", $tipos);

            $M_C    = new Facturacion_Model_DbTable_Comprobantes;
            $R_C    = $M_C->fetchAll("TipoDeComprobante in ($tipos)");

            foreach ($R_C as $k => $r) {
                $this->reasignarPrecioArticulo($r);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchComprasyVentas($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "PersonasRegistrosDePrecios.TipoDeRegistroDePrecio in (1,2) and ifnull(Historico,0) <> 1");
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchInformados($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "PersonasRegistrosDePrecios.TipoDeRegistroDePrecio in (3) and ifnull(Historico,0) <> 1");
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchHistoricoCompra($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "PersonasRegistrosDePrecios.TipoDeRegistroDePrecio in (1,3)");
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchHistoricoVenta($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "PersonasRegistrosDePrecios.TipoDeRegistroDePrecio = 2");
        return parent::fetchAll($where, $order, $count, $offset);
    }
}
