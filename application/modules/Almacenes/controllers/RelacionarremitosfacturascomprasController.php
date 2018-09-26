<?php

/**
 * Almacenes_RelacionarRemitosFacturasComprasController
 *
 * Relacionar Remitos a Facturas Compras
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Almacenes_RelacionarRemitosFacturasComprasController
 * @extends Rad_Window_Controller_Action
 */
class Almacenes_RelacionarRemitosFacturasComprasController extends Rad_Window_Controller_Action
{
    protected $title = 'Asociar Remitos a Facturas de Compras';

    public function initWindow()
    {
//		$detailGrid->id 			= $this->getName().'_FacturasCompras';
//		$detailGrid->remotefield 	= 'ComprobantePadre';
//		$detailGrid->localfield		= 'Id';

		$this->view->gridFacturaCompra = $this->view->RadGridManyToMany(
    		'Facturacion_Model_DbTable_FacturasCompras',
    		'Facturacion_Model_DbTable_FacturasComprasRemitos',
    		'Almacenes_Model_DbTable_RemitosDeIngresos',
    		array(
    		    'withPaginator'	  => false,
    		    'withToolbar'	  => false,
                'loadAuto'        => false,
    			'iniSection'      => 'reducido',
				'fetch'			  => 'FaltantesDeRecibir',
				'detailGrid'      => $detailGrid,
                'id'              => $this->getName().'_FacturasCompras'
            )
		);
		
		
		/**
		 * Facturas de Compras Articulos (Paso 2 - Hija)
		 */
		 /*
		$config->loadAuto        = false;
		$config->title           = null;
		$config->withPaginator   = false;
        $config->id              = $this->getName().'_FacturasComprasArticulos';

 		$this->view->gridFacturaCompraArticulos = $this->view->radGrid(
			'Facturacion_Model_DbTable_FacturasComprasArticulos',
            $config,
            '',
            'reducido'
		);
		unset($config);
		*/

		/**
		 * Grilla Remitos de Ingresos
		 */
        $this->view->grid = $this->view->radGrid(
           'Almacenes_Model_DbTable_RemitosDeIngresos',
           array(
               'abmForm'=>'',
               'detailGrid'=> $detailGrid,
			   'fetch'=>'FaltantesDeFacturar',		
               'sm' => new Zend_Json_Expr("
                     new Ext.grid.RowSelectionModel(
                     {
                        singleSelect: true,
                        listeners: {
                            'rowselect': function(i, rowIndex, r) {
                                detailGrid = {remotefield: 'ComprobantePadre', localfield: 'Id'};
                                gh = Ext.getCmp('{$this->getName()}_FacturasCompras');
                                gh.setPermanentFilter(1,'Persona',r.data.Persona);
                                gh.loadAsDetailGrid(detailGrid, r.data.Id);
                            }
                        }
                     })"
               )
           ),                // Evitamos que radGrid cree automaticamente el formulario al no tenerlo
           '','reducidoalmacenes'
        );
    }

}