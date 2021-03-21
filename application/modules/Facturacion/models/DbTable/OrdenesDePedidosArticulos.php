<?php
require_once('Rad/Db/Table.php');
/**
 * @class 		Facturacion_Model_DbTable_OrdenesDePedidosArticulos
 * @extends		Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * Ordenes de Pedidos Articulos
 * 
 * Detalle de la cabecera de la tabla 
 * Campos:
 * 		Id					-> Identificador Unico
 *		Comprobante			-> identificador de la Factura Venta	
 *		TipoDeComprobanteDetalle 	-> identidicador de Art, Serv, tiket
 *		Articulo			-> identificador del articulo, servicio, etc (puede ser null)
 * 		CuentaCasual		-> Cuenta del Plan de Cuenta a utilizar en el caso qeu no se indique el articulo
 *		Cantidad			-> Cantidad de elementos del articulo indicado
 *		PrecioUnitario		-> Precio por unidad del articulo expresado en moneda local
 *		PrecioUnitarioMExtranjera	-> Precio por unidad del articulo expresado en otra moneda
 *		DescuentoEnPocentaje		-> Descuento realizado sobre el precio unitario (rango 0.01 a 99.99) 
 *		Modificado			-> Bndera que indica si fue modificado manualmente
 *		Observaciones		-> Obs. internas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 *
 */
class Facturacion_Model_DbTable_OrdenesDePedidosArticulos extends Facturacion_Model_DbTable_ComprobantesDetalles
{

    protected $_calculatedFields = array(
        'CantAsociada' => "fCantSinAsociarRelHijo(ComprobantesDetalles.Comprobante,ComprobantesDetalles.Articulo)"
    );
     
