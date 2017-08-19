<?php
require_once 'Rad/CustomFunctions.php';

/**
 * Crea el Json para una Grilla de datos RadGrid
 *
 * @package Rad
 * @subpackage ViewHelpers
 * @class View_Helper_RadGrid
 * @extends Zend_View_Helper_Abstract
 * @author Martin Santangelo
 */
class View_Helper_RadGrid extends Zend_View_Helper_Abstract {
    /**
     * Retorna el javascript necesario para construir una grilla de datos apuntada a un dataGateway
     *
     * @param string $modelClass
     * @param string $config
     * @param string $gridType 'editor' para grilla editable, '' grilla normal
     * @param string $iniSection Section del ini que se usara @deprecated
     * @return string
     */
    public function RadGrid($modelClass, $config = null, $gridType = null, $iniSection = 'default') {

        $info = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($modelClass);

        $config = Rad_CustomFunctions::objectToArray($config);

        // TODO: estandarizar los controladores, usar el iniSection solo de $config, sacar el parametro de este metodo
        if ($config['iniSection'])
            $iniSection = $config['iniSection'];

        $output = array (
            'xtype'      => "rad{$gridType}gridpanel",
            'filters'    => true,
            'url'        => '/default/datagateway',
            'model'      => $info['model'],
            'module'     => $info['module'],
            'forceFit'   => true,
            'stateful'   => false,
            'iniSection' => ($config && $config->iniSection) ? $config->iniSection : $iniSection,
        );

        // Si hay configuracion en el ini la incluimos
        $iniConfig = Rad_GridDataGateway_ModelMetadata::getModelClassIni($modelClass, $iniSection . "Grid");
        // Rad_Log::debug(array('iniConfig'=>$iniConfig));
        if ($iniConfig) {
            $config = Rad_CustomFunctions::mergeConf($config, $iniConfig);
        }

        // si es una grilla Abm editor le agregamos el formulario si no lo tiene y la configuracion de la ventana
        if ($gridType == 'abmeditor') {
            if ( !array_key_exists('abmForm', $config) ) {
                $config['abmForm'] = new Zend_Json_Expr($this->view->radForm($modelClass, '/default/datagateway', $iniSection));
            }
        }

        // Incluimos la configuracion pasada tambien como parametro. Esta tiene presedencia sobre las otras!
        $output = Rad_CustomFunctions::mergeConf($output, $config);

        //result encoded in JSON
        $json = Zend_Json::encode($output, false, array('enableJsonExprFinder' => true));

        return $json;
    }
}
