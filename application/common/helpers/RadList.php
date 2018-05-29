<?php
require_once 'Rad/CustomFunctions.php';
/**
 * Crea el Json para una Grilla de datos RadGrid
 *
 * @package Rad
 * @subpackage ViewHelpers
 * @class View_Helper_RadList
 * @extends Zend_View_Helper_Abstract
 * @author Martin Santangelo
 */
class View_Helper_RadList extends Zend_View_Helper_Abstract
{
    /**
     * Retorna el javascript necesario para construir una grilla de datos apuntada a un dataGateway 
     *
     * @param string $modelName
     * @param string $controllerUrl
     * @param string $variableName
     * @param string $config
     * @param string $gridType 'editor' para grilla editable, '' grilla normal
     * @param string $fetch Funcion, sin la palabra fetch, a la que se hara el query  
     * @return string
     */
    public function RadList($modelName, $controllerUrl, $variableName = "grid", $config = "", $gridType = '', $fetch = '', $iniSection = 'default')
    {
        if ($fetch) {
            $fetch = "/fetch/$fetch/";
        } else {
			$fetch = '';
		}
	
		
        if ($variableName) $output->id = $variableName;
        $output->xtype      = "radlistpanel";
        $output->filters    = true;
		$output->url        = $controllerUrl;
		$output->fetch      = $fetch;
		$output->model		= $modelName;
        $output->forceFit   = true;
        $output->stateful   = false;
		$output->iniSection = $iniSection;
        
        if (!$iniSection) $iniSection = 'default';
		
        // Si hay configuracion en el ini la incluimos
        $iniConfig  = Rad_CustomFunctions::loadIniConfig(RAD_GRIDDATAGATEWAY_MODEL_INI_PATH."$modelName.ini", $iniSection."Grid");
        if ($iniConfig) {
            $config = Rad_CustomFunctions::mergeConf($config, $iniConfig);
        }
       
        // Incluimos la configuracion pasada tambien como parametro. Esta tiene presedencia sobre las otras!
        foreach ($config as $key => $value)
        {
            $output->$key = $value;
        }
		
		//$output = Rad_CustomFunctions::mergeConf( objectToArray($output), objectToArray($config));
        
        //result encoded in JSON
        $json = Zend_Json::encode($output, false, array('enableJsonExprFinder' => true));
        return $json;
    }
}
