<?php
/**
 * Rad_Util_RangoFechas
 *
 *
 * @package     Rad
 * @subpackage  Util
 * @class       Rad_Util_RangoFechas
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Rad_Util_RangoFechas
{
    protected $_desde;
    protected $_hasta;

    /**
     * @param DateTime|string
     * @param DateTime|string
     */
    public function __construct($desde, $hasta)
    {
        if ($desde instanceof DateTime) {
            $this->_desde = $desde;
        } else {
            $this->_desde = new DateTime($desde);
        }


        if ($hasta instanceof DateTime) {
            $this->_hasta = $hasta;
        } else {
            $this->_hasta = new DateTime($hasta);
        }
    }

    /**
     * Retorna la cantidad de dias del Rango
     */
    public function getDias()
    {
        return $this->_hasta->diff($this->_desde)->d;
    }

    public function getDiff()
    {
        return $this->_hasta->diff($this->_desde);
    }

    public function getDesde()
    {
        return $this->_desde;
    }

    public function getHasta()
    {
        return $this->_hasta;
    }
}