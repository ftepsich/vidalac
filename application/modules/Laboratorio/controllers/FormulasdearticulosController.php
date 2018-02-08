<?php
//require_once 'Rad/Window/Controler/Action.php';
//require_once 'PhpExt/Javascript.php';
             
class Window_FormulasDeArticulosController extends Rad_Window_Controler_Action
{
    protected $title = "Formulas de Los Productos";
    
    protected function initWindow ()
    {
		// ----------------------------------------------------------------------------------------------------------
		// GRILLA HIJA 1
		// ----------------------------------------------------------------------------------------------------------
		
		$configHija->abmWindowTitle 	='Insumos de los Productos';
		$configHija->abmWindowWidth 	= 500;
		$configHija->abmWindowHeight 	= 150;			
		$configHija->title 				= 'Insumos';
		//$configHija->iniSection 		= 'reducido';
		$configHija->loadAuto			= false;
		//$configHija->abmForm 			= new Zend_Json_Expr($abmForm);
        
		$grillaHija1 = $this->view->radGrid('FormulasProductos' ,'/datagateway' ,'trGridFormulaHija11' ,$configHija,'abmeditor');

		
		// ----------------------------------------------------------------------------------------------------------
		// GRILLA PADRE
		// ----------------------------------------------------------------------------------------------------------


		$detailGrid->id 			= 'trGridFormulaHija11';
		$detailGrid->remotefield 	= 'Producto';
		$detailGrid->localfield		= 'Id';
				
        $configHijaP->detailGrid = $detailGrid;
		
        $grillaPadre = $this->view->radGrid('Productos' ,'/datagateway' ,'eGridArticuloPadre11' ,$configHijaP ,'','TieneFormula','reducido');
        
		// ----------------------------------------------------------------------------------------------------------
		// ENSAMBLO GRILLAS EN JSON
		// ----------------------------------------------------------------------------------------------------------
		
		$JsonPaneles = $grillaHija1;
		
		$JsonPaneles = $this->view->JsonRender	(
			APPLICATION_PATH.'/common/json/TabPanel.json',
			array	('JsonPaneles' => $JsonPaneles)
		);	
        $ventana = $this->view->JsonRender	(
			APPLICATION_PATH.'/common/json/BorderNC.json',
			array (	
				'JsonGrillaNorte'  	=> $grillaPadre,
				'JsonTituloNorte'  	=> 'Productos',
				'JsonAltoNorte'	=> '250',
				'JsonSplitNorte'	=> 'true',
				'JsonGrillaCentro' 	=> $JsonPaneles,
				'JsonTituloCentro'  => ''
			)
		);
		
		// ----------------------------------------------------------------------------------------------------------
		// DIBUJO
		// ----------------------------------------------------------------------------------------------------------
											
        $this->windowsObj->items = PhpExt_Javascript::variable($ventana);
        $this->windowsObj->setWidth(900);		
        $this->windowsObj->setMinWidth(800);		
        $this->windowsObj->setHeight(500);
        $this->windowsObj->setMinHeight(500);
        $this->windowsObj->setBorder(false);
        $this->windowsObj->layout = "fit";
    }
    
  	    
		
}
