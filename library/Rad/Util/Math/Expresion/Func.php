<?php
namespace Rad\Util\Math\Expresion;


class Func {
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param $name
     * @param $callback
     */
    function __construct($name, $callback)
    {
        $this->name = $name;
        $this->callback = $callback;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCallback()
    {
        return $this->callback;
    }
}