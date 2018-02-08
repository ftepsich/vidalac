<?php
require_once 'Adapter.php';

require_once 'Fiscal/HasarComm.php';
require_once 'Fiscal/HasarFiscal.php';

class FiscalException extends Exception
{}

/**
 * Simplemente levanta una excepcion si hay un error en la imp fiscal
 */
class FiscalHasarWExcep extends HasarFiscal
{
    public function sendCommand(array $cmd)
    {

        $r = parent::sendCommand($cmd);
        if ($err = $this->isStatusError()) {
            throw new FiscalException($err.': '.print_r($this->getStatus(),true));
        }
        return $r;
    }
}

/**
 * Model_Fiscalizar_Preimpreso
 *
 * Adaptador para fiscalizar comprobantes usando facturas con números pre impresos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martin Alejandro Santangelo
 */
class Facturacion_Model_Fiscalizar_ImpresoraFiscal extends Facturacion_Fiscalizar_Adapter_Abstract
{
    protected $requiereImpresion   = false;
    protected $permiteRefiscalizar = false;
    protected $generaNumero        = true;
    protected $tiposComprobantes   = array(54, 56, 29, 30);

    /**
     * retorna la direccion fiscal o la primera direccion de no encontrarla.
     *
     * @param  Rad_Db_Table_Row $persona registro persona
     * @return string
     */
    protected function _getDireccion($persona)
    {
        $direcciones = $persona->findDependentRowset('Base_Model_DbTable_Direcciones');

        foreach ($direcciones as $key => $dir) {
            if ($dir->TipoDeDireccion == 1) {
                return $dir->Direccion;
            }
        }
        if (count($direcciones) > 0) return @$direcciones[0]->Direccion;
        return '';
    }

    protected function _getTipoRespIva($i)
    {
        switch ($i) {
            case '1':
               return 'B'; // Resp no inscripto
            case '2':
               return 'T'; // No categorizado
            case '3':
               return 'I'; // Responsable inscripto
            case '4':
               return 'M'; // Resp. monotributo
            case '5':
               return 'E'; // Exento
            case '6':
               return 'C'; // Consumidor final
            case '7':
               return 'A'; // No responsable

            default:
                throw new Facturacion_Fiscalizar_Adapter_Exception("Modalidad de Iva del cliente no soportada por el controlador fiscal ($i)");
                break;
        }
    }

    protected function _getTipoDoc($n)
    {
        switch ($n) {
            case '1':
                return 'C';
            case '3':
                return 'L';
            case '7':
                return '0';
            case '9':
                return '1';
            case '19':
                return '2';
            case '21':
                return '3';
            case '5':
                return '4';
            default:
                return ' ';
        }
    }

    public function _getPunto($comprobante)
    {
        $punto = $comprobante->findParentRow('Base_Model_DbTable_PuntosDeVentas');

        if (!$punto) throw new Facturacion_Fiscalizar_Adapter_Exception('No se encontro el registro del punto de venta');

        return $punto->Numero;
    }

    protected function _createFiscal($comprobante)
    {
        if ($this->fiscal) return;
        $punto = $this->_getPunto($comprobante);
        $cfg = Rad_Cfg::get('/configs/fiscal.yml');

        $conf = $cfg->puntodeventa->$punto;

        if (empty($conf)) throw new Facturacion_Fiscalizar_Adapter_Exception('No esta configurado el punto de venta en fiscal.yml');

        $this->fiscal = new FiscalHasarWExcep(new HasarComm($conf->Ip, $conf->Port));
    }

    public function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        Rad_Log::debug('fiscalizando imp fiscal');

        $this->_checkTipoCorrecto($comprobante);
        $this->_createFiscal($comprobante);

        $this->abrirComprobante($comprobante);

        $modelFacturas  = $comprobante->getTable();
        $modelArticulos = new Facturacion_Model_DbTable_TicketFacturasArticulos(array(), true);

        $items = $modelArticulos->fetchAll("Comprobante = $comprobante->Id");

        foreach ($items as $item) {
            $this->agregarItem($item, $comprobante);
        }

        $idRecibo = $modelFacturas->getIdComprobantePago($comprobante->Id);

