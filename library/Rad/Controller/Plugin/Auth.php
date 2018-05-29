<?php
/**
 * Rad_Controller_Plugin_Auth
 *
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Controller_Plugin
 * @author Martin Alejandro Santangelo
 */

/**
 * Rad_Controller_Plugin_Auth
 *
 * Este plugin verifica la autenticacion del usuario y si tiene permisos para el controlador que esta llamando
 * usando Rad_Acl
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Controller_Plugin
 * @author Martin Alejandro Santangelo
 */
class Rad_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{

    /**
     * PreDispach
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $loginController = 'auth';
        $loginAction     = 'index';

        // Verificamos que el usuario este logueado al sistema, sino lo enviamos al login

        $auth = Zend_Auth::getInstance();

        $isAjax = $request->isXmlHttpRequest();

        // If user is not logged in and is not requesting login page
        // - redirect to login page.

        if (!$auth->hasIdentity() && $request->getControllerName() != $loginController && !in_array($request->getControllerName(), array('desktop','error'))) {
            if ($isAjax) {
                $this->_request->setModuleName('default')
                 ->setControllerName('error')
                 ->setActionName('relogin')
                 ->setDispatched( TRUE );
            } else {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoSimpleAndExit($loginAction, 'desktop');
            }
        }
    }
}
