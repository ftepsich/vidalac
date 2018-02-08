<?php
require_once 'Rad/Db/Table.php';

/**
 * Model_DbTable_ProveedoresListasDePrecios
 *
 * Listas de Precios de los Proveedores y tambien de los Clientes
 * almacena el ultimos precio de compra o venta en el caso que hubiera cambiado el mismo
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Model_DbTable_ProveedoresListasDePrecios
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasListasDePrecios extends Rad_Db_Table
{

    protected $_name = 'PersonasListasDePrecios';
	
    protected $_sort = array('FechaUltimaCompra desc');
	
    protected $_referenceMap = array(
        'Articulos' => array(
            'columns'           => 'Articulo',
            'refTableClass'     => 'Base_Model_DbTable_Articulos',
            'refColumns'        => 'Id',
            'refJoinColumns'    => array('Descripcion','Codigo'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/EsInsumo',
            'refTable'          => 'Articulos',
            'comboPageSize'     => 20
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id'
        ),
        'TiposDeDivisas' => array(
            'columns'           => 'Divisa',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDivisas',
            'refColumns'        => 'Id'
        ),
        'Factura' => array(
            'columns'           => 'Comprobante',
            'refTableClass'     => 'Facturacion_Model_DbTable_Facturas',
            'refJoinColumns'    => array('Numero', 'Punto'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Comprobantes',
            'refColumns'        => 'Id'
        )
    );
	
	//------------------------------------------------------------------------------------------------------------
	///funcion q actualiza la lista de precio 
	public function reasignarPrecioArticulo($row) 	{
		$this->_db->beginTransaction();	        
		try {
		
			$idCD       = $this->_db->quote($row->Id);
			$idArticulo = $this->_db->quote($row->Articulo);
			$idC    	= $this->_db->quote($row->Comprobante);

	        /* controlo que venga el articulo y que el precio sea mayor que 0 */
	        if ($idArticulo && (($row->PrecioUnitario && $row->PrecioUnitario > 0) || ($row->PrecioUnitarioMExtranjera && $row->PrecioUnitarioMExtranjera > 0)))
	        {	

	            $M_C 	= new Facturacion_Model_DbTable_Comprobantes(array(),false);
				$R_C	= $M_C->find($idC)->current();
				
				/* Existe el Comproabnte */
				if (!$R_C) throw new Rad_Db_Table_Exception('No se encontro el registro del Comprobante.');
		        
				/* Debo ver el tipo de opercion... Compra o Venta */
	            $M_TC 	= new Facturacion_Model_DbTable_TiposDeComprobantes(array(),false);
				$R_TC	= $M_TC->find($R_C->TipoDeComprobante)->current();

				/* Existe el Tipo de Comproabnte */
				if (!$R_TC) throw new Rad_Db_Table_Exception('No se encontro el registro del Tipo de Comprobante.');

				/* Recupero el la ultima operacion de este tipo para ese cliente */
				$R_PLP	= $this->fetchRow("TipoOperacion = $R_TC->Grupo and Articulo = $idArticulo and Persona = $R_C->Persona and Divisa = $R_C->Divisa","FechaUltimaOperacion desc");
				
				/* Veo si esta en moneda extranjera */
				if ($R_C->Divisa == 1) {
					$pu 			= $row->PrecioUnitario;
					$valorDivisa 	= 1;
				} else {
					$pu 			= $row->PrecioUnitarioMExtranjera;
					$valorDivisa 	= $R_C->ValorDivisa;
				}

				$Renglon = array(
					'Articulo' 				=> $idArticulo,
					'Persona' 				=> $R_C->Persona,
					'FechaInforme'		 	=> date('Y-m-d'),
					'FechaUltimaOperacion' 	=> $R_C->FechaEmision,
					'PrecioUltimaOperacion'	=> $pu,
					'Divisa' 				=> $R_C->Divisa,
					'ValorDivisa'			=> $valorDivisa,
					'Comprobante' 			=> $idC,
					'ComprobanteDetalle'	=> $idCD,
					'TipoOperacion'			=> $R_TC->Grupo
					);
					
					//Rad_Log::debug($R_PLP);
					//Rad_Log::debug($R_PLP->PrecioUltimaCompra);
					//Rad_Log::debug($pu);
					
				///si no existe o existe y cambio el precio de la ultima compra inserto nuevo registro
				if (!$R_PLP || ($R_PLP && $R_PLP->PrecioUltimaCompra != $pu && $R_PLP->Divisa == $R_C->Divisa && $R_PLP->TipoOperacion != $R_TC->Grupo)) {

					$row = $M_PLP->insert($Renglo);
					
				} 
				
			}			
	        $this->_db->commit();
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}		
	//------------------------------------------------------------------------------------------------------------
	///funcion q actualiza la lista de precio 
	public function desasignarPrecioArticulo($row) 	{

		$this->_db->beginTransaction();	        
		try {

				$idCD       = $row->Id;
			
				$M_PLP 	= new Base_Model_DbTable_PersonasListasDePrecios(array(),false);
				
				if ($idCD) {	
					$where = 'FacturaCompraArticulo = '.$idCD;
					$M_PLP->delete($where); 
				}		
				
				$this->_db->commit();
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}		
	
	
}
