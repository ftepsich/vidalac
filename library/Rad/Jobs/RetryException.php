<?php

/**
 * Excepcion usada para reintentar mas tarde una tarea
 */
class Rad_Jobs_RetryException extends Rad_Jobs_Exception {

    private $delay_seconds = 7200;

    public function setDelay($delay) {
        $this->delay_seconds = $delay;
    }
    public function getDelay() {
        return $this->delay_seconds;
    }
}