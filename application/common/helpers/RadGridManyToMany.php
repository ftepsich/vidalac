<?php
require_once 'Rad/CustomFunctions.php';

/**
 * Crea el Json para una Grilla de datos RadGrid
 */
class View_Helper_RadGridManyToMany extends Zend_View_Helper_Abstract
{
    public function RadGridManyToMany($modelClass, $intersectionClass, $matchClass, $config = "", $iniSection = 'default', $middleIniSection = 'default')
    {
        $modelInfo        = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($modelClass);
        $intersectionInfo = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($intersectionClass);
        $matchInfo        = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($matchClass);
        
        $config = Rad_CustomFunctions::objectToArray($config);

        $output = array (
            'xtype'            => "radformmanytomanygridpanel",
            'filters'          => true,
            'loadAuto'         => false,
            'forceFit'         => true,
            'iniSection'       => $iniSection,
            'model'            => $modelInfo['model'],
            'module'           => $modelInfo['module'],
            'middleIniSection' => $middleIniSection,
            'id'               => $variableName,
            'url'              => "/default/datagateway",
            'saveUrl'          => "/default/datagateway/savemanytomany/",
            'baseParams'       => array(
                'model'              => $modelInfo['model'],
                'm'                  => $modelInfo['module'],
                'intersectionModel'  => $intersectionInfo['model'],
                'intersectionModule' => $intersectionInfo['module'],
                'matchModel'         => $matchInfo['model'],
                'matchModule'        => $matchInfo['module'],
            )
        );
        
        $iniConfig = Rad_GridDataGateway_ModelMetadata::getModelClassIni($modelClass, $iniSection . "Grid");
        if ($iniConfig) {
            $config = Rad_CustomFunctions::mergeConf($config, $iniConfig);
        }
        
        $output = Rad_CustomFunctions::mergeConf($output, $config);
        //result encoded in JSON
        $json = Zend_Json::encode($output, false, array('enableJsonExprFinder' => true));
        return $json;
    }
}
