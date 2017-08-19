<?php
require_once 'Rad/Window/Controler/Action.php';
require_once 'PhpExt/Javascript.php';

class Window_TablerosDeControlController extends Rad_Window_Controler_Action
{
    protected $title = "Tablero de Control";
    
    protected function initWindow ()
    {    	
    	$tablerosDeControl = new Model_DbTable_TablerosDeControl(array(), false);
		$rowset = $tablerosDeControl->fetchAll();
		
		$porLinea = 3;
		$i = 0;
		
		// Arma la tabla donde se van a insertar los relojes
		$gaugeGrid = '"<table><tr>';
		foreach ($rowset as $row) {
			$gaugeGrid .= "<td id='td-{$row->Id}' style='width: 200px; height: 200px;'><div class='x-gauge-cell' id='gauge-{$row->Id}'></div></td>";
			$tablerosJsCreate[] = array('Id'		=> $row->Id,
										'ValorReal'	=> sprintf('%2.2f',((($row->ValorReal-$row->ValorMinimo) * 100 / ($row->ValorMaximo - $row->ValorMinimo))))
										);
			$i++;
			if (($i % $porLinea == 0) && ($i != count($rowset)))
				$gaugeGrid .= '</tr><tr>';
		}
		$gaugeGrid .= '</tr></table>"';
		
		$ventana = "
			{
				xtype: 'panel',
				bodyBorder: false,
				hideBorders: true,
				style: 'padding: 10px;',
				autoScroll: true,
				html: $gaugeGrid
			}
		";
		
		// Javascript que llama a la funcion para crear un reloj y setea variables para el movimiento
		foreach ($tablerosJsCreate as $tab)
			$tJs .= "setTimeout('drawGauge({$tab['Id']});', 500); win.gaugesValues.push(0); win.gaugesRealValues.push({$tab['ValorReal']});";
		
		// Constructor de relojes y checkeo de errores
		$this->setPostConstructorsJs("
			win.gaugeError = false;

			win.gauges = new Array();
			win.gaugesValues = new Array();
			win.gaugesRealValues = new Array();
			win.gaugesTimer = new Array();
			
			updateValue = function(tablero) {
				if (win.gaugesValues[tablero]+1 > win.gaugesRealValues[tablero]) {
					win.gaugesValues[tablero] = win.gaugesRealValues[tablero];
					clearInterval(win.gaugesTimer[tablero]);
				} else {
					win.gaugesValues[tablero]++;
				}
				win.gauges[tablero].needle.setValue( win.gaugesValues[tablero] );
				win.gauges[tablero].label.setText( win.gaugesValues[tablero] );
			}
			
			drawGauge = function(tablero) {
				try {
					win.gauges.push( bindows.loadGaugeIntoDiv('/window/tablerosDeControl/getxml/tablero/'+tablero, 'gauge-'+tablero) );
					win.gaugesTimer.push( setInterval('updateValue('+ (win.gaugesTimer.length) +');', 80) );
				} catch (err) {
					if (win.gaugeError == false) {
						// TODO: algo lindo q avise q no anda nada
						win.gaugeError = true;
					}
    			}
			};
			$tJs
		");
		
		$this->windowsObj->items = PhpExt_Javascript::variable($ventana);
        $this->windowsObj->setWidth(675);		
        $this->windowsObj->setMinWidth(675);
        $this->windowsObj->setHeight(475);
        $this->windowsObj->setMinHeight(475);
        $this->windowsObj->setBorder(false);
        $this->windowsObj->layout = "fit";
    	
	}

	function getxmlAction() {	
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	$tablero = $this->getRequest()->tablero;
    	$tablerosDeControl = new Model_DbTable_TablerosDeControl(array(), false);
    	
    	$row = $tablerosDeControl->fetchRow("Id = $tablero");
    	
    	$cien = $row->ValorMaximo - $row->ValorMinimo;
        $xml = $this->view->JsonRender	(APPLICATION_PATH.'/common/json/Clock.xml',
								array	(	'Descripcion'	=> $row->Descripcion,
											'Unidad'		=> $row->Unidad,
											'ValorMinimo'  	=> 0,
											'ValorMaximo'	=> 100,
											'ValorReal'		=> 0,
											'OptimoMinimo'	=> (($row->OptimoMinimo-$row->ValorMinimo) * 100 / $cien),
											'OptimoMaximo'	=> (($row->OptimoMaximo-$row->ValorMinimo) * 100 / $cien),
											'RegularMinimo'	=> (($row->OptimoMinimo - $row->RegularMinimo)-$row->ValorMinimo) * 100 / $cien,
											'RegularMaximo'	=> (($row->OptimoMaximo + $row->RegularMaximo)-$row->ValorMinimo) * 100 /  $cien,
											'VMinimo'		=> $row->ValorMinimo,
											'VMaximo'		=> $row->ValorMaximo,
											'Etiquetas'		=> 6
										)
							);
    	
		$this->getResponse()->setHeader('Content-Type', 'text/xml');
		$this->getResponse()->setHeader('Encoding', 'UTF-8');
		$this->getResponse()->setBody($xml);	

	}
}