<?php
require_once 'Rad/Window/Controller/Action.php';

/**
 * Laboratorio_FormulasDeProductosController
 *
 * Formulas de productos
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Window_FormulasDeProductosController
 * @extends		Rad_Window_Controller_Action
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_FormulasDeProductosController extends Rad_Window_Controller_Action
{
    protected $title = "Formulas de Los Productos";
    
    public function initWindow ()
    {
		// ----------------------------------------------------------------------------------------------------------
		// GRILLA HIJA 1
		// ----------------------------------------------------------------------------------------------------------
		
		$configHija->abmWindowTitle 	='Insumos de los Productos';
		$configHija->abmWindowWidth 	= 500;
		$configHija->abmWindowHeight 	= 130;			
		$configHija->title 				= 'Insumos';
		//$configHija->iniSection 		= 'reducido';
		$configHija->loadAuto			= false;
        $configHija->id                 = 'trGridFormulaHija11';
		//$configHija->abmForm 			= new Zend_Json_Expr($abmForm);
        
		$this->view->gridFormulas = $this->view->radGrid('Laboratorio_Model_DbTable_FormulasProductos' ,$configHija,'abmeditor');

		
		// ----------------------------------------------------------------------------------------------------------
		// GRILLA PADRE
		// ----------------------------------------------------------------------------------------------------------


		$detailGrid->id 			= 'trGridFormulaHija11';
		$detailGrid->remotefield 	= 'Producto';
		$detailGrid->localfield		= 'Id';
        $detailGrid->fetch          = 'TieneFormula';
				
        $configHijaP->detailGrid = $detailGrid;
		
        $this->view->grid = $this->view->radGrid('Base_Model_DbTable_Productos',$configHijaP ,'','reducido');
    }
}
