<?php
/**
 * Task_Abstract
 *
 * clase base para las tareas a ejecutar por linea de comando
 *
 * @package     Aplicacion
 * @subpackage  Process
 * @copyright   SmartSoftware Argentina 2012
 * @author      Martin Santangelo
 */
abstract class Task_Abstract
{
    /**
     * metodo que se ejectua al llamar la tarea implementar en las subclases!!!
     */
    public function run()
    {
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    abstract public function printHelp();
}
