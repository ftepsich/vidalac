<?php
/**
 * Este modelo almacena las respuestas del web services para facturacion
 * electronica de la afip
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 * @class 	Facturacion_Model_DbTable_FacturacionElectronicaAfip
 * @extends	Rad_Db_Table
 */
class Facturacion_Model_DbTable_FacturacionElectronicaAfip extends Rad_Db_Table
{
    protected $_name = 'FacturacionElectronicaAfip';

    protected $_referenceMap = array(
        'Comprobantes' => array(
            'columns' => 'Comprobantes',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refTable' => 'ConceptosImpositivos',
            'refColumns' => 'Id',
        )
    );
	
}