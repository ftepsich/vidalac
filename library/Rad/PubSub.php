<?php
/**
 * Rad_PubSub
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage PubSub
 * @author Martin Alejandro Santangelo
 */

/**
 * Rad_PubSub
 *
 * Implementacion del patron Publish/Subscribe
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage PubSub
 * @author Martin Alejandro Santangelo
 */
class Rad_PubSub
{
    /**
     * @var array de subscriptores
     */
    static protected $_suscribers;

    /**
     * @var array de despachadores de eventos adicionales
     */
    static protected $_dispatchers = array();

    /**
     * @var Zend_Cache
     */
    static protected $_cache;

    /**
     * @var string
     */
    static protected $_file;

    /**
     * @var bool
     */
    private static $_loaded = false;
    
    /**
     * Setea el cache para la configuracion de subscribers
     * @param Zend_Cache
     */
    static public function setCache( $cache )
    {
        self::$_cache = $cache;
    }

    /**
     * inicializa
     *
     * @param string $file nombre del archivo de configuracion
     */
    static public function init($file = null)
    {
        self::$_file = $file;
        // cargo los subscriptores desde el archivo
        self::_load();
    }

    /**
     * Carga los subscriptores desde un archivo de configuracion dado por self::$_file
     */
    static protected function _load()
    {
        if (self::$_loaded) return;

        if (self::$_cache) {
            self::$_suscribers = self::$_cache->load('Rad_PubSub_Subscribers');
        }

        // si no hay nada cargo el ini si envio como parametro
        if (empty(self::$_suscribers)) {

            if (self::$_file) {

                $config = Rad_Cfg::get(self::$_file);

                // subscrivo todos los eventos
                foreach ($config->toArray() as $evento => $suscribers) {
                    foreach ($suscribers as $subsc) {
                        foreach ($subsc as $model => $method) {
                            self::subscribe($evento, $model, $method);
                        }
                    }
                }

                self::saveToChache();
            }
        }
        
        self::$_loaded = true;
    } 

    static public function saveToChache()
    {
        if (self::$_cache && is_array(self::$_suscribers)) {
            self::$_cache->save(self::$_suscribers,'Rad_PubSub_Subscribers');
        }
    }

    /**
     * Agregar un despachador de eventos
     * Estos reciven todos los eventos junto a sus argumuentos luego de ser ejecutados todos los
     * subscriptores.
     *
     * @param string $handler nombre metodo de la clase contexto que recibira las peticiones
     */
    static public function addDispatcher($context, $handler = null)
    {
        if ($handler) {
            self::$_dispatchers[] = array($context, $handler);
        } else {
            self::$_dispatchers[] = $context;
        }
    }
    
    /**
     * Subscribe un obj al evento $topic
     *
     * @param string $topic nombre del ini
     * @param string $context nombre de la clase contexto
     * @param string $handler nombre metodo de la clase contexto que recibira las peticiones
     */
    static public function subscribe($topic, $context, $handler = null)
    {
        $jerarquia = explode('/', $topic);

        $ref = &self::$_suscribers;

        foreach ($jerarquia as $j) {
            
            if (!$j) continue;

            if ( !isset( $ref[$j] ) ) {
                $ref[$j] = array(
                    'subscribers' => array(),
                    'childs'      => array()
                );
            }

            $topicRef = &$ref[$j];

            $ref = &$ref[$j]['childs'];
        }

        if ($handler) {
            // self::$_suscribers[$topic][] = array($context, $handler);
            $topicRef['subscribers'][] = array($context, $handler);
        } else {
            // self::$_suscribers[$topic][] = $context;
            $topicRef['subscribers'][] = $context;
        }

    }

    /**
     * solo usada para debug
     * @return array
     */
    static public function getSubscribers()
    {
        return self::$_suscribers;
    }

