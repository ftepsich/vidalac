<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_AfipGananciasEscalasPeriodos 
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipGananciasEscalasPeriodos extends Rad_Db_Table
{
    protected $_name = 'AfipGananciasEscalasPeriodos';

    protected $_sort = array('FechaDesde desc');

    protected $_referenceMap = array();

    protected $_dependentTables = array('Afip_Model_DbTable_AfipGananciasEscalas');

    public function getPeiodoId($fecha) {
        
    	$where 	= "FechaDesde >= '$fecha' and ifnull(FechaHasta,'2199-01-01') <= '$fecha'";
    	$r 		= $this->fetchRow($where);
    	if ($r) return $r['Id'];
    	return null;
        
    }



}