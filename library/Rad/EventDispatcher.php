<?php

/**
 * Despachador Simple de Eventoas
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Rad_EventDispatcher
 * @author Martin Alejandro Santangelo
 */
class Rad_EventDispatcher
{
    protected $_eventsSubscribers = array();

    public function __construct(array $events)
    {
        // init callback collections
        foreach ($events as $name) {
            $this->_eventsSubscribers[$name] = new Rad_CallbackCollection;
        }
    }

    protected function _eventExist($n)
    {
        if (!array_key_exists($n, $this->_eventsSubscribers)){
            throw new Rad_Exception("El evento $n no existe");
        }
    }

    public function on($event, $callback)
    {
        $this->_eventExist($event);
        $this->_eventsSubscribers[$event]->append($callback);
    }

    public function clearListeners($event)
    {
        $this->_eventExist($event);
        $this->_eventsSubscribers[$event] = new Rad_CallbackCollection;
    }

    public function fire() {
        $args  = func_get_args();
        $event = array_shift($args);
        $this->_eventExist($event);

        $e = $this->_eventsSubscribers[$event];

        call_user_func_array($e, $args);
    }
}