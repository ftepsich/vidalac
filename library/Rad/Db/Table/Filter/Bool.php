<?php

/**
 * Rad_Db_Table_Filter_Bool
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table_Filter
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Filter_Bool extends Rad_Db_Table_Filter_Abstract
{
    /**
     * @var array list of allowed operators
     */
    protected $_operators = array(
        0 => 'equals',
    );
    
    /**
     * @var array maps abstract operators to sql operators
     */
    protected $_opSqlMap = array(
        'equals'     => array('sqlop' => ' = ?'),
    );
    
    /**
     * appends sql to given select statement
     *
     * @param Zend_Db_Select                $_select
     */
    public function appendFilterSql($_select)
    {
        $action = $this->_opSqlMap[$this->_operator];
		$field  = $this->_getQuotedFieldName();
         
        $db = $_backend->getAdapter();
         
         // prepare value
        $value = $this->_value ? 1 : 0;

        $_select->where($field . $action['sqlop'], $value);
 
    }
}
