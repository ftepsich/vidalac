<?php

require_once('Rad/Db/Table.php');

/**
 *
 * Base_Model_DbTable_UnidadesDeMedidas_Exception
 *
 * Unidades De Medidas Exception
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_UnidadesDeMedidas_Exception
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_UnidadesDeMedidas_Exception extends Rad_Exception
{

}

/**
 *
 * Base_Model_DbTable_UnidadesDeMedidas
 *
 * Unidades De Medidas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_UnidadesDeMedidas
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_UnidadesDeMedidas extends Rad_Db_Table
{

    protected $_name = "UnidadesDeMedidas";
    protected $_sort = array('Descripcion asc');
    protected $_validators = array(
        'Descripcion' => array(
            array(
                'Db_NoRecordExists',
                'UnidadesDeMedidas',
                'Descripcion',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            )
        ),
        'DescripcionR' => array(
            array(
                'Db_NoRecordExists',
                'UnidadesDeMedidas',
                'DescripcionR',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            )
        )
    );

    protected $_referenceMap = array(
        'TipoDeUnidad' => array(
            'columns'        => 'TipoDeUnidad',
            'refTableClass'  => 'Base_Model_DbTable_TiposDeUnidades',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeUnidades',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        ),
        'AfipUnidadesDeMedidas' => array(
            'columns'        => 'Afip',
            'refTableClass'  => 'Afip_Model_DbTable_AfipUnidadesDeMedidas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'AfipUnidadesDeMedidas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        )
    );

    // fin Public Init -------------------------------------------------------------------------------------------
    public function fetchEsUnidadMinima($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "EsUnidadMinima = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    /**
     * @param $cantidad
     * @param $unidadOrigen
     * @param $unidadDestino
     * @param bool $conUm     Si es true le agrega la descripcion reducida de la unidad de medida
     * @return float|string
     * @throws Base_Model_DbTable_UnidadesDeMedidas_Exception
     */
    public function convert($cantidad, $unidadOrigen, $unidadDestino, $conUm=false)
    {
        if (!is_int($unidadOrigen)) {
            throw new Base_Model_DbTable_UnidadesDeMedidas_Exception('La unidad de origen debe ser un entero');
        }
        if (!is_int($unidadOrigen)) {
            throw new Base_Model_DbTable_UnidadesDeMedidas_Exception('La unidad de destino debe ser un entero');
        }
        if (!is_numeric($cantidad)) {
            throw new Base_Model_DbTable_UnidadesDeMedidas_Exception('La cantidad debe ser numerica');
        }

        $uO = $this->find($unidadOrigen)->current();
        $uD = $this->find($unidadDestino)->current();

        if (!$uO) {
            throw new Base_Model_DbTable_UnidadesDeMedidas_Exception('No se encontro la unidad de origen');
        }

        if (!$uD) {
            throw new Base_Model_DbTable_UnidadesDeMedidas_Exception('No se encontro la unidad de destino');
        }

        $cantidadConvertida = $cantidad * $uO->UnidadMinima / $uD->UnidadMinima;

        if ($conUm) {
            $cantidadConvertida .= ' '.$uD->DescripcionR;
        }
        return $cantidadConvertida;
    }
}