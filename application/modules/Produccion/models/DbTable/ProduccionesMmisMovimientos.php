<?php
/**
 * Almacena los cambios realizados en las cantidades de los mmis dentro de la produccion
 */
class Produccion_Model_DbTable_ProduccionesMmisMovimientos extends Rad_Db_Table
{
    protected $_gridGroupField = 'Produccion';
    
    protected $_name = 'ProduccionesMmisMovimientos';
    
    protected $_sort = array('Fecha desc');

    protected $_referenceMap = array(
        
        'Mmis' => array(
            'columns'           => 'Mmi',
            'refTableClass'     => 'Almacenes_Model_DbTable_Mmis',
            'refJoinColumns'    => array('Identificador'), 
            'comboSource'	=> 'datagateway/combolist',
            'refTable'		=> 'OrdenesDeProduccionesMmis',
            'refColumns'        => 'Id',
        ),
        'Producciones' => array(
            'columns'           => 'Produccion',
            'refTableClass'     => 'Produccion_Model_DbTable_Producciones',
            'refJoinColumns'    => array('OrdenDeProduccion', 'Comienzo','Final'),
            'comboSource'	=> 'datagateway/combolist',
            'refTable'		=> 'Producciones',
            'refColumns'        => 'Id',
        )    
    );

    protected $_dependentTables = array();
    
    // protected $_calculatedFields = array(
    //     'CantidadEnUnidadDeMedida' => 'fArticuloStockPorCantidad(Mmis.Articulo, ProduccionesMmisMovimientos.Cantidad)',
    //     'DescPackaging' => 'fArticuloPackagingDescripcion(Mmis.Articulo)'
    // );
    
    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('Mmis')
              ->joinRef('Articulos',array('ArticuloDescripcion' => 'Descripcion'));
        }
        
        // $this->addAutoJoin(
        //         'Articulos',
        //         'Mmis.Articulo = Articulos.Id',
        //         array(
        //             'ArticuloDescripcion' => 'Articulos.Descripcion'
        //         )
        // );
        // $this->addAutoJoin(
        //         'Productos',
        //         'Productos.Id = Articulos.Producto',
        //         array(
        //             'ProductoDescripcion' => 'Productos.Descripcion'
        //         )
        // );
        // $this->addAutoJoin(
        //         array('UnidadesDeMedidasP' => 'UnidadesDeMedidas'),
        //         'UnidadesDeMedidasP.Id = Productos.UnidadDeMedida',
        //         array(
        //             'UnidadDeMedidaPDescripcion' => 'UnidadesDeMedidasP.DescripcionR',
        //             'UnidadDeMedidaPTipo'        => 'TipoDeUnidad'
        //         )
        // ); 
    }
   
    /**
     * Registra un cambio en un mmi dentro de produccion
     * PRESUME QUE EXISTE EL MMI Y LA PRODUCCION
     * 
     * @param type $iProduccion
     * @param type $idmmi
     * @param type $cantidad
     * @param type $tipo 1 Uso de mercaderia, 2 Egreso de mercaderia producida
     */
    public function registratMovimientoDeMmiEnProduccion($iProduccion, $idmmi, $cantidad, $tipo)
    {
        try {
            $this->_db->beginTransaction();
            $idProduccion = $this->getAdapter()->quote($iProduccion, 'INTEGER');
            $idmmi = $this->getAdapter()->quote($idmmi, 'INTEGER');
           // $cantidad = $this->getAdapter()->quote($cantidad, 'INTEGER'); 
            
            $M_P = new Produccion_Model_DbTable_Producciones();
            $M_M = new Almacenes_Model_DbTable_Mmis();            

//            // Verifico q exista la produccion 
//            $R_P = $M_P->find($idProduccion)->current();
//
//            if (!$R_P)
//                throw new Rad_Exception('No se encontro la Produccion.');               
//            
//            // Verifico q exista la Orden de Produccion Mmi 
//            $R_M = $M_M->find($idmmi)->current();
//
//            if (!$R_M)
//                throw new Rad_Exception('No se encontro el Mmi.');              
            
            // creamos un movimiento
            $row = $this->createRow();
            $row->Produccion             = $idProduccion;
            $row->Mmi            	     = $idmmi;
            $row->Tipo                   = $tipo;
            $row->Cantidad               = $cantidad; 		
            $row->Fecha                  = date('Y-m-d H:i:s');
            $row->save();                   

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }       
    }
    
    /**
     * Retorna la cantidad de materia prima que se quito al mmi de materia prima 
     * dado por $idMmi en la Produccion $idProduccion
     * @param int $idMmi Id del MMI
     * @param int $idPrpoduccion Id de la Produccion 
     */
    public function retornarCantidadUtilizadaDeMateriaPrima($idMmi, $idProduccion) {
        $select = $this->select();
        $select->from(array('p' => 'ProduccionesMmisMovimientos'),array('cantidad' => 'SUM(Cantidad) * -1'));
        $select->where('Mmi = '.$idMmi)->where('Produccion = '.$idProduccion);
        $cantidad = $this->_db->fetchOne($select);
        
        if (!$cantidad) $cantidad = 0;
        
        return $cantidad;
    }
    
}