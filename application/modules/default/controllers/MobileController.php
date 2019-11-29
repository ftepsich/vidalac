<?php
/**
 * Mobile Controler
 * @author Martin A. Santangelo
 */
class MobileController extends Zend_Controller_Action
{
    public function init ()
    {
		/* Initialize action controller here */
    }
    
    public function indexAction ()
    {
        if (! Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('mobile', 'auth');
            return;
        }
        
        $this->view->usuario = $this->view->escape(Zend_Auth::getInstance()->getIdentity()->Nombre);
    }
}

