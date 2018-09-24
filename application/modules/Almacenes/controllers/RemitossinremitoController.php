<?php
/**
*	Controlador de Remitos de Ingreso
*/         
class Almacenes_RemitosSinRemitoController extends Rad_Window_Controller_Action
{
    protected $title = 'Ingreso de Mercadería sin Remito';
	
	/**
	* Inicializa la ventana del modulo
	*/
    public function initWindow ()
    {
		/**
		 * Formulario Principal Remitos de Ingreso (Paso 1)
		 */
		$this->view->form = $this->view->radForm(
			'Almacenes_Model_DbTable_RemitosSinRemito',  
			'datagateway',
			'wizard'
		);
		
		/**
		 * Ordenes de Compras Remitos (Paso 2 - Padre)
		 */
		 
		$detailGrid->id 			= $this->getName().'_OrdenesDeComprasArticulos';
		$detailGrid->remotefield 	= 'Comprobante';
		$detailGrid->localfield		= 'Id';

		$this->view->gridOrdenesDeCompra = $this->view->RadGridManyToMany(
    		'Facturacion_Model_DbTable_OrdenesDeCompras',
    		'Facturacion_Model_DbTable_OrdenesDeComprasRemitos',
    		'Almacenes_Almacenes_Model_DbTable_Remitos',
    		array(
    			'title'           => 'Orden de Compra',
    		    'withPaginator'	  => false,
    		    'withToolbar'	  => false,
				'fetch'			  =>'AsociadosYFaltantesDeRecibir',				
                'loadAuto'        => false,
    			'iniSection'      => 'reducido',
				'detailGrid'      => $detailGrid,
                'id'              => 'OrdenesDeComprasRelacionadas'
            )
		);
		unset($detailGrid);
		
		/**
		 * Ordenes de Compras Articulos (Paso 2 - Hija)
		 */
		$config->loadAuto        = false;
		$config->title           = null;
		$config->withPaginator   = false;
        $config->id              = $this->getName().'_OrdenesDeComprasArticulos';

 		$this->view->gridOrdenesDeCompraArticulos = $this->view->radGrid(
			'Facturacion_Model_DbTable_OrdenesDeComprasArticulos',
            $config,
            '',
            'reducido'
		);
		unset($config);
	
		/**
		 * Articulos del Remito (Paso 3)
		 */
		$config->abmWindowTitle		= 'Artículo';
		$config->abmWindowWidth		= 650;
		$config->abmWindowHeight	= 200;
		$config->withPaginator		= false;
		$config->title 				= 'Artículo';
		$config->loadAuto			= false;
		$config->autoSave			= true;
		
		$this->view->gridRemitosArticulos = $this->view->radGrid(
			'Almacenes_Model_DbTable_RemitosArticulosDeIngresos',
            $config,
            'abmeditor',
            'wizard'
		);
	    unset($config);		
		
		/**
		 * Grilla Remitos de Ingresos
		 */
        $this->view->grid = $this->view->radGrid(
           'Almacenes_Model_DbTable_RemitosSinRemito',
           array('abmForm'=>''),                // Evitamos que radGrid cree automaticamente el formulario al no tenerlo
           'abmeditor'
        );		 
    }
	
	/**
	 *	Genera MMIs con los articulos
	 */

	public function cerrarremitoAction()
	{
	    //ini_set("display_errors",1);
	    $this->_helper->viewRenderer->setNoRender(true);
		
		$request 	= $this->getRequest();
		$idRemito 	= $request->getParam('id');
		
		$db = Zend_Registry::get('db');
		$idRemito = $db->quote($idRemito,'INTEGER');

		try {
			$M_RSR 	= new Almacenes_Model_DbTable_RemitosSinRemito(array(), false);
			$M_RSR->cerrar($idRemito);
			
			echo '{success: true}';
	    } catch (Rad_Db_Table_Exception $e) {
			//error_log($e->getMessage());
			echo "{success: false, msg: '".addslashes($e->getMessage()) ."'}";
	    }
	}		 
	
}