        if ($idRecibo) {
            $modelPagos = new Facturacion_Model_DbTable_RecibosFicticiosDetalles;
            $pagos = $modelPagos->fetchAll("Comprobante = $idRecibo");
            foreach ($pagos as $pago) {
                // es efectivo
                if($pago->Caja)
                    $this->agregarPago('Efectivo', $pago->PrecioUnitario, $comprobante);
                // es tarjeta
                if($pago->TarjetaDeCreditoCupon)
                    $this->agregarPago('Tarjeta', $pago->PrecioUnitario, $comprobante);
            }
        }

        if ($comprobante->TipoDeComprobante < 32) {
            $this->fiscal->cerrarComprobanteNoFiscal();
        } else {
            $this->fiscal->cerrarComprobante();
        }
    }

    protected function _verificarComprobanteAbierto()
    {

        $this->fiscal->estado();
        $s = $this->fiscal->getStatus();
        if (isset($s['fiscal'][13])) {
            if (Rad_Confirm::confirm( "Imp. Fiscal: Comprobante ya abierto, quiere cancelarlo", _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
                $this->fiscal->cancelar();
            }
        }

    }

    protected function abrirComprobante(Rad_Db_Table_Row $comprobante)
    {
        $this->_createFiscal($comprobante);

        $this->_verificarComprobanteAbierto();

        switch ($comprobante->TipoDeComprobante) {
            case 54:;
                $tipo = 'B';
            break;
            case 56:
                $tipo = 'A';
            break;

            case 29:
                $tipo = 'R';
            break;
            case 30:
            case 31:
                $tipo = 'S';
            break;
            default:
                throw new Facturacion_Fiscalizar_Adapter_Exception("Tipo de comprobante $comprobante->TipoDeComprobante no soportado");
        }

        // si no es un consumidor final generico imprimo los datos
        if ($comprobante->Persona != 1) {
            $persona = $comprobante->findParentRow('Base_Model_DbTable_Personas');

            $iva     = $this->_getTipoRespIva($persona->ModalidadIva);
            $tipoDoc = $this->_getTipoDoc($persona->TipoDeDocumento);

            $numDoc  = ($tipoDoc != 'C' && $tipoDoc != 'L')?$persona->Dni: str_replace('-', '',$persona->Cuit);

            // no hay numero de doc o cuit
            if (!$numDoc) throw new Facturacion_Fiscalizar_Adapter_Exception('El número de DNI o CUIT del cliente no estan cargados correctamente');

            $dir = $this->_getDireccion($persona);


            $this->fiscal->setComprador($persona->RazonSocial,$numDoc, $iva, $tipoDoc,  $dir);
        }

        if (in_array($comprobante->TipoDeComprobante, array(29,30,31,32))) {
            if (!$comprobante->ComprobanteRelacionado) {
                throw new Facturacion_Fiscalizar_Adapter_Exception('No selecciono la factura relacionada a la nota de credito');                
            }
            $numero = $comprobante->getTable()->recuperarDescripcionComprobante($comprobante->ComprobanteRelacionado);
            $this->fiscal->setCompOriginal($numero);

            $ret = $this->fiscal->abrirComprobanteNoFiscal($tipo, $comprobante->Id);
        } else {
            $ret = $this->fiscal->abrirComprobante($tipo, $iva, $tipoDoc);
        }

        if ($ret[2]) {
            $comprobante->getTable()->setNumeroFactura_Fiscalizador($ret[2], $comprobante->Id);
        } else {
            throw new Facturacion_Fiscalizar_Adapter_Exception('La impresara fiscal no retorno número de comprobante');
        }

        return $ret[2];
    }

    public function anular(Rad_Db_Table_Row $comprobante)
    {
        $this->_checkTipoCorrecto($comprobante);
        $this->_createFiscal($comprobante);
        $this->fiscal->cancelar($comprobante);
    }

    protected function agregarItem(Rad_Db_Table_Row $item, Rad_Db_Table_Row $comprobante)
    {
        //$this->_checkTipoCorrecto($comprobante);
//        $this->_createFiscal($comprobante);

        $iva = $item->findParentRow('Base_Model_DbTable_ConceptosImpositivos');

        $tc = $comprobante->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        $this->fiscal->addItem($item->ArticulosDescArreglada, $item->Cantidad, $item->PrecioUnitario, $iva->PorcentajeActual, !$tc->DiscriminaImpuesto);
    }

    public function agregarPago($desc, $monto, Rad_Db_Table_Row $comprobante, $tarjeta = '')
    {
        $this->_checkTipoCorrecto($comprobante);
        $this->_createFiscal($comprobante);

        $this->fiscal->pago($monto, $desc, $tarjeta);
    }
}