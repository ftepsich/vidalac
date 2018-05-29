<?php

/**
 * Almacenes_RelacionarRemitosFacturasVentasController
 *
 * Relacionar Remitos a Facturas Ventas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Almacenes_RelacionarRemitosFacturasVentasController
 * @extends Rad_Window_Controller_Action
 */
class Almacenes_RelacionarRemitosFacturasVentasController extends Rad_Window_Controller_Action
{
    protected $title = 'Asociar Remitos a Facturas de Ventas';

    public function initWindow()
    {
//		$detailGrid->id 			= $this->getName().'_FacturasVentas';
//		$detailGrid->remotefield 	= 'ComprobantePadre';
//		$detailGrid->localfield		= 'Id';

		$this->view->gridFacturaVenta = $this->view->RadGridManyToMany(
    		'Facturacion_Model_DbTable_FacturasVentas',
    		'Facturacion_Model_DbTable_FacturasVentasRemitos',
    		'Almacenes_Model_DbTable_RemitosDeSalidas',
    		array(
    		    'withPaginator'	  => false,
    		    'withToolbar'	  => false,
                'loadAuto'        => false,
    			'iniSection'      => 'reducido',
				'fetch'			  => 'FaltantesDeEnviar',
				'detailGrid'      => $detailGrid,
                'id'              => $this->getName().'_FacturasVentas'
            )
		);
		
		
		/**
		 * Facturas de Ventas Articulos (Paso 2 - Hija)
		 */
		 /*
		$config->loadAuto        = false;
		$config->title           = null;
		$config->withPaginator   = false;
        $config->id              = $this->getName().'_FacturasVentasArticulos';

 		$this->view->gridFacturaVentaArticulos = $this->view->radGrid(
			'Facturacion_Model_DbTable_FacturasVentasArticulos',
            $config,
            '',
            'reducido'
		);
		unset($config);
		*/

		/**
		 * Grilla Remitos de Salidas
		 */
        $this->view->grid = $this->view->radGrid(
           'Almacenes_Model_DbTable_RemitosDeSalidas',
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
                                gh = Ext.getCmp('{$this->getName()}_FacturasVentas');
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