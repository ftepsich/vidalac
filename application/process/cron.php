<?php
require 'ProcessAbstract.php';
/**
 * Corre un proceso de cron pasado por parametro de linea de comando
 */
class Cron extends ProcessAbstract
{

    const PHP_PATH = '/usr/local/zend/bin/';

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
        try {
            $db = Zend_Registry::get('db');

            $hora      = date('H:i');
            $diaSemana = date('w');
            $diaMes    = date('j');

            // Traigo las tareas diarias
            $diarias = $db->fetchAll(
                "select CT.Script FROM CronProgramaciones CP JOIN CronTareas CT on CT.Id = CP.CronTarea where CP.Tipo = 1 and CP.Hora = '$hora:00'"
            );
            // Traigo las semanales
            $semanales = $db->fetchAll(
                "select CT.Script FROM CronProgramaciones CP JOIN CronTareas CT on CT.Id = CP.CronTarea where CP.Tipo = 2 and CP.Hora = '$hora:00' and CP.Dia = $diaSemana"
            );

            // Traigo las mensuales
            $mensuales = $db->fetchAll(
                "select CT.Script FROM CronProgramaciones CP JOIN CronTareas CT on CT.Id = CP.CronTarea where CP.Tipo = 3 and CP.Hora = '$hora:00' and CP.Dia = $diaMes"
            );



            // Ejecuto
            foreach ($diarias as $value) {
                $this->runTask($value['Script']);
            }

            foreach ($semanales as $value) {
                $this->runTask($value['Script']);
            }

            foreach ($mensuales as $value) {
                $this->runTask($value['Script']);
            }

        } catch( Exception $e ){
            $this->logError($e->getMessage());
        }
    }

    /**
     * Corre una tarea en un proceso a parte
     */
    public function runTask($name)
    {
        // echo self::PHP_PATH."php -f ".__DIR__.DIRECTORY_SEPARATOR."CronRunner.php $name";
        $this->log("Ejecutando $name");

        $c = Rad_Cfg::get('/configs/proccess.yml');

        system($c->php_path."php -f ".__DIR__.DIRECTORY_SEPARATOR."CronRunner.php $name", $retval);

        if ($retval === false) {
            $this->logError("cron: La tarea $name no termino correctamente.");
        }
    }
}

// instanciamos y ejecutamos
$cron = new Cron;

$cron->run();