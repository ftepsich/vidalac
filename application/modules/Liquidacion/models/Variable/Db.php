<?php

/**
 * Liquidacion_Model_Variable
 *
 * Variables que ejecutan sql para obtener la formula
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
abstract class Liquidacion_Model_Variable_Db extends Liquidacion_Model_Variable
{
    protected $_sql;
    protected $_db;

    public function __construct($nombre, Liquidacion_Model_Periodo $periodo, $sql, $db)
    {
        $this->_sql = $sql;
        $this->_db  = $db;

        parent::__construct(null, $nombre, '', '', $periodo);
    }

    protected function _prepareSQL($servicio)
    {
        $sql = $this->_sql;
        $sql = str_replace('{servicio}', $servicio->Id, $sql);
        $sql = str_replace('{año}', $this->periodo['año'], $sql);
        $sql = str_replace('{dia}', $this->periodo['dia'], $sql);
        $sql = str_replace('{mes}', $this->periodo['mes'], $sql);
    }

    public function getFormulaFromDb($servicio)
    {
        return $this->_db->fetchOne($this->_prepareSQL($servicio));
    }

    public function getFormula($servicio)
    {

        return $this->getFormulaFromDb($servicio);
    }
}