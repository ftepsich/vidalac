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
class Facturacion_Model_Fiscalizar_Null extends Facturacion_Fiscalizar_Adapter_Abstract
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
        45,
        47,
        52,
        54,
        57
    );

    public function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        // hace las verifacaciones pertinentes
        parent::fiscalizar($comprobante);
    }
}
