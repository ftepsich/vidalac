<?php
require 'Abstract.php';

/**
 * Publica el cierre de una Factura
 */
class Cron_dummy extends Cron_Abstract
{
    public function run()
    {
        $this->log('Estoy corriendo...');
    }
}