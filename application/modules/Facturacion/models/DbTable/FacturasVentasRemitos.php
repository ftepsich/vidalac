<?php

require_once 'Rad/Db/Table.php';

/**
 * @class 		Facturacion_Model_DbTable_FacturasVentasRemitos
 * @extends		Facturacion_Model_DbTable_ComprobantesRelacionados
 *
 *
 * Facturas Compras Remitos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * 		Id					-> Identificador Unico
 * 		ComprobantePadre	-> Identificador de Factura Compra
 * 		ComprobanteHijo		-> Identificador de Remito
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_FacturasVentasRemitos extends Facturacion_Model_DbTable_ComprobantesRelacionados {

    /**
     * Mapa de Referencias
     * @var array
     */
    protected $_referenceMap = array(
        'Remitos' => array(
            'columns'       => 'ComprobanteHijo',
            'refTableClass' => 'Almacenes_Model_DbTable_RemitosDeSalidas',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        ),
        'FacturasCompras' => array(
            'columns'       => 'ComprobantePadre',
            'refTableClass' => 'Facturacion_Model_DbTable_FacturasVentas',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        )
    );
	
    /**
     * Variables que modifican el comportamiento de la asociacion, se debe indicar cual es el modelo 
     * hijo y cual el modelo padre.
     * Se utilizan en las funciones: asociarComprobanteHijoConPadre y agregarComprobanteHijoAPadre
     */
	protected $_class_comprobantePadre = "Facturacion_Model_DbTable_FacturasVentasArticulos";
	protected $_class_comprobanteHijo  = "Almacenes_Model_DbTable_RemitosArticulosDeSalidas";	

    /**
     * Inserta un registro y agrega lo disponible en M_CD o en el caso que este cerrada la factura
     * asocia lo ingresado manualmente en la F al Remito seleccionado
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        $this->_db->beginTransaction();
        try {

            if ($data['ComprobanteHijo'] && $data['ComprobantePadre']) {
                $id = parent::insert($data);
                $idRemito = $data['ComprobanteHijo'];
                $idFactura = $data['ComprobantePadre'];
            } else {
                throw new Rad_Db_Table_Exception('Faltan datos necesarios.');
            }

            $M_R = new Almacenes_Model_DbTable_Remitos();
            $M_R->salirSi_noExiste($idRemito);
            $M_R->salirSi_noEstaCerrado($idRemito);

            $M_FV = new Facturacion_Model_DbTable_FacturasVentas();
            if ($M_FV->estaCerrado($idFactura)) {
                // La Factura esta cerrada por lo tanto esta asociando la fac a un Remito
                $this->asociarComprobanteHijoConPadre($idRemito, $idFactura);
            } else {
                // La Factura esta abierta por lo tanto esta agregando cosas a una factura
                $this->agregarComprobanteHijoAPadre($idRemito, $idFactura);
            }

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}
