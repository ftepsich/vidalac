<?php
require_once 'Adapter.php';

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
class Facturacion_Model_Fiscalizar_Preimpreso extends Facturacion_Fiscalizar_Adapter_Abstract
{
    protected $requiereImpresion   = false;
    protected $permiteRefiscalizar = true;
    protected $generaNumero        = false;
    protected $tiposComprobantes   = array(
        15,
        16,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        29,
        30,
        31,
        32,
        37,
        38,
        39,
        40,
        45
    );

    protected function _getPrintTemplate($comprobante)
    {
        $cfg = Rad_Cfg::get();
        $prop = ($comprobante->TipoDeComprobante);
        $template = $cfg->Facturacion->Preimpreso->Reporte->$prop;

        if (!$template) throw new Facturacion_Fiscalizar_Adapter_Exception('Este comprobante no tiene Template de impresion Configurado');

        return $template;
    }

    protected function _getPrinter($comprobante)
    {
        $cfg  = Rad_Cfg::get();
        $prop = ($comprobante->TipoDeComprobante);
        $printer = $cfg->Facturacion->Preimpreso->Printer->$prop ?$cfg->Facturacion->Preimpreso->Printer->$prop: $cfg->Facturacion->Preimpreso->CommonPrinter;

        if (!$printer) {
            throw new Facturacion_Fiscalizar_Adapter_Exception('Este comprobante no tiene Impresora Configurada y no existe una impresora por defecto');
        }
        return $printer;
    }

    protected function _getCopies($comprobante)
    {
        $cfg = Rad_Cfg::get();
        $prop = ($comprobante->TipoDeComprobante);
        return ($cfg->Facturacion->Preimpreso->Copias->$prop) ? $cfg->Facturacion->Preimpreso->Copias->$prop: 1;
    }

    public function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        // hace las verifacaciones pertinentes
        parent::fiscalizar($comprobante);

        require_once 'PrintIpp/CupsPrintIPP.php';

        $cfg = Rad_Cfg::get();
        try {
            // Generamos el PDF
            $report = new Rad_BirtEngine();
            $report->setParameter('Id', $comprobante->Id, 'Int');

            $template = $this->_getPrintTemplate($comprobante);


            $report->renderFromFile(APPLICATION_PATH."/../birt/Reports/$template.rptdesign", 'pdf');


            $ipp = new CupsPrintIPP();
            $ipp->with_exceptions = true;

            $ipp->handle_http_exceptions = true;
            $ipp->setHost($cfg->Facturacion->Preimpreso->Cups);
            $printer = $this->_getPrinter($comprobante);
            $ipp->setPrinterURI($printer);
            $ipp->setCopies($this->_getCopies($comprobante));
            $ipp->setDocumentName("Comprobante Numero: $comprobante->Punto-$comprobante->Numero");

            $ipp->setData($report->getStream()); // le mandamos el pdf

            //Solo imprimo si esta en produccion
            if (APPLICATION_ENV == 'production') {
                $status = $ipp->printStreamJob();

                if ($ipp->status[0] != 'successfull-ok') {
                    throw new Facturacion_Fiscalizar_Adapter_Exception('Error al imprimir Factura.');
                }
            }
        } catch (Exception $e) {
            //Rad_Log::err($e->getMessage());
            throw new Facturacion_Fiscalizar_Adapter_Exception('Error al imprimir Factura:<br>'.$e->getMessage());
        }
    }
}