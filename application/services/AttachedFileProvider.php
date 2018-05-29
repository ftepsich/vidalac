<?php
/**
 * Service_AttachedFileProvider
 *
 * Se encarga de configurar el proveedor de archivos atachados 
 * que se quiera utilizar para el sistema
 * 
 * @package     Aplicacion
 * @subpackage  Service
 * @author Martin Alejandro Santangelo
 */
class Service_AttachedFileProvider
{
    protected static $_provider;

    public static function getProvider()
    {
        if (!self::$_provider) {
            self::$_provider = new Rad_Db_Table_AttachedFileProvider_Local;

            self::$_provider->setFolder( APPLICATION_PATH . '/../data/attachedFiles' );
        }
        return self::$_provider;
    }
}
    