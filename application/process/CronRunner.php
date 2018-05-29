<?php
require 'ProcessAbstract.php';
/**
 * Corre un proceso de cron pasado por parametro de linea de comando
 */
class CronRunner extends ProcessAbstract
{
    protected $_taskName;

    public function __construct($tn)
    {
        $this->_taskName = $tn;
        parent::__construct();
    }

    public function logError($text) {
        error_log(
            date('Y-m-d H:i:s').' '.$text.PHP_EOL,
            3,
            APPLICATION_PATH.'/../logs/crons.error'
        );
    }

    public function log($text) {
        error_log(
            date('Y-m-d H:i:s').' '.$text.PHP_EOL,
            3,
            APPLICATION_PATH.'/../logs/crons.log'
        );
    }

    public function run()
    {
        if ($this->_taskName) {
            $this->runTask($this->_taskName);
        } else {
            $this->logError('CronRunner: No se paso la tarea a Ejecutar!.');
        }
    }

    public function runTask($name)
    {
        $tarea = "Cron_".$name;
        
        if (!file_exists('Cron/'.$name.'.php')) {
            $this->logError('La tarea $name no existe');
        }

        if ((include 'Cron/'.$name.'.php') === false) {
            $this->logError('No se pudo incluir Cron/'.$name.'.php');
            return;
        }

        try {
            $task = new $tarea($this);
            $task->run();
        } catch(Exception $e) {
            $this->logError($e->getMessage());
            return;
        } 
    }
}

// instanciamos y ejecutamos
$cron = new CronRunner($argv[1]);
$cron->run();