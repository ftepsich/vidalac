<?php
require __DIR__.'/../ProcessAbstract.php';


class Worker extends ProcessAbstract
{
    protected $options = array();

    public function run()
    {
        Rad_Jobs::init();

        $worker = new Rad_Jobs_Worker($this->options);
        $worker->start();
    }
}
