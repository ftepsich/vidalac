<?php
//TODO: Cambiar esto para que ahora envie los errores por Ajax
class Window_ErrorController extends Zend_Controller_Action
{
    public function errorAction ()
    {
        //$this->_helper->viewRenderer->setNoRender(true);
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                //$this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Pagina no econtrada';
                break;
            default:
                // application error 
                //$this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $errors->exception->getMessage();
                if ($errors->exception instanceof  RADWindow_Controler_Exception) {
                    $this->view->path = $errors->exception->getPath();
                }
                break;
        }
    }
}

