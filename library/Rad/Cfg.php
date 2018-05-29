<?php
/**
 * Rad_Cfg
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Cfg
 * @author Martin Alejandro Santangelo
 */

 /**
 * Rad_Cfg
 * Cargador generico de parametros de la aplicacion
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Cfg
 * @author Martin Alejandro Santangelo
 */
class Rad_Cfg
{

    /**
     * @var instancias de los obj de configuracion (una por archivo)
     */
    static protected $instance = array();

    /**
     * @var formatos soportados y mapeo de extensiones
     */
    static private $_suportedTypes = array(
        'ini' => 'Rad_Config_Ini',
        'yml' => 'Rad_Config_Yaml'
    );


    protected $config;

    private static $_defaultFile = '/configs/conf.ini';

    /**
     * retorna la instancia del objeto
     * @param string $file
     */
    static private function getInstance($file = null)
    {
        if (!$file) $file = self::$_defaultFile;

        if (@self::$instance[$file] === null) {

            @self::$instance[$file] = new self($file);
        }
        return self::$instance[$file];
    }

    /**
     * Retorna la clase del lector para el archivo correspondiente
     *
     * @param string $file
     * @return string
     */
    static protected function getReader($file)
    {
        $info = pathinfo($file);
        $ext  = $info['extension'];
        if (! isset( self::$_suportedTypes[$ext] )) {
            throw new Rad_Exception("Rad_Cfg no soporta leer configuracion de archivos con extension '$ext'");
        }
        $class = self::$_suportedTypes[$ext];

        $reader = new $class(APPLICATION_PATH . $file);

        return $reader;
    }


    public function __construct($file = null)
    {
        if (!$file) $file = self::$_defaultFile;

        $this->config = self::getReader($file);
    }

    /**
     * Retorna la configuracion
     */
    static function get($file = null) {
        return self::getInstance($file)->config;
    }
}