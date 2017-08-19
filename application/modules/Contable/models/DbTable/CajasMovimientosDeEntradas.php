<?php

/**
 * Contable_DbTable_CajasMovimientosDeEntradas
 *
 * Detalle de los movimientos de las Cajas
 *
 * @copyright SmartSoftware Argentina
 * @class Contable_DbTable_CajasMovimientos
 * @extends Rad_Db_Table
 * @package Aplicacion
 * @subpackage Contable
 */
class Contable_Model_DbTable_CajasMovimientosDeEntradas extends Contable_Model_DbTable_CajasMovimientos
{
    protected $_permanentValues = array(
        'TipoDeMovimiento'  => 3,
        'Descripcion'       => 'Ingreso de dinero'
    );
    
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();
            
            if ($data['Monto']<0) {
                throw new Rad_Db_Table_Exception("El monto debe ser mayor a 0 (cero).");
            } 
            
            $id = parent::insert($data);

            $this->_db->commit();
            
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    } 
    
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "CajasMovimientos.Monto >= 0";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }     
    
}
