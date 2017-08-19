<?php
class Rrhh_Model_DbTable_LiquidacionesTablasEscalares extends Rrhh_Model_DbTable_LiquidacionesTablas
{

    protected $_permanentValues = array(
        'TipoDeLiquidacionTabla' => 2
    );

    protected $_defaultValues = array(
        'TipoDeLiquidacionTabla' => 2,
        'Grupo' => null
    );

    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "LiquidacionesTablas.Grupo is null ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }      

}