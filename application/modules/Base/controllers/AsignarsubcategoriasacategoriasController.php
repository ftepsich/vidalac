<?php
require_once 'Rad/Window/Controller/Action.php';

/**
 * Base_AsignarSubcategoriasACategoriasController
 *
 * Controlador Asignar Subcategorias A Categorias
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_AsignarSubcategoriasaCategoriasController
 * @extends		Rad_Window_Controller_Action
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_AsignarsubcategoriasacategoriasController extends Rad_Window_Controller_Action
{
    protected $title = "Subcategorias a Categorias";

    public function initWindow ()
    {
		/**
         * Grilla Analisis Modelos
         */
        $configHija->loadAuto			= false;
        $configHija->abmWindowWidth		= 600;
        $configHija->abmWindowHeight	= 200;
        $configHija->abmWindowTitle		= 'Asignar Subcategorias A Categorias';
        $configHija->id                 = 'trSubcategoriasModelosHija';

		$this->view->gridProductosSubCategorias = $this->view->radGrid(
            'Base_Model_DbTable_ProductosSubCategorias',
            $configHija,
            'abmeditor'
        );

		$detailGrid->id	 	     = 'trSubcategoriasModelosHija';
		$detailGrid->remotefield = 'ProductoCategoria';
		$detailGrid->localfield	 = 'Id';

        $configHijaP->detailGrid		= $detailGrid;
        $configHijaP->abmWindowWidth	= 600;
        $configHijaP->abmWindowHeight	= 200;
        $configHijaP->abmWindowTitle	= 'Categorias';
						

        $this->view->grid = $this->view->radGrid('Base_Model_DbTable_ProductosCategorias' ,$configHijaP ,'abmeditor');
        
    }
}
