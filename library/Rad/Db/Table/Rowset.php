<?php

require 'Zend/Db/Table/Rowset/Abstract.php';

/**
 * Clase rowset para la Rad_Db_Table
 *
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
class Rad_Db_Table_Rowset extends Zend_Db_Table_Rowset_Abstract
{
    protected $_hasCalculated = false;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        if ($this->_table) {
                $this->_hasCalculated = $this->_table->hasLocalCalculatedFields();
        }
        
    }

    public function __sleep()
    {
        $r = parent::__sleep();
        $r[] = '_hasCalculated';
        return $r;
    }

    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @return array
     */
    public function toArray()
    {
        // $calcFields = $this->_table->getLocalCalculatedFields();

        if ($this->_hasCalculated) {
            foreach ($this->_data as $i => $r) {
                if (@$this->_rows[$i]) {
                    $this->_data[$i] = $row->toArray();
                } else {
                    $this->_data[$i] = $this->_loadAndReturnRow($i)->toArray();
                }
            }
        } 

        return $this->_data;
    }
}