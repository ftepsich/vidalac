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
class Laboratorio_HabilitarMmisController extends Rad_Window_Controller_Action
{
    protected $title = 'Habilitar MMI para producción';
    
    public function initWindow ()
    {
		$parametrosAdc->withPaginator= false;
		$parametrosAdc->topButtons = array('add' => true, 'del' => false, 'edit' => false);
		
		$this->view->grid = $this->view->radGrid('Almacenes_Model_Mmis' ,$parametrosAdc ,'fasteditor', 'habilitar');
    }
}

