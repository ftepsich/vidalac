<?php

/**
 * Cron_Abstract
 *
 * clase base para las tareas a ejecutar desde el cron.php
 *
 * @package     Aplicacion
 * @subpackage  Process
 * @copyright   SmartSoftware Argentina 2012
 * @author      Martin Santangelo
 */
abstract class Cron_Abstract
{

    protected $_runner;

    public function __construct(CronRunner $runner)
    {
        $this->_runner = $runner;
    }

    public function getName()
    {
        $r = get_class($this);
        return str_replace('Cron_', '', $r);
    }

    public function log($t)
    {
        $this->_runner->log($this->getName().': '.$t);
    }

    public function logError($t)
    {
        $this->_runner->logError($this->getName().': '.$t);
    }

    /**
     * metodo que se ejectua al llamar la tarea implementar en las subclases!!!
     */
    abstract public function run();


}