<?php

/**
 * Workflow Nodo Secuencia
 * Traer Detalles de un Comprobante
 *
 * @copyright SmartSoftware Argentina
 * @package Workflow
 * @subpackage Nodes
 * @author Martin Alejandro Santangelo
 */
class Workflow_Comprobante_TraerDetalles extends Rad_Workflow_Nodo_Iterador
{
	protected static $_tipoEntrada = 'Row\Comprobante';
	protected $_tipoSalida  = 'Row\ComprobanteDetalle';

	/** 
	 *	debe sobreescribirse en las clases hijas y retornar n datos del tipo tipoSalida
	 *  array(
	 *		data1,
	 *		data2,
	 *		data3
	 *	)
	 */
	final public function procesar($data)
	{
        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $rtn  = $M_CD->fetchAll("Comprobante = $data->Id");

		return $rtn;
	}
}