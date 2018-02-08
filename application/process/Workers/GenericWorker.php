<?php
require 'Worker.php';

/**
 * Worker generico
 *
 * Uso:
 *  (para cantidad de iteraciones ilimitadas count = 0)
 *  php GenericWorker.php cola [count] [max_attemp] [sleep]
 *
 *  php GenericWorker.php mails 2 5 5
 *
 */
class GenericWorker extends Worker
{
    public function __construct()
    {
        parent::__construct();

        $count  = 0;
        $sleep  = 5;
        $attemp = 5;

        $argv = $_SERVER["argv"];

        if (@$argv[1]) $queue  = $argv[1];
        if (@$argv[2]) $count  = $argv[2];
        if (@$argv[3]) $attemp = $argv[3];
        if (@$argv[4]) $sleep  = $argv[4];


        $this->options = array(
            'queue'        => $queue,
            'count'        => $count,
            'sleep'        => $sleep,
            'max_attempts' => $attemp
        );
    }
}

$worker = new GenericWorker;

$worker->run();