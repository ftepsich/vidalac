<?php

/**
 * Rad_Db_Table_Filter_Abstract
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table_Filter
 * @author Martin Alejandro Santangelo
 */
abstract class Rad_Db_Table_Filter_Abstract
{

	/**
	 * @var array operadores validos
	 */
    protected $_operators = array();

	/**
	 * @var Db adapter
	 */
    protected $_db = NULL;
	
	/**
     * @var array mapa de operadores de SQL
     */
    protected $_opSqlMap = array();
    
    /**
     * @var string property this filter is applied to
     */
    protected $_field = NULL;
    
    /**
     * @var operador a usar para filtrar
     */
    protected $_operator = NULL;
    
    /**
     * @var mixed valor a filtrar por
     */
    protected $_value = NULL;
    
    /**
     * @var array de opciones especiales
     */
    protected $_options = NULL;
	
	/**
     * Constructor de la clase
     *
     * @param string $_field
     * @param string $_operator
     * @param mixed  $_value    
     * @param array  $_options
     */
    public function __construct($_field, $_operator, $_value, $db, array $_options = array())
    {
		if (!($db instanceof Zend_Db_Adapter_Abstract)) throw new Rad_Db_Table_Filter_Exception('no se especifico el adaptador');
        $this->_setOptions($_options);
        $this->setField($_field);
        $this->setOperator($_operator);
        $this->setValue($_value);
		$this->_db = $db;
    }
	
	/**
     * setea las opciones
     *
     * @param array $_options
     */
    protected function _setOptions(array $_options)
    {
		$this->_options = $_options;
    }
    
    /**
     * setea el campo a filtrar
     *
     * @param string $_field
     */
    public function setField($_field)
    {
        $this->_field = $_field;
    }
    
    /**
     * retorna el campo a filtrar
     *
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }
    
    /**
     * gets operator
     *
     * @return  string
     */
    public function getOperator()
    {
        return $this->_operator;
    }
	
	/**
     * set operator
     *
     * @param  string
     */
    public function setOperator($op)
    {
        if (!in_array($op, $this->_operators)) throw new Rad_Db_Table_Filter_Exception("El operador $op no esta soportado en ".get_class($this));
		$this->_operator = $op;
    }
    
    
    /**
     * sets value
     *
     * @param mixed $_value
     */
    public function setValue($_value)
    {
        if (is_array($_value) && array_key_exists('id', $_value)) {
            $_value = $_value['id'];
        }
        
        //@todo validate value before setting it!
        $this->_value = $_value;
    }

    /**
     * gets value
     *
     * @return  mixed 
     */
    public function getValue()
    {
        return $this->_value;
    }
	
	abstract public function appendFilterSql($_select);
	
	protected function _getQuotedFieldName($_backend) {
        return $this->_db->quoteIdentifier($this->_field);
    }
	
	/**
     * replaces wildcards
     * 
     * @param  string $value
     * @return string
     */
    protected function _replaceWildcards($value)
    {
        if (is_array($value)) {
            $returnValue = array();
            foreach ($value as $idx => $val) {
                $returnValue[$idx] = $this->_replaceWildcardsSingleValue($val);
            }
        } else {
            $returnValue = $this->_replaceWildcardsSingleValue($value);
        }
        
        return $returnValue;
    }
    
    /**
     * replaces wildcards of a single value
     * 
     * @param  string $value
     * @return string
     */
    protected function _replaceWildcardsSingleValue($value)
    {
        $action = $this->_opSqlMap[$this->_operator];
        
        $returnValue = str_replace(array('*', '_'), array('%', '\_'), $value);
        
        $returnValue = str_replace('?', $returnValue, $action['wildcards']);
        
        return $returnValue;
    }
}