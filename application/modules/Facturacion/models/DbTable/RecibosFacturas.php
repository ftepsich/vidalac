<?php

require_once 'Rad/Db/Table.php';

/**
 * @class 		Facturacion_Model_DbTable_RecibosFacturas
 * @extends		Facturacion_Model_DbTable_ComprobantesRelacionados
 *
 *
 * Facturas Compras Remitos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * 		Id					-> Identificador Unico
 * 		ComprobantePadre	-> Identificador del Recibo
 * 		ComprobanteHijo		-> Identificador del Comprobente que se esta pagando (FC, NC, ND)
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_RecibosFacturas extends Facturacion_Model_DbTable_ComprobantesPagosFacturas {

    /**
     * Mapa de Referencias
     * @var array
     */
    protected $_referenceMap = array(
        'ComprobantesCobrados' => array(
            'columns'       => 'ComprobanteHijo',
            'refTableClass' => 'Facturacion_Model_DbTable_Facturas',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        ),
        'Recibos' => array(
            'columns'       => 'ComprobantePadre',
            'refTableClass' => 'Facturacion_Model_DbTable_Recibos',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        )
    );

    /**
     * Solo controla que no sea una factura contado antes de seguir con el insert del padre
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        $this->_db->beginTransaction();
        try {

            $idHijo     = $data['ComprobanteHijo'];

            if ($idHijo) {

                $M_H = new Facturacion_Model_DbTable_Comprobantes();
                $M_H->salirSi_esContado($idHijo);
                $id = parent::insert($data);

            } else {
                throw new Rad_Db_Table_Exception('Faltan datos necesarios.');
            }

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


}