    /**
     * Quita la Subscripcion de un obj
     *
     * @param string $topic nombre el evento
     * @param string $context nombre de la clase contexto
     * @param string $handler nombre metodo de la clase contexto que recibira las peticiones
     */
    static public function unsubscribe($topic, $context, $handler = null)
    {
        if ($handler) {
            $h = array($context, $handler);
        } else {
            $h = $context;
        }

        $jerarquia = explode('/', $topic);

        $ref = &self::$_suscribers;

        foreach ($jerarquia as $j) {

            if (!$j) continue;

            $topicRef = &$ref[$j];

            $ref = &$ref[$j]['childs'];
        }

        if (false === ($index = array_search($h, $topicRef['subscribers']))) {
            return false;
        }

        unset($topicRef['subscribers'][$index]);

        return true;
    }
    
    /**
     * Emite un evento
     *
     * @param string $topic nombre del evento
     * @param array $argv array de parametros a enviar
     */
    static public function publish($topic, $argv = null)
    {

		
        // Rad_Log::debug(self::$_suscribers);

        $subscribers = self::getTopicSubscribers($topic);
        
        if (empty($subscribers)) {
            return;
        }

        $return = null;
        $argv   = func_get_args();
        array_shift($argv);
        
        foreach ($subscribers as $handler) {
            $callback = self::getCallback($handler);
            $return = call_user_func_array($callback, $argv);
        }

        foreach (self::$_dispatchers as $handler) {
            
            $callback = self::getCallback($handler);
            array_unshift($argv, $topic);
            $return = call_user_func_array($callback, $argv);
        }
        return $return;
    }

    /**
     * Retorna los un array con los subscriptores a un mensaje dado
     * 
     * @param string $topic evento
     * @return array
     */
    static private function getTopicSubscribers($topic)
    {
        $subscribers = array();

        $jerarquia = explode('/', $topic);

        $ref = &self::$_suscribers;

        foreach ($jerarquia as $j) {

            if (!$j) continue;

            if ( !isset( $ref[$j] ) ) {
                break;
            }

            if ( !empty($ref[$j]['subscribers']) ) {
                $subscribers = array_merge($subscribers, $ref[$j]['subscribers']);
            }

            $ref = &$ref[$j]['childs'];
        }

        return $subscribers;
    }
    
    /**
     * Emite un evento hasta que $untilCallback retorne true
     *
     * @param mixed $untilCallback nombre del metodo callback
     * @param string $topic nombre del evento
     * @param array $argv array de parametros a enviar
     */
    static public function publishUntil($untilCallback, $topic, $argv = null)
    {
        $subscribers = self::getTopicSubscribers($topic);
        
        if (empty($subscribers)) {
            return;
        }

        $return = null;
        $argv   = func_get_args();
        array_shift($argv);
        
        foreach ($subscribers as $handle) {
            $callback = getCallback($handler);
            $return   = call_user_func_array($callback, $args);
            if (call_user_func($untilCallback, $return)) {
                break;
            }
        }
        foreach (self::$_dispatchers as $handler) {
            $callback = self::getCallback($handler);
            array_unshift($argv, $topic);
            $return = call_user_func_array($callback, $argv);
            if (call_user_func($untilCallback, $return)) {
                break;
            }
        }
        return $return;
    }
    
    /**
     * Emite un evento hasta que $untilCallback retorne true
     *
     * @param string $handler nombre del metodo
     */
    static protected function getCallback($handler)
    {
        if ( is_array($handler) ) {
            if ( is_string($handler[0]) ) {
                $class = $handler[0];
                $callback[0] = new $class();
                $callback[1] = $handler[1];
            } else {
                if (!is_object($handler[0])) {
                    throw new Rad_PubSub_Exception('Contexto para el callback Invalido debe ser una Clase o un objeto');
                }
                $callback = array($handler[0], $handler[1]);
            }
        } else {
            $callback = $handler;
        }
        
        if (!is_callable($callback)) {
            throw new Rad_PubSub_Exception("PubSub: Error el callback '{$handler[0]}, {$handler[1]}' no existe");
        }
        return $callback;
    }
}