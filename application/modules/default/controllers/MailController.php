<?php

/**
 * MailController
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Desktop
 * @author Martin Alejandro Santangelo
 */
class MailController extends Zend_Controller_Action {

    public function init ()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }
    }
    /**
     * Envia un mail
     */
    public function sendAction() {
        $request = $this->getRequest();
        $destino = $request->Destino;
        $asunto = $request->Asunto;
        $cuerpo = $request->Cuerpo;

        $validator = new Zend_Validate_EmailAddress();
        if (!$validator->isValid($destino)) {
            throw new Rad_Exception('La direccion de mail no es correcta');
        }

        try {
            $mail = new Model_Mail($destino, $asunto, $cuerpo);

            Rad_Jobs::enqueue($mail, 'mail');
            
            $msg['success'] = true;

            $this->_helper->json->sendJson($msg);
        } catch (Exception $e) {
            $msg['success'] = false;
            $msg['msg'] = $e->getMessage();
            $this->_helper->json->sendJson($msg);
        }
    }

}

