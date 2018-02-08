<?php

/*

-- Esto en un futuro esperemos que no demasiado lejano va a ser un controlador serio

*/

require_once 'Rad/Window/Controler/Action.php';
//require_once 'PhpExt/Javascript.php';
             
class Window_PruebaController extends Rad_Window_Controler_Action
{
    protected $title = 'Prueba';
    
    protected function initWindow ()
    {
		
		$ventana = "
		{	
			xtype:			'panel',
			buttonAlign:	'center',
			buttons:		[
				{
					text:		'Apagar',
					handler:	function() {
						
					}
				}
			]
		}";
		
		// ----------------------------------------------------------------------------------------------------------
		// DIBUJO
		// ----------------------------------------------------------------------------------------------------------
		
        $this->windowsObj->items = PhpExt_Javascript::variable($ventana);
        $this->windowsObj->setWidth(300);
        $this->windowsObj->setMinWidth(300);
        $this->windowsObj->setHeight(200);
        $this->windowsObj->setMinHeight(200);
        $this->windowsObj->setBorder(false);
        $this->windowsObj->layout = 'fit';
    }

}