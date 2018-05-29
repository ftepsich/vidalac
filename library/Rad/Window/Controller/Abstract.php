<?php
/**
 * Rad_Window_Controller_Abstract
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Window_Controller
 * @author Martin Alejandro Santangelo
 */

/**
 * Esta Clase provee la funcionalidad para crear ventana dentro del Desktop
 * 
 * @author Martin Alejandro Santangelo
 * @package Rad
 * @subpackage Window_Controller
 */
class Rad_Window_Controller_Abstract extends Zend_Controller_Action
{
    const cacheNameSpace = 'Rad_Window_Controller';
    /**
     * Desactiva el cache del controlador
     * @var <boolean>
     */
    protected $_disableCache = false;
    protected $title = 'Titulo';
    /**
     *   Tipo de modulo
     * 	@var string
     */
    protected $moduleType = 'app';
    /**
     *   Tooltip
     * 	@var string
     */
    protected $tooltip = 'Tooltip';
    /**
     * Zend_Cache usado para cachear la salida del IndexAction (Javascript de construccion de la ventana)
     * @var Zend_Cache
     */
    protected $cache = null;
    protected $cacheContent = null;

    /**
     * @param $cache the $cache to set
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return the $cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return unknown
     */
    public function getModuleType()
    {
        return $this->moduleType;
    }

    /**
     * @return unknown
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return unknown
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * @param unknown_type $moduleType
     */
    public function setModuleType($moduleType)
    {
        $this->moduleType = $moduleType;
    }

    /**
     * @param unknown_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param unknown_type $tooltip
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
    }

    /**
     * inicializa el controlador
     */
    public function init()
    {
        // Si hay cache configurado intentamos leer primero los metadatos de el

        if ($this->cache && !$this->_disableCache) {
            $this->cacheContent = $this->cache->load($this->getName());
            if ($this->cacheContent)
                return;
        }

        parent::init();
        $this->initWindow();
    }

    public function initWindow()
    {

    }

    /**
     * Retorna el nombre del modulo para el escritorio
     * 
     * Tener en Cuenta que debe ser un nombre unico!!!
     */
    protected function getName()
    {
        return self::cacheNameSpace.'_'.$this->_request->getControllerName();
    }

    /**
     * Retorna el Scirpt para armar la ventana
     */
    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

//		$this->getResponse()->setHeader('Content-Type', 'text/javascript');
//		$this->getResponse()->setBody( $this->_render() );
        // Lo envio con echo para poder capturarlo desde el Zend_Cache_Static

        header('Content-Type: text/javascript');
        
        // Minimizamos el JS
        if (APPLICATION_ENV != 'development') {
            
            $filter = new Rad_Filter_Minify_Javascript_JsMin();
            echo $filter->filter($this->_render());
            
        } else {
            echo $this->_render();
        }
    }

    protected function _getRenderScriptName()
    {
        $controllerName = $this->_request->getControllerName();
        return "window/$controllerName.js";
    }

    /**
     * renderiza el script correspondiente al controlador usando
     * el metodo _getRenderScriptName() para saber el nombre del template.
     * En caso de no existir el template usa window.phtml
     */
    protected function _render()
    {
        // Si esta cacheada ya devolvemos el contenido del cache
        if ($this->cacheContent) {
            return $this->cacheContent;
        }

        $this->view->name = $this->getName();
        $this->view->title = $this->getTitle();
        //$this->view->moduleType  = $this->moduleType;
        //$this->view->tooltip     = $this->tooltip;

        $scriptName = $this->_getRenderScriptName();
        $file = $this->view->getScriptPath($scriptName);
        if (file_exists($file)) {
            $output = $this->view->render($scriptName);
        } else {
            $this->view->setBasePath(APPLICATION_PATH . '/modules/default/views');
            $output = $this->view->render('window/window.phtml');
        }

        // Si hay cache configurado guardamos los metadatos
        if ($this->cache) {
            $this->cache->save($output, $this->getName(), array("Rad_Window_Controller"));
        }
        return $output;
    }
    
    protected function _sendJsonResponse($data)
    {
        $json = $this->getHelper('Json');
        $json->suppressExit = true;
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        $json->getResponse()->sendResponse();
        $json->sendJson($data, array('enableJsonExprFinder' => true));
    }

    /**
     * PreDispach
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch()
    {
        $request = $this->getRequest();
        $auth = Zend_Auth::getInstance();

        //Zend_Wildfire_Plugin_FirePhp::send($request->getControllerName());
        $module = $request->getModuleName();

        $dbAdapter = Zend_Registry::get('db');

        $identity = $auth->getIdentity();
        // role is a column in the user table (database)
        $isAllowed = false;

        // No es administrador?
        if ($identity->GrupoDeUsuario != 1) {
            try {
                if (method_exists($this, 'authorize')) {
                    $isAllowed = $this->authorize($request);
                } else {
                    $acl = Rad_Acl::getInstance($dbAdapter);
                    $isAllowed = $acl->isAllowed(
                        $identity->GrupoDeUsuario,
                        $module.'/'.$request->getControllerName(),
                        $request->getActionName()
                    );
                }
            } catch (Zend_Acl_Exception $e) {
                $isAllowed = false;
            }

        } else { // Si es Admin siempre tiene permiso
            $isAllowed = true;     
        }
        if (!$isAllowed) {
            throw new Rad_Window_Controller_NotAllowedException('Ud. no tiene permiso para acceder a esta pantalla.');
        }
    }
}
