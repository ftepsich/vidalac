<?php
/**
 * Produccion_Model_OrdenesDeProduccionesMapper
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_OrdenesDeProduccionesMapper
 * @extends     Rad_Mapper
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_OrdenesDeProduccionesMapper extends Rad_Mapper
{
    protected $_class = 'Produccion_Model_DbTable_OrdenesDeProducciones';
    
    /**
     * Cancela una orden de produccion
     * 
     * @param int $id
     */
    public function cancelar ($id)
    {
        $this->_model->cancelar($id);
    }
    
    /**
     * Cierra una orden de produccion
     * 
     * @param int $id
     */
    public function cerrar ($id)
    {
        $this->_model->cerrar($id);
    }
    
    /**
     * Recalcula los insumos necesarios
     * 
     * @param int $id
     */
    public function recacularInsumos ($id)
    {
        $db = $this->_model->getAdapter();
        $id = $db->quote($id, 'INTEGER');

        $orden = $this->_model->find($id)->current();

        if (!$orden) throw new Rad_Exception('No se encontro la Orden de Produccion');

        $this->_model->updateFormulaProducto($orden->Articulo, $orden->Cantidad, $id);
    }
    
    /**
     * Genera los pedidos de materiales para una orden
     * 
     * @param int $id
     * @param bool $recalcular
     */
    public function generarPedidoMateriales ($id, $recalcular = null)
    {	
        return $this->_model->generarPedidoMateriales($id, $recalcular);
    }
    
    /**
     * Setea el tipo de precio (?)
     * TODO: verrr q joraca es esto
     * 
     * @param int $tipo 
     */
    public function setTipoDePrecio ($tipo)
    {
        if (!is_int($tipo) || $tipo < 1 || $tipo > 4) {
            throw new Rad_Exception('El tipo de precio debe ser un entero entre 1 y 4');
        }
        
        $session = new Zend_Session_Namespace('OrdenesDeProducciones');

        $session->TipoDePrecio = $tipo;
    }
    
    /**
     * Devuelve la cantidad de producto que contiene por unidad un articulo
     * 
     * @param int $id
     * @return float 
     */
    public function getCantidadProductoArticulo ($id)
    {
        $db = $this->_model->getAdapter();
        
        $modelArticulo = new Base_Model_DbTable_Articulos();        
        $articulo      = $modelArticulo->find($db->quote($id, 'INTEGER'))->current();
        if(!$articulo)
            new Rad_Exception('No se encontro el articulo');
        
        return $modelArticulo->getCantidadProductoArticulo($articulo);
    }
    
    /**
     * Mueve efecticamente todos los Mmis de una orden de produccion y sus
     * detalles al interdeposito asociado a la linea de produccion
     * 
     * @param int $ODP
     */
    public function moverOrdenDeProduccionAInterdeposito ($ODP)
    {
        $this->_model->moverOrdenDeProduccionAInterdeposito($ODP);
    }
    
    /**
     * Asigna temporalmente en una variable de sesion los Mmis asignados a una
     * orden de produccion y sus detalles
     * 
     * @param int $ODP
     * @param int $ODPDetalle
     * @param array $idMmis
     */
    public function asignarOrdenDeProduccionDetalleMmi_Temporal ($ODP, $ODPDetalle, $idMmis)
    {
        $this->_model->asignarOrdenDeProduccionDetalleMmi_Temporal($ODP, $ODPDetalle, $idMmis);
    }
    
    /**
     * Desasigna Mmis de un detalle de una orden de produccion (variable de sesion)
     * 
     * @param int $ODP
     * @param int $ODPDetalle
     * @param array $idMmis
     */
    public function desasignarOrdenDeProduccionDetalleMmi_Temporal ($ODP, $ODPDetalle, $idMmis)
    {
        $this->_model->desasignarOrdenDeProduccionDetalleMmi_Temporal($ODP, $ODPDetalle, $idMmis);
    }
    
    
}