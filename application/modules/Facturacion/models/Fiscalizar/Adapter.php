<?php
/**
 * Facturacion_Fiscalizar_Adapter_Exception
 *
 * Exception de Adaptador abstracto para fiscalizar comprobantes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martin Alejandro Santangelo
 */
class Facturacion_Fiscalizar_Adapter_Exception extends Rad_Exception
{}

/**
 * Facturacion_Fiscalizar_Adapter_Abstract
 *
 * Adaptador abstracto para fiscalizar comprobantes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martin Alejandro Santangelo
 */
abstract class Facturacion_Fiscalizar_Adapter_Abstract
{
    /**
     * Indica si el adaptador requiere generacion de reporte para el comprobante
     */
    protected $requiereImpresion = false;
    /**
     * Indica si este adaptador autogenera el número de comprobante
     */
    protected $generaNumero      = true;
    /**
     * Tipos de comprobantes que puede fiscalizar el adapatador
     * @var array
     */
    protected $tiposComprobantes = array();
    /**
     * Indica si el adaptador permite refiscalizar
     */
    protected $permiteRefiscalizar = false;

    public function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        $this->_checkTipoCorrecto($comprobante);
    }

    protected function _checkTipoCorrecto(Rad_Db_Table_Row $comprobante)
    {
        if (!in_array($comprobante->TipoDeComprobante, $this->tiposComprobantes )) {
            throw new Facturacion_Fiscalizar_Adapter_Exception(
                'El adaptador de fiscalizacion no soporta el tipo de comprobante '.$comprobante->TipoDeComprobante
            );
        }
    }

//    public function abrirComprobante(Rad_Db_Table_Row $comprobante)
//    {
//        throw new Facturacion_Fiscalizar_Adapter_Exception('Este fiscalizador no implementa el metodo AbrirComprobante');
//    }
//
//    public function agregarItem(Rad_Db_Table_Row $item, Rad_Db_Table_Row $comprobante)
//    {
//        throw new Facturacion_Fiscalizar_Adapter_Exception('Este fiscalizador no implementa el metodo agregarItem');
//    }
//
//    public function anular(Rad_Db_Table_Row $comprobante)
//    {
//        throw new Facturacion_Fiscalizar_Adapter_Exception('Este fiscalizador no implementa el metodo anular');
//    }
//
//    public function agregarPago($desc, $monto, Rad_Db_Table_Row $comprobante, $tarjeta)
//    {
//        throw new Facturacion_Fiscalizar_Adapter_Exception('Este fiscalizador no implementa el metodo agregarPago');
//    }

    public function getPermiteRefiscalizar()
    {
        return $this->permiteRefiscalizar;
    }

    public function getRequiereImpresion()
    {
        return $this->requiereImpresion;
    }

    public function getGeneraNumero()
    {
        return $this->generaNumero;
    }
}
