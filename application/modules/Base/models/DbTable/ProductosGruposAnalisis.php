<?php

require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ProductosGruposAnalisis
 *
 * Productos
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ProductosGruposAnalisis
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ProductosGruposAnalisis extends Rad_Db_Table
{

    protected $_name = 'ProductosGruposAnalisis';
    protected $_sort = 'Productos.Descripcion';
    protected $_validators = array(
        'Descripcion' => array(
            array(
                'Db_NoRecordExists',
                'Productos',
                'Descripcion',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            )
        )
    );
    protected $_referenceMap = array(
        'ProductosCategorias' => array(
            'columns' => 'ProductoCategoria',
            'refTableClass' => 'Base_Model_DbTable_ProductosCategorias',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ProductosCategorias',
            'refColumns' => 'Id'
        ),
        'UnidadesDeMedidas' => array(
            'columns' => 'UnidadDeMedida',
            'refTableClass' => 'Base_Model_DbTable_UnidadesDeMedidas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'UnidadesDeMedidas',
            'refColumns' => 'Id'
        ),
        'ProductosSubCategorias' => array(
            'columns' => 'ProductoSubCategoria',
            'refTableClass' => 'Base_Model_DbTable_ProductosSubCategorias',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ProductosSubCategorias',
            'refColumns' => 'Id'
        ),
        'AnalisisTiposModelos' => array(
            'columns' => 'AnalisisTipoModelo',
            'refTableClass' => 'Laboratorio_Model_DbTable_AnalisisTiposModelos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'AnalisisTiposModelos',
            'refColumns' => 'Id'
        )
    );
    protected $_dependentTables = array(
        'Base_Model_DbTable_Articulos',
        
    );

    public function fetchTieneFormula($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TieneFormula = 1";
        if (is_array($where)) {
            $where[] = $condicion;
        } else {
            $where = $where ? $where . ' and ' . $condicion : $condicion;
        }
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}