<?php

class Liquidacion_Model_LiquidacionesRecibosMapper extends Rad_Mapper
{
    protected $_class = 'Liquidacion_Model_DbTable_LiquidacionesRecibos';

    /**
     * Retorna los dos Id de recibos a comparar para un ajuste dado
     *
     * @param  [int] $numAjuste
     * @param  [int] $sercivio
     * @param  [int] $periodo
     * @return [array]          array los dos Id de recibos a comparar para un ajuste dado
     */
    public function getIdRecibosAjuste($numAjuste, $servicio, $periodo)
    {
        return $this->_model->getIdRecibosAjuste($numAjuste, $servicio, $periodo);
    }
}
