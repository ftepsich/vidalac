<?php
require_once 'Rad/Window/Controler/Action.php';
require_once 'PhpExt/Javascript.php';
             
class Window_HabilitarMmisController extends Rad_Window_Controler_Action
{
    protected $title = 'Habilitar MMI para producción';
    
    protected function initWindow ()
    {
	
		// ----------------------------------------------------------------------------------------------------------
		// GRILLA HIJA
		// ----------------------------------------------------------------------------------------------------------
		
		$parametrosAdc->iniSection= 'habilitar';
		$parametrosAdc->withPaginator= false;
		
		$grilla = $this->view->radGrid('Mmis' ,'/datagateway' ,'GhijaMMisienrfjnadf' ,$parametrosAdc ,'editor');
		
		// ----------------------------------------------------------------------------------------------------------
		// ENSAMBLO GRILLAS EN JSON
		// ----------------------------------------------------------------------------------------------------------
		
        $ventana = $this->view->JsonRender	(APPLICATION_PATH.'/common/json/Border.json',
												array	(	'JsonGrillaCentro' 	=> $grilla,
															'JsonTituloCentro'  => null
														)
											);
		
		// ----------------------------------------------------------------------------------------------------------
		// DIBUJO
		// ----------------------------------------------------------------------------------------------------------
		
        $this->windowsObj->items = PhpExt_Javascript::variable($ventana);
        $this->windowsObj->setWidth(850);		
        $this->windowsObj->setMinWidth(600);
        $this->windowsObj->setHeight(500);
        $this->windowsObj->setMinHeight(500);
        $this->windowsObj->setBorder(false);
        $this->windowsObj->layout = 'fit';
    }

}

