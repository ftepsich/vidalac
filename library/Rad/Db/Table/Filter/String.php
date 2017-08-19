<?php
/**
 * Rad_Db_Table_Filter_String
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table_Filter
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Filter_String extends Rad_Db_Table_Filter_Abstract
{
	 /**
     * @var array operadores validos
     */
    protected $_operators = array(
        0 => 'equals',
        1 => 'contains',
        2 => 'startswith',
        3 => 'endswith',
        4 => 'not',
        5 => 'in',
        6 => 'notin',
        7 => 'isnull',
        8 => 'notnull'
    );
    
    /**
     * @var array maps abstract operators to sql operators
     */
    protected $_opSqlMap = array(
        'equals'     => array('sqlop' => ' LIKE ?',      'wildcards' => '?'  ),
        'contains'   => array('sqlop' => ' LIKE ?',      'wildcards' => '%?%'),
        'startswith' => array('sqlop' => ' LIKE ?',      'wildcards' => '?%' ),
        'endswith'   => array('sqlop' => ' LIKE ?',      'wildcards' => '%?' ),
        'not'        => array('sqlop' => ' NOT LIKE ?',  'wildcards' => '?'  ),
        'in'         => array('sqlop' => ' IN (?)',      'wildcards' => '?'  ),
        'notin'      => array('sqlop' => ' NOT IN (?)',  'wildcards' => '?'  ),
        'isnull'     => array('sqlop' => ' IS NULL',     'wildcards' => '?'  ),
        'notnull'    => array('sqlop' => ' IS NOT NULL', 'wildcards' => '?'  ),
    );
	
	
	
	 /**
     * Agrega el sql al select
     *
     * @param  Zend_Db_Select $_select
     * @throws Tinebase_Exception_NotFound
     */
    public function appendFilterSql($_select)
    {
        // quote field identifier, set action and replace wildcards
        $field  = $this->_getQuotedFieldName();
        $action = $this->_opSqlMap[$this->_operator];
        $value  = $this->_replaceWildcards($this->_value);

        if (in_array($this->_operator, array('in', 'notin')) && ! is_array($value)) {
            $value = explode(' ', $value);
        }
  
        if (is_array($value) && empty($value)) {
             return;
        }
        
        $where = $this->_db->quoteInto($field . $action['sqlop'], $value);
        
        if ($this->_operator == 'not' || $this->_operator == 'notin') {
            $where = "( $where OR $field IS NULL)";
        }
		
        $_select->where($where);
    }
}