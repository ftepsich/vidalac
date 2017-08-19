<?php
require_once('Rad/Db/Table.php');

class Model_DbTable_OrdenesDePedidosArticulos extends Rad_Db_Table
{
	protected $_name = "OrdenesDePedidosArticulos";
	
	// Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Model_DbTable_Articulos',
     		'refColumns'        => 'Id',
     		'refJoinColumns'    => array("Descripcion","Codigo"),                     // De esta relacion queremos traer estos campos por JOIN
     		'comboBox'			=> true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
     		'comboSource'		=> 'datagateway/combolist/fetch/EsArticuloParaVenta',
            'refTable'			=> 'Articulos',
    		'comboPageSize'		=>	10
        ),
        'OrdenesDePedidos' => array(
            'columns'           => 'OrdenDePedido',
            'refTableClass'     => 'Model_DbTable_OrdenesDePedidos',
     		'refJoinColumns'    => array("Observaciones"),                     // De esta relacion queremos traer estos campos por JOIN
     		'comboBox'			=> true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'OrdenesDePedidos',
            'refColumns'        => 'Id',
        )/*,
        'Bienes' => array(
            'columns'           => 'BienGenerico',
            'refTableClass'     => 'Model_DbTable_Bienes',
     		'refJoinColumns'    => array("Descripcion"),                     // De esta relacion queremos traer estos campos por JOIN
     		'comboBox'			=> true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Bienes',
            'refColumns'        => 'Id',
        )*/
);
	// fin  protected $_referenceMap -----------------------------------------------------------------------------
	
	// Inicio Public Init ----------------------------------------------------------------------------------------
	public function init()     {
			$this->_defaultValues = array (
				'Descuento' 	=> 0
			);
			$this ->_validators = array(
				'Articulo'=> array(	
									array(	
											'Db_NoRecordExists',
											'OrdenesDePedidosArticulos',
											'Articulo',
											'OrdenDePedido = {OrdenDePedido} AND Id <> {Id}'
									),
								'messages' => array('El articulo ya existe en la Orden de Pedido')
								),
				'Cantidad'=> array( 
									array( 'GreaterThan',
											0
									),
								'messages' => array('La cantidad no puede ser 0 (cero)')
								),
				'Monto'=> array(
									array('GreaterThan',0),
								'messages' => array('El precio no puede ser menor a 0')
								)								
			);
		parent::init();
	}
// ========================================================================================================================	
	
	public function insert($data)	{
		//TODO: Ver si la gente de Zend se pone las pilas y hace un $this->_db->inTransaction();
	    $this->_db->beginTransaction();
	    try {
			if(!$data["Monto"]){	
				$data["Monto"] = $this->recuperarPrecioUnitario($data['OrdenDePedido'],$data['Articulo']);
			}else  {
				$data['IngresoManual'] = 1;
			}
			$data['CantidadPendiente'] = $data['Cantidad'];
			/*if(!$data["Descuento"]){
				$data["Descuento"]=0;
			}	*/
	        $id = parent::insert($data);
			
			$M_OrdenesDePedidos	= new Model_DbTable_OrdenesDePedidos(array(),false);
			$M_OrdenesDePedidos->cambiarEstadoPorInsert($data['OrdenDePedido']);
	        
			$this->_db->commit();
	        return $id;
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}
// ========================================================================================================================	
	public function update($data,$where)	{
		//TODO: Ver si la gente de Zend se pone las pilas y hace un $this->_db->inTransaction();
	    $this->_db->beginTransaction();
	    try {
			if (isset($data['Cantidad']))  {
				$M_OrdenesDePedidos	= new Model_DbTable_OrdenesDePedidos(array(),false);

				$R = $this->fetchAll($where);
				
				$id = parent::update($data,$where); 

				foreach ($R as $row) {
					$M_OrdenesDePedidos->cambiarEstadoOP($row['OrdenDePedido'],'');
					$data["Monto"] = $this->recuperarPrecioUnitario($data['OrdenDePedido'],$data['Articulo']);
					if($row->Monto != $data['Monto']){
						$data['IngresoManual'] = 1;
						parent::update($data, 'Id ='.$row['Id']);	
						
					}
				}
			} else {
				parent::update($data,$where); 
			}
	        $this->_db->commit();
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}
//========================================================================================================================
	
	public function delete($where) 	{
	    $this->_db->beginTransaction();
	    try {
			$R_OPA		= $this->fetchAll($where);
			$M_ODP		= new Model_DbTable_OrdenesDePedidos(array(),false);				
			foreach ($R_OPA as $row) {
				if ($row['OrdenDePedido'])
					$M_ODP->salirSi_NoSePuedeModificar($row['OrdenDePedido']);
			}
			parent::delete($where); 
			
	        $this->_db->commit();
		} catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}
	
// ========================================================================================================================	
	public function insertarPrecio($OrdenDePedido) {
		try {
			$this->_db->beginTransaction();	
			
			$M_OP = new Model_DbTable_OrdenesDePedidos(array(),false);
			$R_OrdenesDePedidos 	= $M_OP->fetchRow("Id = ".$OrdenDePedido);
			
			if ($R_OrdenesDePedidos->ProductoListaDePrecio) {

				$R_OPA 	= $this->fetchAll("OrdenDePedido = ".$OrdenDePedido);
 
					if (!$R_OPA) {
						// Arrojar una exception que corta el script y muestra un mensaje en pantalla
						throw new Rad_Db_Table_Exception('No se encontro el registro.');
					}
				
			        foreach ($R_OPA as $row) {
						$PrecioUnitario = $this->recuperarPrecioUnitario($row['OrdenDePedido'],$row['Articulo']);
						$row2 	= array('Monto' => $PrecioUnitario);
						parent::update($row2, 'Id ='.$row['Id']);
					}
			} else {
					throw new Rad_Db_Table_Exception("No se selecciono la lista de precio.");
			}
			
			
			$this->_db->commit();
		} catch (Exception $e) {
			$this->_db->rollBack();
			throw $e;
	    }		
	}
// ========================================================================================================================	
	public function recuperarPrecioUnitario($OrdenDePedido,$Articulo) 	{
	    if (!$OrdenDePedido) {
			throw new Rad_Db_Table_Exception("No se encuentra el registro.");
		} else {
	        $M_OP = new Model_DbTable_OrdenesDePedidos();
	        $R_OP = $M_OP->find($OrdenDePedido)->current();
	        if ($R_OP) {
	            $M_LP = new Model_DbTable_ProductosListasDePreciosDetalle();
	            $R_LP = $M_LP->fetchRow("Articulo = $Articulo AND ListaDePrecio = $R_OP->ProductoListaDePrecio");
	            if ($R_LP) {
                    $PrecioUnitario	= $R_LP->Precio;
                    if (!$PrecioUnitario) {
                        $PrecioUnitario = '0.00';        
                    }
	            } else {
	                $PrecioUnitario = '0.00';
	            }
	        }
	    } 
		return $PrecioUnitario;
		
	}	
// ========================================================================================================================	
	public function updateSinRecalculo($data,$where) {
		try {
			$this->_db->beginTransaction();	
			
			parent::update($data,'Id ='.$where);					
			
			$this->_db->commit();
		} catch (Exception $e) {
			$this->_db->rollBack();
			throw $e;
	    }		
	}


}

?>