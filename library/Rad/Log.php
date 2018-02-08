<?php
/**
 * Rad_Log
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Log
 * @author Martin Alejandro Santangelo
 */

/**
 * Rad_Log
 *
 * EMERG  = 0
 * ALERT  = 1
 * CRIT   = 2
 * ERR    = 3
 * WARN   = 4
 * NOTICE = 5
 * INFO   = 6
 * DEBUG  = 7
 * USER   = 8
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Log
 * @author Martin Alejandro Santangelo
 */
class Rad_Log
{
    /**
     * @var Zend_Log instancia del logger
     */
    public $logger;

    /**
     * @var Rad_Log instancia del singleton
     */
    static protected $instance;

    /**
     *  Constructor de la clase
     */
    public function __construct()
    {
        $this->logger = new Zend_Log();
        $this->logger->addPriority('USER', 8);

        // en testing no hago nada

        $userIdentity = Zend_Auth::getInstance()->getIdentity();

        // Si tenemos usuario logueado guardamos su id en el evento
        if ($userIdentity) {
            $this->logger->setEventItem('user', $userIdentity->Id);
        } else {
            // en testing fijamos el usuario admin si no hay usuario
            $this->logger->setEventItem('user', 1);
        }

        // agregue que solo lo haga en producion o desarrollo sino fallan los unittest
        if (APPLICATION_ENV != 'test') {
            $this->logger->setEventItem('ip', $_SERVER['REMOTE_ADDR']);
        }

        $fileWriter = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../logs/error');
        $fileWriter->addFilter(Zend_Log::WARN);

        // Logueador de eventos de usuarios en la DB
        $columnMapping = array('Usuario' => 'user', 'Evento' => 'message');
        $db = Zend_Registry::get('db');
        $userWriter = new Zend_Log_Writer_Db($db, 'UsuariosLogs', $columnMapping);
        $userWriter->addFilter(new Zend_Log_Filter_Priority(8,'='));

        $format = '%ip% %timestamp% %priorityName% (%user%): %message%' . PHP_EOL;

        $formatter = new Zend_Log_Formatter_Simple($format);

        $fileWriter->setFormatter($formatter);

        $this->logger->addWriter($fileWriter);
        $this->logger->addWriter($userWriter);

        // Si estamos en modo desarrollo enviamos todos los logs al firebug
        if (APPLICATION_ENV == 'development') {
            $firebugWriter = new Zend_Log_Writer_Firebug();
            $firebugWriter->setPriorityStyle(3, 'TRACE');
            $firebugWriter->setPriorityStyle(2, 'TRACE');
            $firebugWriter->setPriorityStyle(1, 'TRACE');

            $this->logger->addWriter($firebugWriter);

            $fileWriterInfo = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../logs/debug');
            $fileWriterInfo->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG,'='));
            $fileWriterInfo->setFormatter($formatter);
            $this->logger->addWriter($fileWriterInfo);
        } else {
            // $config = Rad_Cfg::get();

            // $config = array(
            //     'auth'     => 'login',
            //     'ssl'      => 'tls',
            //     'port'     => 587,
            //     'username' => $config->Mail->usuario,
            //     'password' => $config->Mail->password
            // );

            // $transport = new Zend_Mail_Transport_Smtp($config->Mail->smtp, $config);

            // Zend_Mail::setDefaultTransport($transport);

            // $mail = new Zend_Mail('UTF-8');

            // $mail->setFrom('msantang_78@hotmail.com','msantang78@gmail.com');
            // // Eventos Criticos se envian por mail
            // $mailWriter = new Zend_Log_Writer_Mail($mail);

            // $mailWriter->setSubjectPrependText('SmartSoftware Error Report:');

            // $mailWriter->addFilter(Zend_Log::CRIT);
            // $this->logger->addWriter($mailWriter);
        }
    }

    /**
     * Retorna el logger
     * @return Zend_Log
     */
    static public function getLog()
    {
        return self::getInstance()->logger;
    }


    /**
     * Retorna la instancia del singletons
     *
     * @return Rad_Log
     */
    static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function alert($msg)
    {
        self::getLog()->alert($msg);
    }

    public static function user($msg)
    {
        self::getLog()->user($msg);
    }

    public static function crit($msg)
    {
        self::getLog()->crit($msg);
    }

    public static function debug($msg)
    {
        if (APPLICATION_ENV == 'development') {
            self::getLog()->debug($msg);
        }
    }

    public static function emerg($msg)
    {
        self::getLog()->emerg($msg);
    }

    public static function info($msg)
    {
        self::getLog()->info($msg);
    }

    public static function notice($msg)
    {
        self::getLog()->notice($msg);
    }

    public static function err($msg)
    {
        self::getLog()->err($msg);
    }

    public static function warn($msg)
    {
        self::getLog()->warn($msg);
    }

}