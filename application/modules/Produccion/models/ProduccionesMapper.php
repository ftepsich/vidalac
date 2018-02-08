<?php

/**
 * Produccion_Model_ProduccionesMapper
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_ProduccionesMapper
 * @extends     Rad_Mapper
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_ProduccionesMapper extends Rad_Mapper
{

    protected $_class = 'Produccion_Model_DbTable_Producciones';

    public function asociarEmpleados($idProduccion, $idEmpleado, $idActividad)
    {
        $this->_model->asociarEmpleados($idProduccion, $idEmpleado, $idActividad);
    }

    public function desasociarEmpleados($idProduccion, $idActividad)
    {
        $this->_model->desasociarEmpleados($idProduccion, $idActividad);
    }

    public function iniciarLineaDeTiempo($idOrdenDeProduccion)
    {
        return $this->_model->iniciarLineaDeTiempo($idOrdenDeProduccion);
    }
    
    public function iniciarProducccion($idProduccion) {
        
        $this->_model->iniciarProducccion($idProduccion);  
        
    }
    
    public function generarMmi($idProduccion,$cantidadarticulos,$cantidadporpalet, $tipoPalet) {
        
        $this->_model->generarMmiProduccion($idProduccion, $cantidadarticulos, $cantidadporpalet, $tipoPalet);
   
    }

    public function quitarTodaMercaderiaAMmi($idmmis, $idProduccion = null) {
       $this->_model->quitarTodaMercaderiaAMmi($idmmis, $idProduccion);
    }
    
    public function quitarMercaderiaAMmi($idmmi,$cantidad,$unidaddemedida = null, $idProduccion = null) {
       $this->_model->quitarMercaderiaAMmi($idmmi,$cantidad,$unidaddemedida, $idProduccion);
    }
    
    public function retornarMercaderiaAMmi($idmmi,$cantidad,$unidaddemedida = null, $idProduccion = null) {
       $this->_model->retornarMercaderiaAMmi($idmmi,$cantidad,$unidaddemedida, $idProduccion);
    }

    /** Detiene la produccion, cerrando la linea de tiempo actual
     * @param $id
     * @param $motivo
     * @param $comentario
     */
    public function detenerProduccion($id, $motivo, $comentario)
    {
        $this->_model->detenerProduccion($id, $motivo, $comentario);
    }
    
    /**
     * Finaliza la produccion, cerrando la linea de tiempo actual
     * @param int $id 
     */
    public function finalizarProduccion($id)
    {
        $this->_model->finalizarProduccion($id);
    }
    
    
    public function getTotalProducido($id) 
    {
        $M_OP = new Produccion_Model_DbTable_OrdenesDeProducciones();
        return $M_OP->devolverCantidadProducida($id);
    }
    
    /**
     * Clona los mismo empleados de la produccion anterior de la misma orden de produccion
     *
     * @param int $idProduccion	identificador de la Produccion
     *
     * @return Zend_Db_Table_Row
     */
    public function clonarEmpleados($idProduccion)
    {
        $this->_model->clonarEmpleados($idProduccion);
    }
}