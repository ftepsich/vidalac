<?php
/**
 * TinebaseRad_Db_Table_Filter_Date
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table_Filter
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Filter_Date extends Rad_Db_Table_Filter_Abstract
{
    /**
     * @var array list of allowed operators
     */
    protected $_operators = array(
        0 => 'equals',
        1 => 'within',
        2 => 'before',
        3 => 'after',
        4 => 'isnull',
        5 => 'notnull',
        6 => 'inweek'
    );
    
    /**
     * @var array maps abstract operators to sql operators
     */
    protected $_opSqlMap = array(
        'equals'     => array('sqlop' => ' LIKE ?'),
        'within'     => array('sqlop' => array(' >= ? ', ' <= ?')),
        'before'     => array('sqlop' => ' < ?'),
        'after'      => array('sqlop' => ' > ?'),
        'isnull'     => array('sqlop' => ' IS NULL'),
        'notnull'    => array('sqlop' => ' IS NOT NULL'),
        'inweek'     => array('sqlop' => array(' >= ? ', ' <= ?')),
    );
    
    /**
     * date format string
     *
     * @var string
     */
    protected $_dateFormat = 'yyyy-MM-dd';
    
    /**
     * appends sql to given select statement
     *
     * @param Zend_Db_Select                $_select
     * @param Tinebase_Backend_Sql_Abstract $_backend
     */
     public function appendFilterSql($_select)
    {
        // prepare value
        $value = substr($_value, 0, 10);
         
        // quote field identifier
        $field = $this->_getQuotedFieldName();
         
        // append query to select object
        foreach ((array)$this->_opSqlMap[$this->_operator]['sqlop'] as $num => $operator) {
            if (get_parent_class($this) === 'Tinebase_Model_Filter_Date') {
                $_select->where($field . $operator, $value[$num]);
            } else {
                $_select->where("DATE({$field})" . $operator, $value[$num]);
            }
        }
    }
}