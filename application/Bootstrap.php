<?php
/**
 * Bootstrap
 *
 * Bootstrap de la aplicacion
 *
 * @package     Aplicacion
 * @author Martin Alejandro Santangelo
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('Gestion | Alimentos Vida S.A.');
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $view->addHelperPath(APPLICATION_PATH . '/common/helpers', 'View_Helper');
        $viewRenderer->setView($view);
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
    protected function _initFastCache()
    {
        if (extension_loaded('memcache')) {
            $frontendOptions = array(
                'lifetime'                => 7000,
                'automatic_serialization' => true,
                'ignore_user_abort'       => true
            );
            $backendOptions =  array(
                'servers' => array(
                    array(
                        'host' => '127.0.0.1',
                        'port' => '11211'
                    )
                ),
                'compression' => true
            );
            $cache = Zend_Cache::factory(
                'Core', 'Zend_Cache_Backend_Libmemcached', $frontendOptions, $backendOptions, false, true
            );
        } else {
            $frontendOptions = array(
                'lifetime' => 7000,
                'automatic_serialization' => true,
                'ignore_user_abort' => true
            );
            $backendOptions = array(
            );
            $cache = Zend_Cache::factory(
                'Core', 'Zend_Cache_Backend_File', $frontendOptions, $backendOptions, false, true
            );
        }
        // Hacemos del cache de memoria el cache por defecto de la clase db_table
        if (APPLICATION_ENV == 'production') {
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
            Rad_Acl::setCache($cache);
        }
        Zend_Registry::set('fastCache', $cache);
        return $cache;
    }
    protected function _initPubSub()
    {
        if (APPLICATION_ENV == 'production') {
            $this->bootstrap('FastCache');
            $fastCache = $this->getResource('FastCache');
            Rad_PubSub::setCache($fastCache);
        }
        Rad_PubSub::init('/configs/subscribers.yml');
    }

    protected function _initSlowCache()
    {
        $frontendOptions = array(
            'lifetime' => 7000,
            'automatic_serialization' => true,
            'ignore_user_abort' => true
        );
        $backendOptions = array();
        $cache = Zend_Cache::factory(
            'Core', 'Zend_Cache_Backend_File', $frontendOptions, $backendOptions, false, true
        );
        Zend_Registry::set('slowCache', $cache);
        return $cache;
    }
    public function _initRegistry()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        Zend_Registry::set('db', $db);
        // para poder acceder en forma facil a la configuracion de la aplicacion
        Zend_Registry::set('config', $this->getOptions());
    }
    protected function _initDbProfiler()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        //$db->query('SET NAMES UTF8');    // Fix para MySQL para que retorne en UTF-8
        // solo atachamos el profiler si estamos en modo development
        if (APPLICATION_ENV == 'development') {
            $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
            $db->setProfiler($profiler);
            $profiler->setEnabled(true);
        }
    }

    protected function _initAutoLoad()
    {
        //TODO: ver si esta bien configurado
        $moduleLoader = new Zend_Application_Module_Autoloader(
            array(
                'namespace' => '',
                'basePath'  => APPLICATION_PATH //. '/modules/default'
            )
        );
        return $moduleLoader;
    }
    /*
      protected function _initAcl()
      {
          $this->bootstrap('db');
          $db = $this->getResource('db');
          $acl = Rad_Acl::getInstance($db);
          return $acl;
      } */
}