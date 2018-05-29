<?php

/**
 * Contable_ResumenVentasMensualController
 *
 * Ventas por Articulo Mensual
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_ResumenVentasMensualController
 * @extends Rad_Window_Controller_Action
 */
class Contable_ResumenVentasMensualController extends Rad_Window_Controller_Action
{
    protected $title = 'Resumen de Ventas Mensual';
    
    public function initWindow()
    {
        
    }

    public function getAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $mes = $request->getParam('mes');
        $anio = $request->getParam('anio');
        
        if (!$anio || !$mes) {
            throw new Rad_Db_Table_Exception('Faltan los parametros requeridos.');
        }

        $model = new Contable_Model_VentasMensual();
        $data = $model->getResumenVentasMensual($anio, $mes);

        foreach ($data as &$row) {
            $row['Cantidad'] = (int)$row['Cantidad'];
        }
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
