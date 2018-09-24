<?php
require_once('Rad/Db/Table.php');

/**
 * @class       Facturacion_Model_DbTable_RecibosDetalles
 * @extends     Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * Recibos Facturas
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *      Id                  -> Identificador Unico
 *      Comprobante         -> identificador de la Factura Venta
 *      PrecioUnitario      -> Monto pagado expresado en moneda local
 *      PrecioUnitarioMExtranjera   -> Monto pagado expresado expresado en otra moneda
 *      Modificado          -> Bndera que indica si fue modificado manualmente
 *      Observaciones       -> Obs. internas
 * Opcionales (se debe ingresar uno solo)
 *      ComprobanteRelacionado      -> identificador de un comprobante que se usa como forma de cobro (Retenciones)
 *      Caja                        -> Identificador de la Caja que se utilizo para sacar el dinero para el cobro
 *      Cheque                      -> Identificador del cheque utilizado para cobrar
 *      TransaccionBancaria         -> Identificador de la Tramsaccion Bancaria utilizada para el cobro
 *
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_RecibosDetalles extends Facturacion_Model_DbTable_ComprobantesPagosDetalles
{
    protected $estadoChequeUtilizable = 8;
    protected $estadoChequeUtilizado  = 6;

    protected $_name = "ComprobantesDetalles";
    /*

    */
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
              ->joinRef('ConceptosImpositivos', array(
                    'Descripcion' => '{remote}.Descripcion'
                ));
        }
    }

    protected function getCabecera() {
        return  new Facturacion_Model_DbTable_Recibos();
    }

    /**
     * Permite agregar un pago por una tarjeta de credito generando el cupon y la tarjeta (de no existir esta ultima)
     *
     * @param int $idComprobante    Identificador de la Orden de Pago
     * @param int $idCupon          Identificadores del cupon de pago
     *
     * @return unknown_type
     */
    public function agregarTarjeta($idRecibo, $monto, $cuotas, $numTarjeta, $tipo)
    {
        try {
            $this->_db->beginTransaction();

            $monto      = $this->_db->quote($monto);
            $cuotas     = $this->_db->quote($cuotas, 'INTEGER');
            $numTarjeta = $this->_db->quote($numTarjeta, 'INTEGER');
            $tipo       = $this->_db->quote($tipo, 'INTEGER');

            if (!$monto) throw new Rad_Db_Table_Exception("Ingrese el monto del pago");
            if (!$cuotas) throw new Rad_Db_Table_Exception("Ingrese la cantidad de cuotas");
            if (!$numTarjeta) throw new Rad_Db_Table_Exception("Ingrese el numero de tarjeta");
            if (!$tipo) throw new Rad_Db_Table_Exception("Seleccione el tipo de tarjeta");

            $tarjetas = new Facturacion_Model_DbTable_TarjetasDeCredito;
            // busco la tarjeta
            $tarjeta = $tarjetas->fetchRow("Numero = $numTarjeta AND TarjetaCreditoMarca = $tipo");

            $modeloCompPagos = new Facturacion_Model_DbTable_ComprobantesPagos;
            $recibo = $modeloCompPagos->find($idRecibo)->current();

            if (!$recibo) throw new Rad_Db_Table_Exception("No se encontro el comprobante al que intenta agregar el pago");


            // no existe?
            if (!$tarjeta) {

                $tarjeta                      = $tarjetas->createRow();
                $tarjeta->Numero              = $numTarjeta;
                $tarjeta->Persona             = $recibo->Persona;
                $tarjeta->TarjetaCreditoMarca = $tipo;
                $tarjeta->save();
            }

            // creamos el cupon de pago
            $cupones = new Facturacion_Model_DbTable_TarjetasDeCreditoCuponesEntrantes;
            $cupon   = $cupones->createRow();

            $cupon->TarjetaDeCredito = $tarjeta->Id;
            $cupon->Monto            = $monto;
            $cupon->FechaCupon       = date('Y-m-d H:i:s');
            $cupon->CantidadDePagos  = $cuotas;
            $cupon->save();

            $respuesta = parent::insertPagosTarjeta($idRecibo, $cupon->Id);

            $this->_db->commit();
            return $respuesta;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}