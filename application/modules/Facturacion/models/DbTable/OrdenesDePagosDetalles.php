<?php

/**
 * @class       Facturacion_Model_DbTable_OrdenesDePagosFacturas
 * @extends     Facturacion_Model_DbTable_ComprobantesDetalles
 *
 * Ordenes de Pagos Detalles
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *      Id                          -> Identificador Unico
 *      Comprobante                 -> identificador de la Factura Compra
 *      PrecioUnitario              -> Monto pagado expresado en moneda local
 *      PrecioUnitarioMExtranjera   -> Monto pagado expresado expresado en otra moneda
 *      Modificado                  -> Bndera que indica si fue modificado manualmente
 *      Observaciones               -> Obs. internas
 * Opcionales (se debe ingresar uno solo)
 *      ComprobanteRelacionado      -> identificador de un comprobante que se usa como forma de pago (Retenciones)
 *      Caja                        -> Identificador de la Caja que se utilizo para sacar el dinero para el pago
 *      Cheque                      -> Identificador del cheque utilizado para pagar
 *      TransaccionBancaria         -> Identificador de la Tramsaccion Bancaria utilizada para el pago
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 */
class Facturacion_Model_DbTable_OrdenesDePagosDetalles extends Facturacion_Model_DbTable_ComprobantesPagosDetalles
{
    protected $estadoChequeUtilizable = 6;
    protected $estadoChequeUtilizado  = 3;

    protected $_name = 'ComprobantesDetalles';
    protected $_calculatedFields = array(
        'DescArreglada' => "case
	        when (ComprobantesDetalles.Observaciones is not null) then (ComprobantesDetalles.Observaciones)
	        when (ComprobantesDetalles.ConceptoImpositivo is not null) then (ConceptosImpositivos.Descripcion)
                when (ComprobantesRelacionadosConceptosImpositivos.Id is not null) then (ComprobantesRelacionadosConceptosImpositivos.Descripcion)
	        else ''
        end"
    );

    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('ComprobantesRelacionados')
              ->joinRef('ConceptosImpositivos',array('ComprobantesConceptosImpositivosDescripcion' => 'ComprobantesRelacionadosConceptosImpositivos.Descripcion'));
        }
    }
}