<?php

/**
 * Administrador de colas de impresion del CUPS
 * 
 * @author Martin A. Santangelo
 * @version 
 */
require_once 'Zend/Controller/Action.php';

class ColaimpresionController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrador de Impresiones';

    private $_impresoras;

    public function init()
    {
        $cfg = Rad_Cfg::get();

        $this->_impresoras = new Model_Impresoras($cfg->Facturacion->Preimpreso->Cups);

        parent::init();
    }

    public function initWindow()
    {
        $this->view->impresoras = json_encode($this->_impresoras->getImpresoras());
    }


    /**
     * Obtiene la lista de trabajos de una impresora
     */
    public function getjobsAction()
    {
        require_once 'PrintIpp/CupsPrintIPP.php';

        $this->_helper->viewRenderer->setNoRender(true);

        $impresora = $this->getRequest()->getParam('impresora');

        try {
            $rows = $this->_impresoras->getJobs($impresora);
        
            $ret = array (
                'rows'    => $rows,
                'count'   => count($row),
                'success' => true
            );

            $this->_sendJsonResponse($ret);
        } catch (Exception $e) {
            $this->_sendJsonResponse(array('success' => false, 'msg' => $e->getMessage()));
        } 
        
        
    }

    public function canceljobAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $uri = $this->getRequest()->getParam('trabajo');

        try {
            $rows = $this->_impresoras->cancelJob($uri);
            $this->_sendJsonResponse(array('success' => true));
        } catch (Exception $e) {
            $this->_sendJsonResponse(array('success' => false, 'msg' => $e->getMessage()));
        } 
    }
}