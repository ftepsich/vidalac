<?php
require_once 'Rad/Db/Table.php';

/**
 * Comprobantes Cheques
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 *      Id          -> Identificador Unico
 *      Comprobante -> Identificador del Comprobante
 *      Cheque      -> Identificador del Cheque
 *
 * @class Facturacion_Model_DbTable_ComprobantesCheques
 * @extends Rad_Db_Table
 * @package Aplicacion
 * @subpackage Facturacion
 *
 */
class Facturacion_Model_DbTable_ComprobantesCheques extends Rad_Db_Table
{
    protected $_name = "ComprobantesCheques";

    protected $_referenceMap = array(
        'Cheques' => array(
            'columns'       => 'Cheque',
            'refTableClass' => 'Base_Model_DbTable_Cheques',
            'refTable'      => 'Cheques',
            'refColumns'    => 'Id'
        ),
        'Comprobantes' => array(
            'columns'       => 'Comprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refTable'      => 'Comprobantes',
            'refColumns'    => 'Id'
        )
    );

    /**
     * elimina los cheques hijos del comprobante indicado
     *
     * @param int $idComprobante    identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarRelacionesHijosCheques($row)
    {
        $this->delete('Comprobante = '.$row->Id);

    }

}