	/**
     * Mapa de referencias de la clase
     * @var array
     */
    protected $_referenceMap    = array(
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosGenericos',
            'refJoinColumns'    => array("Descripcion","DescArreglada" =>"IF(ComprobantesDetalles.Articulo is null,ComprobantesDetalles.Observaciones,Articulos.Descripcion)", 'Tipo', "Codigo"),                    
            'comboBox'			=> true,                                    
            'comboSource'		=> 'datagateway/combolist/fetch/EsArticuloParaVenta',
            'refTable'			=> 'Articulos',
            'refColumns'        => 'Id',
            'comboPageSize'		=>	10
        ),
        'OrdenesDePedidos' => array(
            'columns'           => 'Comprobante',
            'refTableClass'     => 'Facturacion_Model_DbTable_OrdenesDePedidos',
            'refTable'			=> 'Comprobantes',
            'refColumns'        => 'Id',
        )
        
    );
	// fin  protected $_referenceMap -----------------------------------------------------------------------------
	
    /**
	 * Validadores
	 *
	 * Articulo		-> valor unico
	 * Cantidad 	-> mayor a cero
	 * Monto		-> mayor a cero
	 *
	 */
	public function init()     {
        $this ->_validators = array(
            /*'Articulo'  => array(
                array(
                    'Db_NoRecordExists',
                    'ComprobantesDetalles',
                    'Articulo',
                    'Comprobante = {Comprobante} AND Id <> {Id}'
                ),
                'messages' => array('El articulo ya existe en la Orden de Pedido')
            ),*/
            'Cantidad'  => array(
                array('GreaterThan',0),
                'messages' => array('La cantidad no puede ser 0 (cero)')
            ),
            'Monto'=> array(
                array('GreaterThan',0),
                    'messages' => array('El precio no puede ser menor a 0')
            )
        );			
		parent::init();
	}
	
	/**
	 * Inseta un Registro
	 *
	 * @param array $data
	 * @return mixed
	 */	
	public function insert($data)	{

	    $this->_db->beginTransaction();
	    try {
		$M_OP	= new Facturacion_Model_DbTable_OrdenesDePedidos(array(),false);

                if (isset($data['Cantidad']) && $data['Cantidad'] <= 0) {
                    throw new Rad_Db_Table_Exception('La cantidad no puede ser 0 (cero).');
                }			
			
                $R_OP = $M_OP->find($data['Comprobante'])->current();
                if (!$R_OP) {
                        throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');
                }
                
                // Verifico que venga el articulo
                if (!$data['Articulo']) {
                        throw new Rad_Db_Table_Exception('Falta seleccionar el Articulo.');
                }                
			
                $M_OP->salirSi_estaCerrado($data['Comprobante']);
			
                // Verifico si ya existe el articulo en el comprobante
                if ($data['Articulo'] && $this->estaElArticuloEnComprobante($data['Comprobante'],$data['Articulo'])){
                        // el articulo se encuentra en el comprobante => updatear cantidad
                        $Rx = $this->fetchRow("Comprobante = ".$data['Comprobante']." and Articulo = ".$data['Articulo']);
                        if ($Rx) {
                                $data["Cantidad"] = $Rx->Cantidad + $data["Cantidad"];
                        }                       
                        $this->update($data,"ComprobantesDetalles.Id = ".$Rx->Id);
                        $id = $Rx->Id;
                        // Publico
                        Rad_PubSub::publish('Facturacion_OPA_Updateado',$Rx);
                } else {
                        // Si esta en moneda extranjera calculo el Precio Unitario en moneda local 
                        if($M_OP->estaEnMonedaExtranjera($data['Comprobante'])) {
                                if (!$data['PrecioUnitario']) {
                                        $data['PrecioUnitario'] = $M_OP->recuperarPUdeListaDePrecioPropia($data['Comprobante'],$data['Articulo']) * $R_OP->ValorDivisa;
                                        $data['PrecioUnitarioMExtranjera'] = 0;
                                } else {
                                        $data['PrecioUnitarioMExtranjera'] = $data['PrecioUnitario'];
                                        $data['PrecioUnitario'] = $data['PrecioUnitarioMExtranjera'] * $R_OP->ValorDivisa;
                                }
                        } else {
                                if (!$data['PrecioUnitario']) {
                                        $data['PrecioUnitarioMExtranjera'] = 0;
                                        $data['PrecioUnitario'] = $M_OP->recuperarPUdeListaDePrecioPropia($data['Comprobante'],$data['Articulo']);
                                }
                        }

                        // Inserto el articulo y publico
                        $id = parent::insert($data);
                        $R_Ins = $this->find($id)->current();
                        Rad_PubSub::publish('Facturacion_OPA_Insertado',$R_Ins);
                }
			$this->_db->commit();
	        return $id;
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}
	
	/**
	 * updateo un registro 
	 *
	 * @param array $data
	 * @param mixwd $where
	 * @return mixed
	 */
	public function update($data,$where) 	{

		$this->_db->beginTransaction();
	    try {
	        // Verifico las cuestiones de forma
	    	if (isset($data['Cantidad']) && $data['Cantidad'] <= 0)	{
				throw new Rad_Db_Table_Exception('La cantidad no puede ser 0 (cero).');
			} 
			
			$M_OP = new Facturacion_Model_DbTable_OrdenesDePedidos(array(),false);
			$reg = $this->fetchAll($where);

			foreach ($reg as $row){
				// Salgo si no se puede modificar la Orden de Compra
				$M_OP->salirSi_estaCerrado($row['Comprobante']);
				
				// Recupero la cabecera
				$R_OP = $M_OP->find($row['Comprobante'])->current();
				if (!$R_OP) {
					throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');
				}				
				// Opero segun en que moneda este
				if($M_OP->estaEnMonedaExtranjera($row['Comprobante'])) {
					
					// Calculo el PU en moneda local
					if (!$data['PrecioUnitarioMExtranjera']) {
						$data['PrecioUnitario'] = 0;
					} else {
						//$data['PrecioUnitarioMExtranjera'] = $data['PrecioUnitario'];
						$data['PrecioUnitario'] = $data['PrecioUnitarioMExtranjera'] * $R_OP->ValorDivisa;
					}					
					
				} else {

					$data['PrecioUnitarioMExtranjera'] = 0;					
				}
                                unset($data["Id"]); 
				// Updateo
				parent::update($data,"Id=".$row['Id']);

				Rad_PubSub::publish('Facturacion_OPA_Updateado',$row);

			}	
			
	        $this->_db->commit();
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}
	
	/**
	 * Borra los registros indicados
	 *
	 * @param array $where
	 * 
	 */
	public function delete($where) 	{
	    
	    try {
			$this->_db->beginTransaction();

			$M_OP	= new Facturacion_Model_DbTable_OrdenesDePedidos(array(),false);

			$reg = $this->fetchAll($where);

			// Si tiene articulos los borro
			if(count($reg)){

				foreach ($reg as $row){
					// Salgo si no se puede modificar la Orden de Compra
					$M_OP->salirSi_estaCerrado($row['Comprobante']);
				}	
				foreach ($reg as $row){
					// Publico y borro el renglon
					parent::delete("Id =".$row['Id']);
					Rad_PubSub::publish('Facturacion_OPA_Borrado',$row);
				}
			}
	        $this->_db->commit();
		} catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}	

	
}
