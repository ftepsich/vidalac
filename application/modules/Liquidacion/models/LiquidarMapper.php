<?php
/**
 */
class Liquidacion_Model_LiquidarMapper extends Rad_Mapper
{
    protected $_class = 'Liquidacion_Model_Liquidar';

    /**
     * Variables utilizadas en caso de usar la instancia como
     */
    protected $_idPeriodo;
    protected $_idLiquidacion;
    protected $_tipo;
    protected $_agrupacion;
    protected $_valor;


    protected function _liquidar ($idPeriodo, $tipo, $agrupacion = null, $valor = null)
    {
        $this->_model->liquidar($idPeriodo, $tipo, $agrupacion, $valor);
    }


    public function liquidar($periodo, $tipo, $empresa)
    {
        $this->setLiquidarJob($periodo, $tipo, $empresa);
        return Rad_Jobs::execute($this);
    }

    public function reliquidar($liquidacion, $periodo, $tipo, $agrupacion = null, $valor = null)
    {
        $this->setReLiquidarJob($liquidacion, $periodo, $tipo, $agrupacion, $valor);
        return Rad_Jobs::execute($this);
    }

    public function setReLiquidarJob($idliq, $idPeriodo, $tipo, $agrupacion = null, $valor = null)
    {
        $this->_idLiquidacion = $idliq;
        $this->_agrupacion = $agrupacion;
        $this->setLiquidarJob($idPeriodo, $tipo, $valor);
    }

    public function setLiquidarJob($idPeriodo, $tipo, $valor = null)
    {
        $this->_idPeriodo  = $idPeriodo;
        $this->_tipo       = $tipo;
        $this->_valor      = $valor;
    }

    public function perform()
    {
        if (!$this->_idLiquidacion) {
            $this->_model->liquidar($this->_idPeriodo, $this->_tipo, $this->_valor);
        } else {
            $this->_model->reliquidar($this->_idLiquidacion, $this->_idPeriodo, $this->_tipo, $this->_agrupacion, $this->_valor);
        }

    }
}