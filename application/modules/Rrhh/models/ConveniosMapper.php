<?php
/**
 * Rrhh_Model_ConveniosMapper
 *
 * @package     Aplicacion
 * @subpackage 	Rrhh
 * @class       Rrhh_Model_ConveniosMapper
 * @extends     Rad_Mapper
 * @copyright   SmartSoftware Argentina 2010
 */
class Rrhh_Model_ConveniosMapper extends Rad_Mapper
{
    protected $_class = 'Rrhh_Model_DbTable_Convenios';
     
    /**
     * Genera los detalles de todas las categorioas de un convenio con nuevo periodo y valores
     * 
     * @param date      $fecha
     * @param int       $idConvenio
     * @param decimal   $valorBasico
     * @param decimal   $valorNoRemunerativo
     * @param bool      $porcentaje
     */
    public function generarDetallesConvenio($fecha, $idConvenio, $valorBasico, $valorBasicoP, $valorNoRemunerativo, $valorNoRemunerativoP,$fechaDesde,$fechaHasta)
    {	
        return $this->_model->generarDetallesConvenio($fecha, $idConvenio, $valorBasico, $valorBasicoP, $valorNoRemunerativo, $valorNoRemunerativoP,$fechaDesde,$fechaHasta);
    }


    /**
     * Genera un convenio nuevo igual al seleccionado con las licencias y formulas salvo los detalles de categoria
     * 
     * @param int       $idConvenio
     * @param varchar   $nombre
     */
    public function generarClonConvenio($nombre, $idConvenio)
    {   
        return $this->_model->generarClonConvenio($nombre, $idConvenio);
    }

    /**
     * Busca la Situacion de revista asociada al Convenio Licencia
     * 
     * @param int      $convenioLicencia
     */
    public function getSituacionDeRevistaLicencia($convenioLicencia)
    {   
        return $this->_model->getSituacionDeRevistaLicencia($convenioLicencia);
    }    
    
    
}