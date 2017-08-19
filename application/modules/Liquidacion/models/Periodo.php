<?php
/**
 * Liquidacion_Model_Periodo
 *
 * Periodo de Liquidacion
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Periodo extends Rad_Util_RangoFechas
{
	protected $_id;
    protected $_tipo;
    protected $_anio;
    protected $_valor;
    protected $_descripcion;

	public function __construct($desde, $hasta, $id)
    {
    	//$id = $this->_db->quote($id, 'INTEGER');

        $M = new Liquidacion_Model_DbTable_LiquidacionesPeriodos;
        $R = $M->find($id)->current();
        if (!$R) throw new Rad_Exception("No existe el periodo de Liquidacion Solicitado al crear el objeto Periodo.");

        $this->_id          = $id;
        $this->_tipo        = $R->TipoDeLiquidacionPeriodo;
        $this->_anio        = $R->Anio;
        $this->_valor       = $R->Valor;
        $this->_descripcion = $R->Descripcion;

    	parent::__construct($desde, $hasta);
    }

    public function getId()
    {
    	return $this->_id;
    }

    public function getTipo()
    {
        return $this->_tipo;
    }

    public function getAnio()
    {
        return $this->_anio;
    }

    public function getValor()
    {
        return $this->_valor;
    }

    public function getDescripcion()
    {
        return $this->_descripcion;
    }

    public function getMes() {
        // TODO: Ojo ... esto debe devolver el mes dependiendo el tipo de periodos (quincenal, semenal, mensual, etc)
        switch ($this->_tipo) {
            default:
                return $this->_valor;
                break;
        }
    }

    /**
     * Devuelve el semestre en que estamos
     * 1 para el 1er y 2 para el segundo
    */
    public function getSemestre() {
        return ($this->getMes() > 6) ? 2 : 1;
    }

    /**
     * Devuelve el dia de inicio del semestre
    */
    public function getFechaInicioSemestre($formato) {

        $formato    = ($formato) ? $formato : 'Y-m-d';
        $anio       = $this->getAnio();
        $f          = new DateTime( ($this->getMes() > 6) ? "$anio-07-01": "$anio-01-01");
        return $f->format($formato);
    }

    /**
     * Devuelve el dia fin del semestre
    */
    public function getFechaFinSemestre($formato) {
        $formato    = ($formato) ? $formato : 'Y-m-d';
        $anio       = $this->getAnio();
        $f          = new DateTime( ($this->getMes() > 6) ? "$anio-12-31": "$anio-06-30");
        return $f->format($formato);
    }

    /**
     * Rad_Util_RangoFechas
     *
     *  getDias()
     *  getDiff()
     *  getDesde()
     *  getHasta()
     *
     */

}