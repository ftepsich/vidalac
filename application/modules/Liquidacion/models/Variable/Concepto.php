<?php

/**
 * Liquidacion_Model_Variable
 *
 * Representa a todas las variables del liquidador de sueldo
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Variable
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Variable_Concepto extends Liquidacion_Model_Variable
{
    protected $_postLiquidarConcepto;

    protected $_codigo;

    protected $_tipo;

    protected $_detalle;

    protected $_detalleResultado;

    protected $_descripcion;

    protected static $_postCalcularConcepto;

    protected static $_sumportipo = [];

    public function __construct($id, $nombre, Liquidacion_Model_Periodo $periodo, $codigo, $tipo, $tipoConcepto, $descripcion, $formula = null,  $selector = null, $detalle = null, $postLiqConcepto = null)
    {
        $this->_postLiquidarConcepto = $postLiqConcepto;
        $this->_codigo               = $codigo;
        $this->_tipo                 = $tipo; // tipo de concepto liquidacion
        $this->_tipoConcepto         = $tipoConcepto; // tipo de concepto liquidacion
        $this->_detalle              = $detalle;
        $this->_detalleResultado     = $detalle;
        $this->_descripcion          = $descripcion;

        parent::__construct($id, $nombre, $codigo, $descripcion, $periodo, $formula, $selector);
    }

    public static function initSum()
    {
        self::$_sumportipo = [];
    } 

    public static function setPostCalcular(Liquidacion_Model_Liquidador_IPostCalcularConcepto $p)
    {
        self::$_postCalcularConcepto = $p;
    }

    public function getCodigo()
    {
        return $this->_codigo;
    }

    public function getDetalle()
    {
        return $this->_detalle;
    }

    public function getResultadoDetalle()
    {
        return $this->_detalleResultado;
    }

    public function getTipo()
    {
        return $this->_tipo;
    }

    public function getDescripcion()
    {
        return $this->_descripcion;
    }

    public static function getSumTipo($tipo) {
        return self::$_sumportipo[(string)$tipo];
        // $r = self::$_sumportipo[(string)$tipo]; 
        // return ($r)? $r:0;        
    }

    protected function _calcular($evaluador, $formula)
    {
        // calculo la variable
        $v   = parent::_calcular($evaluador, $formula);
        $det = $this->getDetalle();

        self::$_sumportipo[(string)$this->_tipoConcepto] += $v;

        // calculo el detalle
        if ($det) {
            if (!is_numeric($det)) {
                $this->_detalleResultado = $evaluador->execute($det);
            } else {
                $this->_detalleResultado = $det;
            }
        }

        return $v;
    }

    // protected function _postCalcular($valor, $evaluador, $servicio)
    // {
    //     // Si hay seteado un post liquidar lo ejecutamos
    //     if (self::$_postCalcularConcepto) {
    //         self::$_postCalcularConcepto->calculado($valor, $servicio, $evaluador, $this, $this->_periodo);
    //     }
    //     parent::_postCalcular($valor, $evaluador, $servicio);
    // }
}
