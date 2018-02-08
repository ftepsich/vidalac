<?php
require 'Worker.php';

class MailWorker extends Worker
{
    protected $options = array('queue' => 'mail');
}

$worker = new MailWorker;

$worker->run();