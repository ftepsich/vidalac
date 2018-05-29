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
class Contable_MovimientosMensualController extends Rad_Window_Controller_Action
{
    protected $title = 'Estadisticas Comprobantes Mensual';
    
    public function initWindow ()
    {
        $model = new Contable_Model_FacturacionMensual();
        $anios = $model->getAniosConMovimientos();
        $aniosCombo = count($anios) ? $anios : array(array('anio' => date('Y')));

        // Todos los años para el combo
        $this->view->aniosCombo = $aniosCombo;
        // Año por default. El primero, si existen en el combo, el año actual, si no existen
        $this->view->anio = $aniosCombo[0]['anio'];
    }

    public function getAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $modelFacturacionMensual = new Contable_Model_FacturacionMensual();

        $anio = (int) $this->getRequest()->anio;

        $data = $modelFacturacionMensual->getMovimientosAnio($anio);

        $rtn['rows'] = $data;
        $rtn['count'] = count($data);
        $rtn['success'] = true;

        $json = $this->getHelper('Json');
        $json->suppressExit = true;
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        $json->getResponse()->sendResponse();
        $json->sendJson($rtn, array('enableJsonExprFinder' => true));
    }

}
