<?php
require_once 'ComprobantesDetalles.php';

/**
 * @class       Facturacion_Model_DbTable_FacturasComprasArticulos
 * @extends     Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * Facturas Compras Articulos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *      Id                  -> Identificador Unico
 *      Comprobante         -> identificador de la Factura Compra
 *      TipoDeComprobanteDetalle    -> identidicador de Art, Serv, tiket
 *      Articulo            -> identificador del articulo, servicio, etc (puede ser null)
 *      CuentaCasual        -> Cuenta del Plan de Cuenta a utilizar en el caso qeu no se indique el articulo
 *      Cantidad            -> Cantidad de elementos del articulo indicado
 *      PrecioUnitario      -> Precio por unidad del articulo expresado en moneda local
 *      PrecioUnitarioMExtranjera   -> Precio por unidad del articulo expresado en otra moneda
 *      DescuentoEnPocentaje        -> Descuento realizado sobre el precio unitario (rango 0.01 a 99.99)
 *      Modificado          -> Bndera que indica si fue modificado manualmente
 *      Observaciones       -> Obs. internas
 *
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_FacturasComprasArticulos extends Facturacion_Model_DbTable_ComprobantesDetalles
{
    protected $_name = 'ComprobantesDetalles';

    protected $_validators = array(
        'ConceptoImpositivo' => array(
            'NotEmpty',
            'messages'  => array('Se debe ingresar el tipo de IVA.')
        ),
        'DescuentoEnPorcentaje' => array(
            array('Between',0, 100, true)
        )
    );

    protected $_referenceMap = array(
        'ConceptosImpositivos' => array(
            'columns'        => 'ConceptoImpositivo',
            'refTableClass'  => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns' => array('Descripcion','PorcentajeActual'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/EsIVACompra',
            'refTable'       => 'ConceptosImpositivos',
            'refColumns'     => 'Id',
        ),
        'Articulos' => array(
            'columns'        => 'Articulo',
            'refTableClass'  => 'Base_Model_DbTable_Articulos',
            'refJoinColumns' => array(
                'Descripcion',
                'DescArreglada' => 'IF(ComprobantesDetalles.Articulo is null,ComprobantesDetalles.Observaciones,Articulos.Descripcion)',
                'Tipo'
            ),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Articulos',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'FacturaCompra' => array(
            'columns'        => 'Comprobante',
            'refTableClass'  => 'Facturacion_Model_DbTable_FacturasCompras',
            'refJoinColumns' => array('Numero'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
        ),
        'PlanesDeCuentas' => array(
            'columns'        => 'CuentaCasual',
            'refTableClass'  => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns' => array('Descripcion', 'Jerarquia'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/PlanCuentaImputable',
            'refTable'       => 'PlanesDeCuentas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        )
    );

    /** Inseta un Registro, en el caso que exista ya debo updatear la cantidad
     * @param array $data   Datos a insertar
     * @return mixed
     * @throws Rad_Db_Table_Exception
     * @throws Exception
     */
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $M_FC = new Facturacion_Model_DbTable_FacturasCompras(array(), false);

            // Controlo que de existir la cantidad sea mayor a 0
            if (isset($data['Cantidad']) && $data['Cantidad'] <= 0) throw new Rad_Db_Table_Exception('La cantidad no puede ser 0 (cero).');

            $R_FC = $M_FC->find($data['Comprobante'])->current();

            if (!$R_FC) throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');

            // Verifico que no exista un doble porcentaje
            if (isset($data['DescuentoEnPorcentaje']) &&
                    $data['DescuentoEnPorcentaje'] > 0.01 && $R_FC->DescuentoEnMonto) {
                throw new Rad_Db_Table_Exception('Se intenta ingresar un doble descuento.');
            }

            $M_FC->salirSi_estaCerrado($data['Comprobante']);

            // Verifico si ya existe el articulo en el comprobante
            /*
            if ($data['Articulo'] && $this->estaElArticuloEnComprobante($data['Comprobante'], $data['Articulo'])) {
                // el articulo se encuentra en el comprobante => updatear cantidad
                $Rx = $this->fetchRow('Comprobante = ' . $data['Comprobante'] . ' and Articulo = ' . $data['Articulo']);
                if ($Rx) {
                    $data['Cantidad'] = $Rx->Cantidad + $data['Cantidad'];
                }
                $this->update($data, 'ComprobantesDetalles.Id = $Rx->Id');
                $id = $Rx->Id;
                // Publico
                Rad_PubSub::publish('Facturacion_FCA_Updateado', $Rx);
                // Como se updateo arreglo los comporobantes impositivos
                $M_FC->recalcularConceptosImpostivos($data['Comprobante']);
            } else {
             */


            // Reviso si viene el articulo, de no venir revisar los otros campos requeridos
            if (isset($data['Articulo']) && !$data['Articulo']) {
                // Si no detalla el articulo debe venir el IVA y la Cuenta
                if (isset($data['ConceptoImpositivo']) && !$data['ConceptoImpositivo']) throw new Rad_Db_Table_Exception('Falta ingresar el tipo de IVA.');
                if (isset($data['CuentaCasual']) && !$data['CuentaCasual'])             throw new Rad_Db_Table_Exception('Falta ingresar la cuenta a la cual asociar uno de los detalles.');
            } else {
                // Busco el IVA correspondiente al Articulo
                if (!$data['ConceptoImpositivo']) {
                    $data['ConceptoImpositivo'] = $this->recuperarArticuloIVA($data['Articulo']);
                }
            }

            // Si esta en moneda extranjera calculo el Precio Unitario en moneda local
            if ($M_FC->estaEnMonedaExtranjera($data['Comprobante'])) {
                if (!$data['PrecioUnitario']) {
                    $data['PrecioUnitario'] = 0;
                    $data['PrecioUnitarioMExtranjera'] = $M_FC->recuperarPUdeListaDePrecio($data['Comprobante'], $data['Articulo']);
                } else {
                    $data['PrecioUnitarioMExtranjera'] = $data['PrecioUnitario'];
                    $data['PrecioUnitario'] = $data['PrecioUnitarioMExtranjera'] * $R_FC->ValorDivisa;
                }
            } else {
                if (!$data['PrecioUnitario']) {
                    $data['PrecioUnitarioMExtranjera'] = 0;
                    if ($data['Articulo']) {
                        $data['PrecioUnitario'] = $M_FC->recuperarPUdeListaDePrecio($data['Comprobante'], $data['Articulo']);
                    }
                }
            }

            // Inserto el articulo y publico
            $id     = parent::insert($data);
            $R_Ins  = $this->find($id)->current();
            Rad_PubSub::publish('Facturacion_FCA_Insertado', $R_Ins);
            // Recalculo los conceptos impositivos
            $M_FC->recalcularConceptosImpostivos($R_Ins->Comprobante);


            //}

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /** Updateo un registro
     * @param array $data
     * @param array $where
     * @return mixed|void
     * @throws Rad_Db_Table_Exception
     * @throws Exception
     */
    public function update($data, $where)
    {
        $this->_db->beginTransaction();

        try {

            // Verifico las cuestiones de forma
            if (isset($data['Cantidad']) && $data['Cantidad'] <= 0) throw new Rad_Db_Table_Exception('La cantidad no puede ser 0 (cero).');

            $M_FC = new Facturacion_Model_DbTable_FacturasCompras(array(), false);
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                // Salgo si no se puede modificar la factura
                $M_FC->salirSi_estaCerrado($row['Comprobante']);

                // Recupero la cabecera
                $R_FC = $M_FC->find($row['Comprobante'])->current();
                if (!$R_FC) throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');


                // Verifico que no exista un doble descuento
                if (isset($data['DescuentoEnPorcentaje']) &&
                        $data['DescuentoEnPorcentaje'] > 0.01 && $R_FC->DescuentoEnMonto > 0.01) {
                    throw new Rad_Db_Table_Exception('Se intenta ingresar un doble descuento.');
                }

                // Opero segun en que moneda este
                if ($M_FC->estaEnMonedaExtranjera($row['Comprobante'])) {
                    // Calculo el PU en moneda local
                    if (!$data['PrecioUnitarioMExtranjera']) {
                        $data['PrecioUnitario'] = 0;
                    } else {
                        $data['PrecioUnitario'] = $data['PrecioUnitarioMExtranjera'] * $R_FC->ValorDivisa;
                    }
                } else {
                    $data['PrecioUnitarioMExtranjera'] = 0;
                }

                // Updateo
                parent::update($data, 'Id=' . $row['Id']);
                Rad_PubSub::publish('Facturacion_FCA_Updateado', $row);

                $M_FC->recalcularConceptosImpostivos($row['Comprobante']);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Esta funcion borra un articulo sin hacer las validaciones comunes
     * EXISTE SOLO PARA USAR DENTRO DE Facturacion_Model_DbTable_FacturasCompras->eliminarDetalle()
     *
     * @param $id
     * @throws Rad_DbTable_Exception
     */
    public function forceDelete($id)
    {
         $row = $this->find($id)->current();
         if (!$row) throw new Rad_DbTable_Exception('No existe el articulo de la factura que esta intentando eliminar.');
         parent::delete('Id =' . $row->Id);
         Rad_PubSub::publish('Facturacion_FCA_Borrado', $row);
    }
    /**
     * Borra los registros indicados
     *
     * @param array $where
     *
     */
    public function delete($where)
    {

        try {
            $this->_db->beginTransaction();

            $M_FC = new Facturacion_Model_DbTable_FacturasCompras(array(), false);

            $reg = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg)) {

                foreach ($reg as $row) {
                    // Salgo si no se puede modificar la factura
                    $M_FC->salirSi_estaCerrado($row['Comprobante']);
                }
                foreach ($reg as $row) {
                    // Publico y borro el renglon
                    parent::delete('Id =' . $row['Id']);
                    Rad_PubSub::publish('Facturacion_FCA_Borrado', $row);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}
