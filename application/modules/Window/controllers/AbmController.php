<?php

require_once 'Rad/Window/Controller/Action.php';

/**
 * Controlador generico ABM
 */
class Window_AbmController extends Rad_Window_Controller_Action
{
    protected $grid = null;
    protected $modelName = '';
    protected $modelIni  = 'default';
    protected $model;
    protected $module;
    protected $iniConfig;
    protected $fetch = '';

    /**
     * Arma la grilla usando el view helper radGrid
     */
    protected function buildGrid()
    {
        $parametrosAdc->iniSection    = $this->modelIni;
        $parametrosAdc->autoSave      = true;
        $parametrosAdc->withRowEditor = false;
        $parametrosAdc->module        = $this->module;
        $parametrosAdc->fetch         = $this->fetch;
        $parametrosAdc->loadAuto      = false;

        //$parametrosAdc->abmForm         = new Zend_Json_Expr($ambForm);
        $parametrosAdc->view = new Zend_Json_Expr("
            new Ext.grid.GroupingView ({
                enableNoGroups: false,
                forceFit: true,
                hideGroupedColumn: true,
                groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Registros\" : \"Registro\"]})'
            })");

        $this->grid = $this->view->radGrid($this->modelClass, $parametrosAdc, 'abmeditor', $this->modelIni);
    }

    public function authorize($request)
    {
        $db = Zend_Registry::get('db');

        $acl = new Rad_ModelAcl($db);

        return $acl->allowView($this->modelClass);
        /*
        // No es mas simple como esta arriba?
        if (!$acl->allowView($this->modelClass)) {
            return false;
        } else {
            return true;
        }
        */
    }

    public function getformAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $ambForm = $this->view->radForm($this->modelName, "default/datagateway", $this->modelIni);
        echo $ambForm;
    }

    public function init()
    {
        $this->idMicroTime = microtime(true) * 10000;

        $request = $this->getRequest();
        $this->modelName = $request->getParam('model');
        $this->fetch     = $request->getParam('fetch');
        $this->module    = $request->getParam('m');
        $modelIni        = $request->getParam('section');
        
        if ($modelIni) {
            $this->modelIni = $modelIni;
        }
        
        $prefix = ($this->module == 'default' || !$this->module) ? '' : (ucfirst($this->module) . '_');

        $this->modelClass = $prefix . 'Model_DbTable_' . $this->modelName;

        parent::init();
    }

    protected function _getRenderScriptName()
    {
        return "window/abm.js";
    }

    protected function getName()
    {
        return parent::getName() . $this->modelName . $this->modelIni;
    }

//    public function indexAction()
//    {
//        $db = Zend_Registry::get('db');
//
//        $acl = new Rad_ModelAcl($db);
//        if (!$acl->allowView($this->modelClass)) {
//            $this->_forward('denied', 'error', 'default');
//        } else {
//            return parent::indexAction();
//        }
//    }

    public function initWindow()
    {
        $iniConfig = Rad_GridDataGateway_ModelMetadata::getModelClassIni($this->modelClass);
        $this->iniConfig = $iniConfig[$this->modelIni . 'AbmWindow'];
        //$this->iniConfig = Rad_GridDataGateway_ModelMetadata::getModelClassIni($this->modelClass, $this->modelIni . "AbmWindow");
        $this->title = str_replace("_", " ", $this->modelName);
        
        $this->view->AbmWinCfg = Zend_Json::encode($this->iniConfig, false, array('enableJsonExprFinder' => true));

        $this->buildGrid();

        $width  = ($this->iniConfig['width']) ? $this->iniConfig['width'] : 800;
        $height = ($this->iniConfig['height']) ? $this->iniConfig['height'] : 500;
        
        //Rad_Log::debug('requires: '. $iniConfig['module']['requires']);
        $this->view->requires = Zend_Json::encode($iniConfig['module']['requires'], false);
        
        $this->view->grid = $this->grid;
        $this->view->windowWidth = $width;
        //$this->windowsObj->setMinWidth($width);
        $this->view->windowHeight = $height;
        //$this->windowsObj->setMinHeight($height);
    }

}
