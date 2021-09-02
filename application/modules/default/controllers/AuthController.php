<?php

/**
 * AuthController
 *
 * @author Martin A. Santangelo
 * @version
 */
require_once 'Zend/Controller/Action.php';

class AuthController extends Zend_Controller_Action
{
  
    /**
     * Muestra el formulario de login
     */
    public function indexAction()
    {
        $response = $this->getResponse();
        $response->setHeader('content-type', 'application/x-javascript; charset=utf-8');
    }

    public function mobileAction()
    {
        //$this->_forward("loginjs");
    }

    /**
     * Login de usuarios
     *
     */
    public function loginAction()
    {
        // Desactivamos el layout y el view
        $this->_helper->viewRenderer->setNoRender(true);
        // Get a reference to the singleton instance of Zend_Auth
        $auth = Zend_Auth::getInstance();
        // Set up the authentication adapter
        $bootstrap = $this->getInvokeArg('bootstrap');
        $dbAdapter = $bootstrap->getResource('db');

        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $username = $this->getRequest()->getParam('login');
        $password = password_hash($this->getRequest()->getParam('password'), PASSWORD_DEFAULT, array('salt' => '754CC93A968B7F919C1C6477457F3') );

        $authAdapter->setTableName('Usuarios')
                ->setIdentityColumn('Nombre')
                ->setCredentialColumn('ClaveHash')
                ->setIdentity($username)
                ->setCredential($password);

        //$authAdapter = new MyAuthAdapter( $this->getRequest()->getParam('login'), $this->getRequest()->getParam('password'));
        $response = new stdClass();
        // Attempt authentication, saving the result
        if (!$this->_checkHardware()) {
            $response->success = false;
            $response->msg = "Este software no fue licenciado para este servidor.<br> SmartSoftware sera notificado.";
        } else {
            $result = $auth->authenticate($authAdapter);
            switch ($result->getCode()) {
                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                /** do stuff for nonexistent identity * */
                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    $response->success = false;
                    $response->msg = "Usuario o contraseña invalidos!";
                    Rad_Log::warn("Usuario $username error autenticacion");
                    break;
                case Zend_Auth_Result::SUCCESS:


                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(
                        null,
                        'Clave'
                    ));

                    $response->success = true;
                    $response->usuario = Zend_Auth::getInstance()->getIdentity()->Nombre;

                    Rad_Log::user("Usuario $username autenticado");

                    break;
                default:
                    /** do stuff for other failure * */
                    $response->success = false;
                    $response->msg = "Error desconocido!";

                    break;
            }
        }

        echo json_encode($response);
    }

    /**
     * Agregar en /etc/sudoers !!!
     * www-data  ALL=NOPASSWD: /usr/sbin/dmidecode
     *
     *
     * @return unknown_type
     */
    public function gethwidAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $hwId = shell_exec("sudo /usr/sbin/dmidecode |grep 'Serial Number:'");
        echo md5($hwId);
    }



    /**
     * logout de usuarios
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_helper->redirector('index','desktop');
    }

    /**
     * logout de usuarios
     */
    public function logoutmobileAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_helper->redirector('mobile');
    }

}
