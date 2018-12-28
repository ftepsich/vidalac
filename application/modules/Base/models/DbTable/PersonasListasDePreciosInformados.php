<?php
require_once 'Rad/Db/Table.php';

/**
 * Model_DbTable_PersonasListasDePrecios
 *
 * Listas de Precios de los Proveedores
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Model_DbTable_PersonasListasDePrecios
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasListasDePreciosInformados extends Rad_Db_Table
{

    protected $_name = 'PersonasListasDePreciosInformados';
    protected $_sort = array('FechaInforme desc', 'PorCantidad asc', 'Persona asc');

    public function init()
    {
        $this->_defaultSource = self::DEFAULT_CLASS;
        $this->_defaultValues = array(
            'Divisa' => '1',
            'FechaInforme' => date('Y-m-d'),
            'Activo' => '1'
        );
        parent::init();
    }

    protected $_referenceMap = array(
         'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_Articulos',
            'refJoinColumns'    => array("Descripcion","Codigo"),                   
            'comboBox'          => true,                         
            'comboSource'       => 'datagateway/combolist/fetch/EsArticuloParaCompra',
            'refTable'          => 'Productos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns'    => array("RazonSocial"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        ),
        'TiposDeDivisas' => array(
            'columns'           => 'Divisa',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDivisas',
            'refColumns'        => 'Id'
        )
    );

   public function fetchOrdenArticulo($where = null, $order = null, $count = null, $offset = null)
    {
        $order2 = array("Articulo asc", "PorCantidad Asc");
        $order[] = $order2;
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}