<?php
/**
 * Service_TableManager
 *
 * Manager de Instancias de Rad_Db_Table
 *
 * @package     Aplicacion
 * @subpackage  Service
 * @author      Martin Alejandro Santangelo
 */
class Service_TableManager
{
    protected static $_tables = array();

    static public function get($clase, $conf = array(), $withJoins=false)
    {
        $args = func_get_args();
        $key  = self::_key($args);

        if (!array_key_exists($key , self::$_tables)) {
            self::$_tables[$key] = new $clase($conf, $withJoins);
        }

        return self::$_tables[$key];
    }

    static public function clear($clase, $conf = array(), $withJoins=false)
    {
        $args = func_get_args();
        $key  = self::_key($args);
        if (array_key_exists($key , self::$_tables)) {
            unset(self::$_tables[$key]);
            return true;
        }
        return false;
    }


    static public function getCached($clase, $conf = array(), $withJoins=false)
    {
        $model = self::get($clase, $conf, $withJoins);

        $frontendOptions = array(
            'lifetime'                => 7000,
            'automatic_serialization' => true,
            'ignore_user_abort'       => true,
            'cached_entity'           => $model
        );

        $backendOptions = array();

        $cache = Zend_Cache::factory(
            'Class', 'Zend_Cache_Backend_ZendServer_ShMem', $frontendOptions, $backendOptions, false, true
        );

        return $cache;
    }

    static protected function _key($args)
    {
        return md5(serialize($args));
    }
}