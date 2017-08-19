<?php

class Rad_Db_Table_CalculatedColumn_Exception extends Rad_Db_Table_Exception
{

    public function __construct($msg, $model)
    {
        parent::__construct($msg, array(), $model);
    }
}