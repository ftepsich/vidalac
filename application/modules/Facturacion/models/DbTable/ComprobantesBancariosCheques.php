<?php
require_once 'Rad/Db/Table.php';

/**
 * Comprobantes Bancarios Cheques
 * 
 * @class Facturacion_Model_DbTable_ComprobantesBancariosCheques
 * @extends Facturacion_Model_DbTable_ComprobantesCheques
 * @package Aplicacion
 * @subpackage Facturacion
 *
 */
class Facturacion_Model_DbTable_ComprobantesBancariosCheques extends Facturacion_Model_DbTable_ComprobantesCheques
{
    public function init() {
        $this->_referenceMap['Comprobantes']['refTableClass'] = 'Facturacion_Model_DbTable_ComprobantesBancarios';
        parent::init();
    }
}