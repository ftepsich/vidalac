<?php

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_GreaterOrEqThan extends Zend_Validate_Abstract
{

    const NOT_GREATER = 'notGreaterThan';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_GREATER => "'%value%' is not greater than '%min%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_min'
    );

    /**
     * Minimum value
     *
     * @var mixed
     */
    protected $_min;

    /**
     * Sets validator options
     *
     * @param  mixed $min
     * @return void
     */
    public function __construct($min)
    {
        $this->setMin($min);
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  mixed $min
     * @return Zend_Validate_GreaterThan Provides a fluent interface
     */
    public function setMin($min)
    {
        $this->_min = $min;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is greater than min option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if ($this->_min > $value) {
            $this->_error(self::NOT_GREATER);
            return false;
        }
        return true;
    }

}
