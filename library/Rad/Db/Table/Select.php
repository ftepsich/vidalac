<?php

/**
 * Rad_Db_Table_Select
 *
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Select extends Zend_Db_Table_Select 
{
    /**
     * Tests query to determine if expressions or aliases columns exist.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        $readOnly = false;
        $fields   = $this->getPart(Zend_Db_Table_Select::COLUMNS);
        $cols     = $this->_info[Zend_Db_Table_Abstract::COLS];

        if (!count($fields)) {
            return $readOnly;
        }
		
        foreach ($fields as $columnEntry) {
            $column = $columnEntry[1];
            $alias = $columnEntry[2];

            if ($alias !== null) {
                $column = $alias;
            }
            
            switch (true) {
                case ($column == "SQL_CALC_FOUND_ROWS ".$this->_table->getName().'.'.self::SQL_WILDCARD):
                case ($column == self::SQL_WILDCARD):
                    break;

                case ($column instanceof Zend_Db_Expr):
                case (!in_array($column, $cols)):
                    $readOnly = true;
                    break 2;
            }
        }

        return $readOnly;
    }

}
