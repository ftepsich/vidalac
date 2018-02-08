<?php
/**
 * Rrhh_Model_LiquidacionesTablasMapper
 *
 * @package     Aplicacion
 * @subpackage 	Rrhh
 * @class       Rrhh_Model_LiquidacionesTablasMapper
 * @extends     Rad_Mapper
 * @copyright   SmartSoftware Argentina 2010
 */
class Rrhh_Model_LiquidacionesTablasMapper extends Rad_Mapper
{
    protected $_class = 'Rrhh_Model_DbTable_LiquidacionesTablas';
     
    /**
     * Genera los detalles de una tabla con nuevo periodo y valores
     * 
     * @param date      $fecha
     * @param int       $idTabla
     * @param decimal   $valor
     * @param bool      $porcentaje
     */
    public function generarDetallesTablas($fecha, $idTabla, $valor, $porcentaje = false)
    {	
        return $this->_model->generarDetallesTablas($fecha, $idTabla, $valor, $porcentaje);
    }
    
    
}