<?php

/**
 * Contable_FacturacionMensualController
 *
 * FActuracion Mensual
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_FacturacionMensualController
 * @extends Rad_Window_Controller_Action
 */
class Contable_PagosCobrosMensualController extends Rad_Window_Controller_Action
{

    protected $title = 'Pagos y Cobros Mensuales';
    
    public function initWindow()
    {
        
    }

    public function getAction()
    {
        $anio = $this->getRequest()->anio;
        if (!$anio) $anio = date('Y');
        $this->_helper->viewRenderer->setNoRender(true);
        
        $model = new Contable_Model_FacturacionMensual();
        $data = $model->getPagosAnio($anio);
        
        $rtn['rows']  = $data;
        $rtn['count'] = count($data);
        $rtn['success'] = true;

        
        $json = $this->getHelper('Json');
        $json->suppressExit = true;
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        $json->getResponse()->sendResponse();
        $json->sendJson($rtn, array('enableJsonExprFinder' => true));
    }

}
