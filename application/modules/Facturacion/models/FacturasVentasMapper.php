<?php

class Facturacion_Model_FacturasVentasMapper extends Rad_Mapper
{
    protected $_class = 'Facturacion_Model_DbTable_FacturasVentas';

    /**
     * Anula un comprobante
     */
    public function anular ($id)
    {
        $this->_model->anular($id);
    }

    /**
     * Cierra un comprobante
     */
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }

    /**
     * Devuelve el proximo numero de una factura de acuerdo al punto de venta
     */
    public function recuperarProximoNumero ($punto, $tipo)
    {
        return $this->_model->recuperarProximoNumero($punto, $tipo);
    }

    /**
     * Inserta conceptos impositivos desde el controlador salteando logica de modelos
     */
    public function insertarConceptosDesdeControlador ($id)
    {
        // desactivo los joins y calculados para mejorar la perfomance
        $this->_model->setFetchWithAutoJoins(false);
        $this->_model->setFetchWithCalcFields(false);
        $this->_model->insertarConceptosDesdeControlador($id);
    }

    /**
     * Controla que el total del concepto sea mayor a 0 (cero)
     */
    public function getControlTotalConcepto ($id)
    {
        $M_CI = new Facturacion_Model_DbTable_ComprobantesImpositivos();
        $M_CI->controlTotalConcepto($id);
    }

    public function getTotal($id)
    {
        return $this->_model->recuperarMontoTotal($id);
    }

    /**
     * Cambia la imputacion de un comprobante a un libro de iva
     */
    public function cambiarImputacionIva ($idComprobante, $idLibroIVA)
    {
        // desactivo los joins y calculados para mejorar la perfomance
        $this->_model->setFetchWithAutoJoins(false);
        $this->_model->setFetchWithCalcFields(false);

        $this->_model->cambiarImputacionIVA($idComprobante, $idLibroIVA);
    }

    /**
     * Compensa una Factura de Venta en su totalidad con una Nota de Credito
     */
    public function compensarFacturasConNotas ($idComprobante)
    {
        $this->_model->compensarFacturasConNotas($idComprobante);
    }

    /**
     * Devuelve el estado financiero y de cuenta corriente de un cliente a determinada fechas
     */
    public function getEstadoDeCuentaPorCliente ($idCliente)
    {
        $ECP = new Contable_Model_EstadosCuentasPersonas();

        $hoy = new DateTime();
        $mas30dias = clone $hoy;
        $mas60dias = clone $hoy;
        $mas180dias = clone $hoy;

        $mas30dias->modify('+30 day');
        $mas60dias->modify('+60 day');
        $mas180dias->modify('+180 day');

        return array(
            'cc_hoy'        => number_format((float)$ECP->cuentaCorrienteAFecha($idCliente, $hoy->format('Y-m-d')), 2, ',', '.'),
            'ef_hoy'        => number_format((float)$ECP->estadoFinancieroAFecha($idCliente, $hoy->format('Y-m-d')), 2, ',', '.'),
            'ef_30dias'     => number_format((float)$ECP->estadoFinancieroAFecha($idCliente, $mas30dias->format('Y-m-d')), 2, ',', '.'),
            'ef_60dias'     => number_format((float)$ECP->estadoFinancieroAFecha($idCliente, $mas60dias->format('Y-m-d')), 2, ',', '.'),
            'ef_180dias'    => number_format((float)$ECP->estadoFinancieroAFecha($idCliente, $mas180dias->format('Y-m-d')), 2, ',', '.'),
            'ef_mas'        => number_format((float)$ECP->estadoFinancieroAFecha($idCliente), 2, ',', '.'),
        );
    }

    public function getComprobanteExportacion($id)
    {
        $model = new Facturacion_Model_DbTable_ComprobantesDeExportaciones;

        $id = $model->getAdapter()->quote($id, 'INTEGER');

        $row = $model->fetchRow("comprobante = $id");

        if (!$row) {
            $row = $model->createRow();
            $row->Comprobante = $id;
        }

        return $row->toArray();
    }
}
