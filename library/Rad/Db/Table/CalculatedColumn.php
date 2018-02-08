<?php
/**
 * Representa una columna calculada en local, no el la base de datos
 * cada columna contiene un callback que retorna el valor de la misma
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_CalculatedColumn
{
    protected $_name;

    protected $_callback;

    protected $_table;

    public function __construct($name, $callback, Rad_Db_Table $table)
    {
        $this->_name  = $name;

        $this->_table = $table;
        
        if (!is_callable($callback)) {
            throw new Rad_Db_Table_CalculatedColumn_Exception('El callback tiene que ser ejecutable', $table);
        }

        $this->_callback = $callback;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getColumnValue($row)
    {
        $f = $this->_callback;
        return $f($row);
    }
}
