<?php

require_once 'Rad/Window/Controller/Action.php';

class Develop_BuildModelsController extends Rad_Window_Controller_Action
{

    protected $title = "Creador de Modelos";

    public function indexAction()
    {
        // if (Rad_Confirm::confirm( 'De verdad queres entrar?', _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
        //     Rad_Log::debug('Si queres');
        // } else {
        //     Rad_Log::debug('No queres');
        // }
        parent::indexAction();
    }

    public function gettablesAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $db = Zend_Registry::get('db');
        $tables = $db->listTables();
        foreach ($tables as $k => $table) {
            $json[] = "{Id:'$table',tableName:'$table'}";
        }
        $json = implode(',', $json);
        $count = count($tables);
        echo "{rows:[$json],count:$count,success:true}";
    }

    /**
     * 	Crea los registros que falten en la tabla modelos a partir de las tablas de la base de datos
     */
    public function updatemodelAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $modelos = new Model_DbTable_Modelos();
        $modelos->updateFromTables();
        echo "{success:true}";
    }

    public function buildmodelAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $rq = $this->getRequest();

        $buildModel = new Develop_Model_ModelFromTable();
        try {
            if (!$rq->sobrescribir) {
                if ($buildModel->modelExists($rq->table)) {
                    echo "{success:false, msg:'No puede crearse el modelo por que ya existe'}";
                    return;
                }
                if ($buildModel->iniExists($rq->table)) {
                    echo "{success:false, msg:'No puede crearse el ini por que ya existe'}";
                    return;
                }
            }
            $buildModel->generateModel($rq->table);
            $buildModel->generateIni($rq->table);
        } catch (Develop_Model_ModelFromTable_Exception $e) {
            echo "{success:false,msg:\"" . $e->getMessage() . "\"}";
        }
    }

}