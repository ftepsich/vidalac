<?php
require __DIR__.'/Jobs/Job.php';

/**
 * Wraper para DJJob
 *
 * Esta clase solo esta para inicializar la configuracion de DJJob en el momento de necesitarse
 */
class Rad_Jobs
{
    const JOB_STATUS_FAILED   = 1;
    const JOB_STATUS_WAITING  = 2;
    const JOB_STATUS_FINISHED = 3;
    const JOB_STATUS_RUNNING  = 4;

    private static $config;

    /**
     * Encola un proceso para ser ejecutado por un Worker
     *
     * @param  Object $job   Objecto proceso
     * @param  string $queue cola
     *
     * @return int           id de proceso
     */
    public static function enqueue($job, $queue = 'default', $start = null)
    {
        self::init();

        return Rad_Jobs_Job::enqueue($job, $queue, $start);
    }

    /**
     * ejectua un proceso
     *
     * @param  [type]  $job    [description]
     * @param  boolean $unique [description]
     * @return [type]          [description]
     */
    public static function execute($job){
        self::init();
        // lo encolo en la cola $execute$ reservada para procesos q se ejecutan al encolarlos
        $cola = uniqid('execute', true);
        $id   = Rad_Jobs_Job::enqueue($job, $cola);

        $c = Rad_Cfg::get('/configs/proccess.yml');

        $command = $c->php_path.'php -f '.APPLICATION_PATH."/process/Workers/GenericWorker.php $cola 1 1";

        // ejecuto el comando en background
        exec($command." > ".APPLICATION_PATH."/../logs/genericworker.log &", $retval);

        if ($retval === false) {
            throw new Rad_Exception('Error al ejecutar el proceso');
        }
        return $id;
    }

    public static function jobStatus($id)
    {
        self::init();
        $rs = Rad_Jobs_Job::runQuery(
            "SELECT attempts, failed_at, error, locked_at FROM Jobs WHERE id = ?",
            array($id)
        );

        if (count($rs) == 1) {
            $r = $rs[0];
            if ($r['error']) return array('status' => self::JOB_STATUS_FAILED, 'error' => $r['error']);
            if ($r['attempts'] == 0 && !$r['locked_at']) return array('status' => self::JOB_STATUS_WAITING);
            return array('status' => self::JOB_STATUS_RUNNING);
        }
        return array('status' => self::JOB_STATUS_FINISHED);
    }

    /**
     * Inicializa la conexion con la DB
     * y setea el usuario al proceso de background por si se ejecuta codigo q necesite
     * Zend_Auth::getInstance()->getIdentity()
     */
    public static function init()
    {
        if (!self::$config) {
            $appconf = Zend_Registry::get('config');
            $c = $appconf['resources']['db']['params'];

            self::$config = "mysql:host={$c['host']};dbname={$c['dbname']};port=3306";

            if(php_sapi_name() != "cli") {
                $u = Zend_Auth::getInstance()->getIdentity()->Id;
            } else {
                $u = 0;
                Rad_Jobs_Job::setPreRun(function($t){
                    $user = $t->user;
                    if (!$user) {
                        return;
                    }

                    $auth = Zend_Auth::getInstance();
                    // para no levantar el de session q es mas codigo al dope innesesario para entorno cli
                    $auth->setStorage(new Zend_Auth_Storage_NonPersistent);

                    $s    = $auth->getStorage();
                    $m    = new Model_DbTable_Usuarios;

                    $usuario = $m->fetchRow('Id = '.$user);

                    $s->write((object)$usuario->toArray());
                });
            }

            Rad_Jobs_Job::configure(self::$config, array(
                'mysql_user' => $c['username'],
                'mysql_pass' => $c['password'],
                'user'       => $u
                )
            );
        }
    }
}