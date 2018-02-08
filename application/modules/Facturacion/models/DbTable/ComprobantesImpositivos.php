<?php

/**
 * @class       Facturacion_Model_DbTable_ComprobantesImpositivos
 * @extends     Facturacion_Model_DbTable_Comprobantes
 *
 * Conceptos Impositivos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *      Id                  -> Identificador Unico
 *      Persona             -> Proveedor que nos realiza
 *      ComprobantePadre    -> Comprobante que lo genero o del cual depende
 *      TipoDeComprobante   -> 10, 11, 12, 13
 *      Numero              -> Numero interno del comprobante
 *      FechaEmision        -> Fecha de generacion de la factura Padre
 *      Divisa              -> (cte) = 1
 *      ValorDivisa         -> (cte) = 1
 *      Cerrada             -> Indica si la factura es modificable o no.
 *      Observaciones       -> Obs. internas
 *      ConceptoImpositivo  -> Idetificador del Concepto impositivo
 *      ConceptoImpositivoPorcentaje -> Porcentaje con el que se calculo el Concepto Impositivo
 *              MA_NG_Anterior      -> Monto Acumulado, Neto Gravado antes de la Generacion del comprobante
 *              MA_MT_Anterior      -> Monto Acumulado, Monto Total antes de la Generacion del comprobante
 *              MA_CI_Anterior      -> Monto Acumulado, Concepto Impositivo antes de la Generacion del comprobante
 *              MontoImponible      -> Monto sobre el que se calcula el concepto
 *      Monto               -> Monto calculado del concepto (se puede modificar manualmente)
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_ComprobantesImpositivos extends Facturacion_Model_DbTable_Comprobantes
{
    protected $_sort = array("FechaEmision desc");
    // Relaciones
    protected $_referenceMap = array(
        'ConceptosImpositivos' => array(
            'columns'        => 'ConceptoImpositivo',
            'refTableClass'  => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'ConceptosImpositivos',
            'refColumns'     => 'Id'
        ),
        'TiposDeComprobantes' => array(
            'columns'        => 'TipoDeComprobante',
            'refTableClass'  => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeComprobantes',
            'refColumns'     => 'Id'
        ),
        'Personas' => array(
            'columns'        => 'Persona',
            'refTableClass'  => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id'
        ),
        'LibrosIVA' => array(
            'columns'        => 'LibroIVA',
            'refTableClass'  => 'Contable_Model_DbTable_LibrosIVA',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => '/datagateway/combolist/fetch/Abiertos',
            'refTable'       => 'LibrosIVA',
            'refColumns'     => 'Id'
        ),
        'ComprobantesPadres' => array(
            'columns'        => 'ComprobantePadre',
            'refTableClass'  => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('Numero'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id'
        )
    );
    /**
     * Valores Default tomados del modelo y no de la base
     *
     */
    protected $_defaultSource = self::DEFAULT_CLASS;
    /**
     * Valores Default
     *
     *  'Divisa'        => '1',
     *  'ValorDivisa'   => '1',
     *  'Estado'        => '1'
     *
     */
    protected $_defaultValues = array(
        'Divisa'      => '1',
        'ValorDivisa' => '1',
        'Estado'      => '1',
        'Punto'       => '1',
        'Cerrado'     => '0',
        'Despachado'  => '0',
        'Anulado'     => '0'
    );
    /**
     * Valores Permanentes
     *
     * 'TipoDeComprobante' => array('10','11','12','13')
     *
     */
    protected $_permanentValues = array(
        'TipoDeComprobante' => array('10', '11', '12', '13')
    );

    // Se hereda
    // protected $_dependentTables      = array();


    public function init ()
    {
        $this->_defaultValues['FechaEmision'] = date('Y-m-d');
        parent::init();
    }



    public function insert($data)
    {
        $this->_db->beginTransaction();
        try {
            $this->salirSi_estaCerrado($data['ComprobantePadre']);

            // Salgo si la FC no es A o M o en el caso de los pagos o cobros existe una factura que no sea A o M
            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
            $M_C->salirSi_NoEsComprobanteAoM($data['ComprobantePadre']);


            // este control se llevo al insert de FacturasVentasCenceptos en las facturas compras se debe permitir
            //$this->salirSi_estaElConceptoAsignado($data['ComprobantePadre'], $data['ConceptoImpositivo']);

            // Recupero el tipo de comprobante que se trata
            $ImputacionFiscal = $this->recuperarImputacionFiscalDelComprobante($data['ComprobantePadre']);

            if ($this->esIVA($data['ConceptoImpositivo'])) {
                switch ($ImputacionFiscal) {
                    case 'CreditoFiscal' : $data['TipoDeComprobante'] = 10;
                        break;
                    case 'DebitoFiscal' : $data['TipoDeComprobante'] = 11;
                        break;
                }
            } else {
                switch ($ImputacionFiscal) {
                    case 'CreditoFiscal' : $data['TipoDeComprobante'] = 12;
                        break;
                    case 'DebitoFiscal' : $data['TipoDeComprobante'] = 13;
                        break;
                }
            }

            // Completo los datos que faltan a partir del padre
            $R_C_P = $M_C->find($data['ComprobantePadre'])->current();
            if (!$R_C_P) {
                throw new Rad_Db_Table_Exception("No se puede localizar el comprobante padre.");
            }
            // Recupero el Monto Imponible del Comprobate Padre
            $MontoImponible = $M_C->recuperarMontoImponibleFacturacion($data['ConceptoImpositivo'], $data['ComprobantePadre']);
            $data['MontoImponible'] = $MontoImponible;
            $data['Persona'] = $R_C_P->Persona;
            $data['LibroIVA'] = $R_C_P->LibroIVA;

            // De no venir el porcentaje lo busco
            if (!$data["ConceptoImpositivoPorcentaje"]) {
                $data['ConceptoImpositivoPorcentaje'] = $this->recuperarPorcentajeConcepto($data['Persona'], $data['ConceptoImpositivo']);
            }

            // Aunque no quede monto Imponible inserto igual
            if ($MontoImponible > 0.00001) {
                $data['Monto'] = $MontoImponible * $data["ConceptoImpositivoPorcentaje"] / 100;
            } else {
                $data['Monto'] = 0;
            }

            // Indico que se cargo manualmente
            $data['Modificado'] = 1;

            $id = parent::insert($data);

            /*
              $row = $M_C->find($data['ComprobantePadre'])->current();
              $M_xx = new Contable_Model_DbTable_LibrosIVADetalles(array(),false);
              $M_xx->asentarLibroIVA($row);
             */

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {

            // Mapeo el $data con los campos que se pueen modificar a variables
            $gPorcentaje = $data['ConceptoImpositivoPorcentaje'];
            $gMonto = $data['Monto'];

            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);

            $reg = $this->fetchAll($where);

            if (!count($reg)) {
                throw new Rad_Db_Table_Exception("No se puede localizar el registro a modificar.");
            }

            // Controles
            foreach ($reg as $row) {
                $this->salirsi_estaCerrado($row->Id);
                $this->salirSi_estaCerrado($row->ComprobantePadre);

                // Que no modifique la persona del comprobante impositivo
                if (isset($data['ComprobantePadre']) && $data['ComprobantePadre']) {
                    throw new Rad_Db_Table_Exception("No se puede modificar la asignacion del comprobante Impositivo.");
                }
                // Que no modifique el concepto impositivo
                if (isset($data['ConceptoImpositivo']) && $data['ConceptoImpositivo'] != $row->ConceptoImpositivo) {
                    throw new Rad_Db_Table_Exception("No se puede modificar el Concepto Impositivo. De ser necesario eliminelo e ingreselo correctamente.");
                }
                // Que en el caso de los IVA no modifique el porcentaje
                if ($gPorcentaje && $gPorcentaje != $row->ConceptoImpositivoPorcentaje &&
                        $this->esIVA($row->ConceptoImpositivo)) {
                    throw new Rad_Db_Table_Exception("No se puede modificar el porcentaje de IVA.");
                }
            }

            /*
            $M_FC = new Facturacion_Model_DbTable_Comprobantes(array(), false);
            $M_CI = new Base_Model_DbTable_ConceptosImpositivos(array(), false);
            */

            // updateo y publico
            foreach ($reg as $row) {

                $whereRow = ' Id = ' . $row->Id;

                // Si se borro el porcentaje busco el que corresponda
                if (!$gPorcentaje) {
                    $data["ConceptoImpositivoPorcentaje"] = $this->recuperarPorcentajeConcepto($row->Persona, $row->ConceptoImpositivo);
                    $gPorcentaje = $data["ConceptoImpositivoPorcentaje"];
                }

                // Si cambio el porcentaje y no se indico el monto o no se modifico, recalculo el monto
                if ($gPorcentaje != $row->ConceptoImpositivoPorcentaje) {
                    // Si cambio el porcentaje pero no cambio el monto --> recalculo el monto
                    if (!$gMonto || ($gMonto && $gMonto == $row->Monto)) {
                        $MontoImponible = $M_C->recuperarMontoImponibleFacturacion($row->ConceptoImpositivo, $row->ComprobantePadre);
                        $data['Monto'] = $MontoImponible * $gPorcentaje / 100;
                        $data['MontoImponible'] = $MontoImponible;
                    } else {
                        // Cambio el monto y el porcentaje
                        //$data['MontoImponible'] = 0;
                    }
                } else {
                    // Si no cambio el porcentaje y no cambio el monto uso el valor ingresado
                    if (!$gMonto || ($gMonto && $gMonto == $row->Monto)) {
                        // Recupero el Monto Imponible del Comprobate Padre
                        $MontoImponible = $M_C->recuperarMontoImponibleFacturacion($row->ConceptoImpositivo, $row->ComprobantePadre);
                        $data['Monto'] = $MontoImponible * $gPorcentaje / 100;
                        $data['MontoImponible'] = $MontoImponible;
                    } else {
                        // Cambio el monto y no el porcentaje
                        //$data['MontoImponible'] = 0;
                    }
                }

                $data['Modificado'] = 1;
                parent::update($data, $whereRow);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Esta funcion borra un comprobante impositivo sin hacer las validaciones comunes
     * EXISTE SOLO PARA USAR DENTRO DE Facturacion_Model_DbTable_Facturas->_eliminarConceptosHijos()
     *
     * @param type $id
     */
    public function forceDelete($id)
    {

//        $id = $this->_db->qoute($id, 'INTEGER');
        $row = $this->find($id)->current();
        if (!$row) throw new Rad_DbTable_Exception('No existe el comprobante imp. que esta intentando eliminar.');
        Rad_PubSub::publish('CoI_preBorrarConcepto', $row);
        parent::delete("Id = $id");
        Rad_PubSub::publish('CoI_posBorrarConcepto', $row);
    }

    public function delete($where)
    {
        $this->_db->beginTransaction();
        try {

            $reg = $this->fetchAll($where);

            // Controles
            foreach ($reg as $row) {
                // Reviso si el esta cerrado
                $this->salirsi_estaCerrado($row->Id);
                // Reviso si el padre esta cerrado
                $this->salirsi_estaCerrado($row->ComprobantePadre);
            }
            // Borro y publico
            foreach ($reg as $row) {
                Rad_PubSub::publish('CoI_preBorrarConcepto', $row);
                parent::delete("Id = $row->Id");
                Rad_PubSub::publish('CoI_posBorrarConcepto', $row);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Cierra el Comprobante del Concepto
     *
     * @param int $idComprobante    identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function cerrarConcepto($row)
    {
        $this->salirsi_estaCerrado($row->Id);

        $data = array(
            'Cerrado'       => 1,
            'FechaCierre'   => date('Y-m-d H:i:s')
        );
        Rad_PubSub::publish('CoI_preCerrarConcepto', $row);
        parent::update($data, "Id = $row->Id");
        Rad_PubSub::publish('CoI_posCerrarConcepto', $row);
    }

    /**
     * Verifica si un conceto impositivo es de tipo IVA
     *
     * @param int $idConcepto   identificador del concepto impositivo a verificar
     *
     * @return boolean
     */
    public function esIVA($idConcepto)
    {
        $M_CI = new Base_Model_DbTable_ConceptosImpositivos(array(), false);
        $R_CI = $M_CI->fetchRow("Id = $idConcepto");

        if ($R_CI->EsIVA == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si un conceto impositivo es de tipo INGRESOS BRUTOS
     *
     * @param int $idConcepto identificador del concepto impositivo a verificar
     *
     * @return boolean
     */
    public function esIB($idConcepto)
    {
        $M_CI = new Base_Model_DbTable_ConceptosImpositivos(array(), false);
        $R_CI = $M_CI->fetchRow("Id = $idConcepto");

        if ($R_CI->TipoDeConcepto == 3) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el monto de los conceptos sea mayor a 0 (cero)
     *
     * @param int $idComprobante    identificador del concepto impositivo a verificar
     *
     * @return boolean
     */
    public function controlTotalConcepto($idComprobante)
    {
        $R_CI = $this->fetchAll("ComprobantePadre = $idComprobante");
        foreach ($R_CI as $row) {
            if ($row["Monto"] < 0) {
                throw new Rad_Db_Table_Exception("Existen Conceptos con montos menores a 0 (cero).");
            }
        }
    }

}
