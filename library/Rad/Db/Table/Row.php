<?php

require 'Zend/Db/Table/Row/Abstract.php';

/**
 * Clase row para la Rad_Db_Table
 *
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
class Rad_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
    protected $_calcFields = array();

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        
        /**
         * Fix para que guarde los valores default
         */
        if (!isset($config['stored']) || $config['stored'] === false) {
            $this->_modifiedFields = $this->_data;
        }

        // veo los campos calculados
        if ($this->_table) {
            $this->_calcFields = new ArrayObject($this->_table->getLocalCalculatedFields());
        }

    }

     /**
     * Retrieve row field value
     * 
     * Agrego calculados solo bajo demanda
     *
     * @param  string $columnName The user-specified column name.
     * @return string             The corresponding column value.
     * @throws Zend_Db_Table_Row_Exception if the $columnName is not a column in the row.
     */
    public function __get($columnName)
    {
        if (!empty($this->_calcFields) && array_key_exists($columnName, $this->_calcFields)) {
            $calc = @$this->_calcFields[$columnName];
            
            if (!$calc) throw new Rad_Exception('El Campo calculado no existe en el Row'); 
            
            return $this->_getCalcFieldValue($calc);
        }
        return parent::__get($columnName);
    }

    /**
     * Agrega un campo calculado seteando su valor directamente
     */
    public function setCalculatedField($name, $value) { 
        $this->_calcFields[$name] = $value;
    }

    public function getCalculatedField($name)
    {
        return $this->_calcFields[$name];
    }


    protected function _getCalcFieldValue($calc)
    {
        if (is_object($calc)) {
            return $calc->getColumnValue($this);
        } else {
            return $calc;   
        }
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = (array) $this->_data;
        
        foreach ($this->_calcFields as $name => $calc) {
            $data[$name] = $this->_getCalcFieldValue($calc);
        }

        return $data;
    }
}
