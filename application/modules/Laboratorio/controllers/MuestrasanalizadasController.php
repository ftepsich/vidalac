<?php


/**
 * Controlador Muestras analizadas
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_Model_DbTable_RemitosEstados
 * @extends		Rad_Window_Controller_Action
 */
class Laboratorio_MuestrasanalizadasController extends Rad_Window_Controller_Action
{
    protected $title = "Muestras ya analizadas";
    
    public function initWindow ()
    {
        /**
         * Grilla hija
         */
		$configHija->LoadAuto	= false;
		$configHija->id     	= 'trAnalisisModeloswdebvhioHija26bdr';
        
		$this->view->gridAnalisisProt = $this->view->radGrid('Laboratorio_Model_DbTable_AnalisisProtocolo',$configHija,'');

		/**
         * Grilla Principal
         */
		$detailGrid->id				='trAnalisisModeloswdebvhioHija26bdr';
		$detailGrid->remotefield	='Muestra';
		$detailGrid->localfield		='Id'; 
											
		$configHijaP->abmWindowWidth 	= 740;
		$configHijaP->abmWindowHeight 	= 350;
		$configHijaP->abmWindowTitle	= 'Agregar muestras';
		$configHijaP->detailGrid		= $detailGrid;
		$configHijaP->fetch     		= 'Controlados';


		$this->view->grid = $this->view->radGrid('Laboratorio_Model_DbTable_AnalisisMuestras' ,$configHijaP);

    }
}
