<?php
require_once('Rad/Db/Table.php');

/**
 * Ordenes de Pedidos Remitos
 *
 *
 * @class 		Facturacion_Model_DbTable_OrdenesDePedidosRemitos
 * @extends		Facturacion_Model_DbTable_ComprobantesRelacionados
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_OrdenesDePedidosRemitos extends Facturacion_Model_DbTable_ComprobantesRelacionados
{

	/**
     * Mapa de Referencias
     * @var array
     */
  protected $_referenceMap    = array(
	    'Remitos' => array(
            'columns'           => 'ComprobantePadre',
            'refTableClass'     => 'Almacenes_Model_DbTable_Remitos',
            'refTable'			=> 'Comprobantes',
            'refColumns'        => 'Id'
        ),
        'OrdenesDePedidos' => array(
            'columns'           => 'ComprobanteHijo',
            'refTableClass'     => 'Facturacion_Model_DbTable_OrdenesDePedidos',
            'refTable'			=> 'Comprobantes',
            'refColumns'        => 'Id'
        )
	);

    /**
     * Variables que modifican el comportamiento de la asociacion, se debe indicar cual es el modelo 
     * hijo y cual el modelo padre.
     * Se utilizan en las funciones: asociarComprobanteHijoConPadre y agregarComprobanteHijoAPadre
     */
	protected $_class_comprobantePadre = "Almacenes_Model_DbTable_RemitosArticulosDeSalidas";
	protected $_class_comprobanteHijo  = "Facturacion_Model_DbTable_OrdenesDePedidosArticulos";	
	
    /**
     * Inserta un registro y agrega lo disponible en Modelo Comprobante Detalle 
     * o en el caso que este cerrado el Remito asocia lo ingresado manualmente en 
     * el Remito a lo disponible en la Orden de Pedido
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        $this->_db->beginTransaction();
        try {

            if ($data['ComprobanteHijo'] && $data['ComprobantePadre']) {
                $idRel = parent::insert($data);
                $idHijo = $data['ComprobanteHijo'];
                $idPadre = $data['ComprobantePadre'];
            } else {
                throw new Rad_Db_Table_Exception('Faltan datos necesarios.');
            }

            $M_H = new Facturacion_Model_DbTable_OrdenesDePedidos(array(), false);
            $M_H->salirSi_noExiste($idHijo);
            $M_H->salirSi_noEstaCerrado($idHijo);

            $M_P = new Almacenes_Model_DbTable_Remitos(array(), false);
            if ($M_P->estaCerrado($idPadre)) {
                // El Comprobante Padre (Remito) esta cerrado por lo tanto esta asociando la Orden de Compra al Remito
                $this->asociarComprobanteHijoConPadre($idHijo, $idPadre);
            } else {
                // El Comprobante Padre (Remito) esta abierto por lo tanto esta agregando cosas de la Orden de Compra al Remito
                $this->agregarComprobanteHijoAPadre($idHijo, $idPadre);
            }
            $this->_db->commit();
            return $idRel;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


	
}

?>
