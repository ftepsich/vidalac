<?php
require_once 'Rad/Window/Controller/Action.php';

/**
 * Laboratorio_HabilitarMmisController
 *
 * Controlador Habilitar MMi
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_HabilitarMmisController
 * @extends		Rad_Window_Controller_Action
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_AsignaranalisisagruposController extends Rad_Window_Controller_Action
{
    protected $title = "Analisis a Grupos";

    public function initWindow ()
    {
		/**
         * Grilla Analisis Modelos
         */
        $configHija->loadAuto			= false;
        $configHija->abmWindowWidth		= 600;
        $configHija->abmWindowHeight	= 200;
        $configHija->abmWindowTitle		= 'Asignar Analisis al Grupo';
        $configHija->id                 = 'trAnalisisModelosHija';

		$this->view->gridAnalisisModelos = $this->view->radGrid(
            'Laboratorio_Model_DbTable_AnalisisModelos',
            $configHija,
            'abmeditor'
        );

		$detailGrid->id	 	     = 'trAnalisisModelosHija';
		$detailGrid->remotefield = 'AnalisisTipoModelo';
		$detailGrid->localfield	 = 'Id';

        $configHijaP->detailGrid		= $detailGrid;
        $configHijaP->abmWindowWidth	= 600;
        $configHijaP->abmWindowHeight	= 200;
        $configHijaP->abmWindowTitle	= 'Grupo de Analisis';
						

        $this->view->grid = $this->view->radGrid('Laboratorio_Model_DbTable_AnalisisTiposModelos' ,$configHijaP ,'abmeditor');
        
    }
}
