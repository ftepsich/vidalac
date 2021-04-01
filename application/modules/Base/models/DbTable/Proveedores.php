<?php
require_once 'Personas.php';

/**
 * Base_Model_DbTable_Proveedores
 *
 * Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Model_DbTable_Proveedores
 * @extends Model_DbTable_Personas
 */
class Base_Model_DbTable_Proveedores extends Base_Model_DbTable_Personas
{
    protected $_name = 'Personas';
    protected $_sort = array('RazonSocial ASC');
    protected $_permanentValues = array('EsProveedor' => 1);


    public function init ()
    {
        parent::init();
        $this->_calculatedFields['IBProximosVencimientosCM05'] = "(SELECT COUNT(Id) FROM personasingresosbrutos WHERE Persona = Personas.Id AND FechaVencimientoCM05 IS NOT NULL AND FechaVencimientoCM05 < DATE_ADD(CURDATE(), INTERVAL 10 DAY) )";
        $this->_calculatedFields['IGProximosVencimientosCertificados'] = "(SELECT COUNT(Id) FROM personasretencionesganancias WHERE Persona = Personas.Id AND FechaVencimientoCertificadoDeExclusion IS NOT NULL AND FechaVencimientoCertificadoDeExclusion < CURDATE() )";
    }

    public function fetchTransporte($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'EsTransporte = 1';     
        $where = $this->_addCondition($where, $condicion);    
        return parent::fetchAll($where, $order, $count, $offset);
    }

}
