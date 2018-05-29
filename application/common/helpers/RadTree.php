<?php

require_once 'Rad/CustomFunctions.php';

/**
 * Crea el Json para una arbol de datos
 */
class View_Helper_RadTree extends Zend_View_Helper_Abstract
{

    /**
     * Retorna el javascript necesario para construir una grilla de datos apuntada a un dataGateway
     *
     * @param string $modelClass
     * @param string $config
     * @param string $gridType 'editor' para grilla editable, '' grilla normal
     * @param string $fetch Funcion, sin la palabra fetch, a la que se hara el query  
     * @return string
     */
    public function RadTree ($modelClass, $refName, $config = null, $gridType)
    {
        // TODO: Implementar la posibilidad de, a traves de la propuedad
        // treeDisplay en el modelo, poder formatear el campo de visualluzacion
        // en el arbol. Tambien hay que modificar el componente javascript

        $info = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($modelClass);

        if (is_object($config)) {
            $config = Rad_CustomFunctions::objectToArray($config);
        }

        $model = new $modelClass();
        $ref = $model->getReference(get_class($model), $refName);

        $output = array(
            'xtype' => 'radtree',
            'model' => $info['model'],
            'module' => $info['module'],
            'ref' => $refName,
            'display' => $ref['refJoinColumns'][0],
            'parent' => $ref['columns'][0]
        );

        if (!$iniSection)
            $iniSection = 'default';
        
        // Si hay configuracion en el ini la incluimos
        $iniConfig = Rad_GridDataGateway_ModelMetadata::getModelClassIni($modelClass, $iniSection . 'Grid');
        if ($iniConfig) {
            $config = Rad_CustomFunctions::mergeConf($config, $iniConfig);
        }

        // si es una grilla Abm editor le agregamos el formulario si no lo tiene y la configuracion de la ventana
        if ($gridType == 'abmeditor') {
            if (!isset($config['abmForm'])) {
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
