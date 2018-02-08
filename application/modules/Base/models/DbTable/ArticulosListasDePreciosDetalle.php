<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ArticulosListasDePreciosDetalle
 *
 * Detalle de las Listas de Precios de Articulos *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ProductosListasDePreciosDetalle
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ArticulosListasDePreciosDetalle extends Rad_Db_Table
{

    protected $_name = 'ArticulosListasDePreciosDetalle';
    protected $_sort = array('Articulo_cdisplay asc');

    protected $_referenceMap = array(
        'ArticulosListasDePrecios' => array(
            'columns'           => 'ListaDePrecio',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosListasDePrecios',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosListasDePrecios',
            'refColumns'        => 'Id'
        ),
        'TiposDeDivisas' => array(
            'columns'           => 'Divisa',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDivisas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_Articulos',
            'refJoinColumns'    => array("Descripcion", "Codigo"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/EsArticuloParaVenta',
            'refTable'          => 'Articulos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    public function init()
    {
        $this->_validators = array(
            'Articulo' => array(
                array(
                    'Db_NoRecordExists',
                    'ArticulosListasDePreciosDetalle',
                    'Articulo',
                    'ListaDePrecio = {ListaDePrecio} AND Id <> {Id}'
                ),
                'messages' => array('El articulo ya existe en la Lista de Precio')
            )
        );

        parent::init();
    }

}