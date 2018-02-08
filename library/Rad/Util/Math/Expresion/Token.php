<?php
namespace Rad\Util\Math\Expresion;

class Token
{
    private $_type;
    private $_value;

    public function __construct($type,  $value)
    {
        $this->_type  = $type;
        $this->_value = $value;
    }

    public function __toString()
    {
        return $value;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setType($t)
    {
        $this->_type = $t;
    }
}