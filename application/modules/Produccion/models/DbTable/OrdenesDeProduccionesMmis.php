<?php

/**
 * Produccion_Model_DbTable_OrdenesDeProduccionesMmis
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_OrdenesDeProduccionesMmis
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_OrdenesDeProduccionesMmis extends Rad_Db_Table
{
    protected $_name = 'OrdenesDeProduccionesMmis';

    protected $_gridGroupField = 'MmisArticulosArticuloDescripcion';

    protected $_referenceMap    = array(
        'Mmis' => array(
            'columns'        => 'Mmi',
            'refTableClass'  => 'Almacenes_Model_DbTable_Mmis',
            'refJoinColumns' => array('Identificador','UnidadDeMedida','CantidadActual','Articulo','ArticuloVersion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Mmis',
            'refColumns'     => 'Id',
        ),
	    'OrdenesDeProduccionesDetalles' => array(
            'columns'        => 'OrdenDeProduccionDetalle',
            'refTableClass'  => 'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles',
            'refJoinColumns' => array('OrdenDeProduccion','Cantidad'),
            'comboBox'       => false,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'OrdenesDeProduccionesDetalles',
            'refColumns'     => 'Id',
        )
    );

    protected $_dependentTables = array();

    public function __destruct()
    {
        // antes de morir borra el cache de la instancia cacheada de articulos
        if ($this->ma) $this->ma->clean();
    }

    public function getCantidadAsignada($ordenDeProduccionDetalle)
    {
        $select = $this->select();
        $select->from($this, array('sum(CantidadActual) as cantidad'));
        $select->where("OrdenDeProduccionDetalle = $ordenDeProduccionDetalle");

        $cantidad = $this->fetchRow($select);

        return $cantidad->cantidad;
    }

    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('Mmis')
              ->joinRef('Articulos',array('ArticuloDescripcion' => 'Descripcion'))
              ->joinRef('Lotes',array(
                    'Numero',
                    'FechaElaboracion',
                     'FechaVencimiento'
                ));

            // obtengo el objeto cacheado para no pedir lo mismo a la base n veces

            $this->ma = Service_TableManager::getCached('Base_Model_DbTable_Articulos');

            $ma = $this->ma;
            // calculados
            $this->addLocalCalculatedField(
                'CantidadProducto',
                function ($row) use ($ma){

                    $r = $ma->getEstructuraArbol($row->MmisArticuloVersion);

                    // tambien seteo los otros campos ya que tengo los valores aca
                    // asi no tengo q volver a llamar a getEstructuraArbol q es pesada
                    $row->setCalculatedField('UnidadDeMedidaProductoDescripcion', $r['productoUMR']);
                    $row->setCalculatedField('productoDescripcion', $r['productoDescripcion']);
                    $row->setCalculatedField('UnidadDeMedidaProducto', $r['productoUM']);
                    $row->setCalculatedField('TipoUnidadDeMedidaProducto', $r['productoTipoUM']);
                    // Rad_Log::debug($row->Id.' '.$row->getCalculatedField('UnidadDeMedidaProductoDescripcion'));
                    return $r['productoCantTotal'];
                }
            );

        }
    }
}
