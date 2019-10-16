<?php

/**
 * ErrorController
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Desktop
 * @author Martin Alejandro Santangelo
 */
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Controller_Action');


/**
 * ErrorController
 * Controlador de errores del sistema
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Desktop
 * @author Martin Alejandro Santangelo
 */
class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Página no encontrada';
                if ($isAjax) {
                    $this->_helper->viewRenderer->setNoRender(true);
                    echo 'Página no encotrada';
                }
                break;
            default:
				$req = $this->getRequest();
                Rad_ErrorHandler::handleException($errors->exception, $req);
				
				// si la llamada anterior no corta la ejecucion y manda la respuesta renderizo el template estandar de error
				$this->view->exception = $errors->exception;
				$this->view->message = $errors->exception->getMessage();
        }
    }

    protected function _sendConfirmationError($exception)
    {
        $this->getResponse()->setHttpResponseCode(506);
        $msg = new stdClass();
        $msg->msg = addslashes($exception->getMessage());
        $msg->uid = $exception->getUid();
        $msg->options = $exception->getOptions();
        $this->_sendJsonResponse($msg);
    }

    /**
     * Envia la respuesta json sin romper los logs de firephp
     */
    protected function _sendJsonResponse($data)
    {

        // no puedo enviar la cabecera  json/javascript porque el formulario al tener fielUpload: true no funciona
        $this->_helper->viewRenderer->setNoRender(true);
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        $data = Zend_Json::encode($data, null, array('enableJsonExprFinder' => true));
        echo $data;
    }

    /**
     * Este action es usado para enviarle a las peticiones ajax el codigo 505 usado por el manejador
     * de errores de js para reloguearse
     */
    public function reloginAction()
    {
        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $this->getResponse()->setHttpResponseCode(505);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function deniedAction()
    {
        self::sendDenied();
    }

    public function getajaxerrorAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $errorNamespace = new Zend_Session_Namespace('Rad_Error_Handler');
        $errorCount = trim($this->getRequest()->getParam("number"));
        
        if ($errorCount == null) {
            echo "<h1>Error no especificado<h1>";
        } else {
            echo $errorNamespace->$errorCount;
        }
    }
}
