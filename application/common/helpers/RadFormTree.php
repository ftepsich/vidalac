<?php

require_once 'Rad/CustomFunctions.php';
require_once 'Rad/DbFieldToExtMapper.php';
//require_once '../application/common/helpers/RadForm.php';
require_once APPLICATION_PATH.'/common/helpers/RadForm.php';

/**
 * View_Helper_RadFormTree
 *
 * @package Rad
 * @subpackage ViewHelpers
 * @class View_Helper_RadFormTree
 * @extends Zend_View_Helper_Abstract
 * @author MAximiliano Churichui
 */
class View_Helper_RadFormTree extends View_Helper_RadForm
{
    //protected $_renderView = 'form/.phtml';
    protected $_extComponent = 'Rad.FormTree';

    /**
     * Retorna el javascript necesario para construir un formulario apuntado a un dataGateway 
     *
     * @param string $modelClass    clase del modelo
     * @param string $url           el url del Data gateway al que accedera el formulario
     * @param string $iniSection    indica que configuracion del ini se usarar para los Metadatos
     * @return string
     */
    public function RadFormTree ($modelClass, $url, $iniSection = 'default')
    {
        return View_Helper_RadForm::RadForm($modelClass, $url, $iniSection);
        /*
        $info = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($modelClass);

        $view = clone $this->view;
        $view->modelName = $info['model'];
        $view->moduleName = $info['module'];
        $view->iniSection = $iniSection;

        $model = new $modelClass(array(), true);

        if (!$iniSection)
            $iniSection = 'default';

        // Obtenemos la configuracion del ini
        $config = Rad_GridDataGateway_ModelMetadata::getModelClassIni($modelClass);

        $modelFieldConfig = $config[$iniSection];
        $window =     /*@$config[$iniSection . 'AbmWindow'];

        $metaData = $model->getMetadataWithJoins();
        $itemsAdc = array();

        foreach ($metaData as $field => $fieldMeta) {
            if (!$model->isFieldVisible($field) || @$fieldMeta['JOINED_FIELD'])
                continue;

            $fieldConfig = Rad_DbFieldToExtMapper::getFieldConfig($fieldMeta);

            if (array_key_exists('editor', $modelFieldConfig[$field])) {
                $fieldConfig = ($modelFieldConfig[$field]['editor'] !== null) ? Rad_CustomFunctions::mergeConf($fieldConfig, $modelFieldConfig[$field]['editor']) : null;
            }
            if ($fieldConfig) {
                if (@$fieldMeta['PRIMARY'])
                    $fieldConfig['xtype'] = 'hidden';
                $items[$fieldMeta['COLUMN_NAME']] = Zend_Json::encode($fieldConfig, false, array('enableJsonExprFinder' => true));
            }
        }

        if (isset($config[$iniSection . 'FormGrids'])) {
            $dependenModelClasses = $config[$iniSection . 'FormGrids'];
        } else {
            $depen    /*denModelClasses = array();
        }

        // Si hay grillas embebidas las agregamos
        foreach ($dependenModelClasses as $dependenmodelName => $dependentIniSection) {
            $dependenModelClass = "Model_DbTable_" . $dependenmodelName;
            $dependenModel = new $dependenModelClass(array(), true);

            $reference = $dependenModel->getReference(get_class($model));

            if (!$reference) {
                throw new Zend_Exception("No existe relacion en $dependenModelClass con el modelo " . get_class($model));
            }

            $gridConfig = null;
            $gridConfig->abmForm = new Zend_Json_Expr($this->radForm($dependenmodelName, $dependenmodelName . "formulario", '', $dependentIniSection));
            $gridConfig->abmWindowTitle = $this->title;
            $gridConfig->border = false;
            $gridConfig->withPaginator = false;
            $gridConfig->loadAuto = false;
            $gridConfig->name = $dependenModelClass;
            $gridConfig->parentLocalField = $reference['columns'][0];
            $gridConfig->parentRemoteField = $reference['refColumns'][0];
            $gridConfig->view = new Zend_Json_Expr("
				new Ext.grid.GroupingView({
					enableNoGroups: false, 
					forceFit:View_Helper_RadFormTree true,
					hideGroupedColumn: true,
					groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Registros\" : \"Registro\"]})'
				})
	    	");
            $itemsAdc[$dependenModelClass] = $this->view->radGrid($dependenModelClass, $gridConfig, 'formabmeditor', $dependentIniSection);
        }

        $view->itemsAdc = $itemsAdc;
        $view->items = $items;

        if ($url) {
            $view->controlerurl = "url:'$url/save/model/{$info['model']}/m/{$info['module']}',";
        } else {
            $view->controlerurl = "url: null,";
        }
        return $this->_render($modelName, $iniSection, $view);
        */
    }

    /*
    protected function _render ($modelName, $section, $view)
    {
        $view->setBasePath(APPLICATION_PATH . '/modules/default/views/');
        $view->addHelperPath(APPLICATION_PATH . '/common/helpers', 'View_Helper');
        return $view->render('form/index.phtml');
    }
     */

}