<?php

/**
 * Liquidacion_Model_VariableCollection
 *
 * Coleccion de Variables
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_VariableCollection
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_VariableCollection implements IteratorAggregate, Countable
{
    private $_variables = array();

    public function __construct($variables = array())
    {
        $this->_variables = $variables;
    }

    public function reduce($f) {
        $rv = 0;
        foreach ($this as $v) {
            $rv = $f($rv, $v->getResultado());
        }
        return $rv;
    }

    public function sum()
    {
        return $this->reduce(function($a, $b) {
            return $a+$b;
        });
    }

    public function add(Liquidacion_Model_Variable $v)
    {
        if ($this->has($v)) {
            throw new Liquidacion_Model_VariableCollection_Exception('ya esta agregada la variable '.$v->getNombre());
        }

        $this->_variables[$v->getNombre()] = $v;
    }

    public function has(Liquidacion_Model_Variable $v)
    {
        return array_key_exists($v->getNombre(), $this->_variables);
    }

    public function hasByName($name)
    {
        return array_key_exists($name, $this->_variables);
    }

    public function remove(Liquidacion_Model_Variable $v)
    {
        if ($this->has($v)) {
            unset($this->_variables[$v->getNombre()]);
        }
    }

    public function get($name)
    {
        return @$this->_variables[$name];
    }

    public function filter($f)
    {
        $filtered_collection = new self(iterator_to_array(new CallbackFilterIterator($this->getIterator(), $f)));
        return $filtered_collection;
    }


    /* ITERATOR */

    /**
     * Get an iterator object
     *
     * @return array
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_variables);
    }

    /**
     * Return the number of keys
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_variables);
    }
}