<?php

/**
 * Produccion_Model_DbTable_ProduccionesMmis
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_ProduccionesMmis
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_ProduccionesMmis extends Rad_Db_Table
{
    protected $_name = 'ProduccionesMmis';
    
    protected $_gridGroupField = 'Produccion';
   
    protected $_sort = 'Id desc';
    
    protected $_referenceMap    = array(
        
        'Producciones' => array(
            'columns'        => 'Produccion',
            'refTableClass'  => 'Produccion_Model_DbTable_Producciones',
            'refJoinColumns' => array('Comienzo','Final','OrdenDeProduccion'),
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Producciones',
            'refColumns'     => 'Id',
        ),
        'Mmis' => array(
            'columns'        => 'Mmi',
            'refTableClass'  => 'Almacenes_Model_DbTable_Mmis',
            'refJoinColumns' => array('Identificador','CantidadOriginal','CantidadActual'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Mmis',
            'refColumns'     => 'Id',
        )    );

    protected $_dependentTables = array();

    
//    public function init()
//    {
//        parent::init();
//        $this->addAutoJoin(
//                'Articulos',
//                'Mmis.Articulo = Articulos.Id',
//                array(
//                    'ArticuloDescripcion' => 'Articulos.Descripcion'
//                )
//        );
//    }
}