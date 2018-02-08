<?php

class Rad_Db_Table_Exception extends Rad_Exception
{

    protected $_fieldsErrors;

    public function __construct($msg, $fieldsErrors = array(), $model = null)
    {
        $this->message       = $msg;
        $this->model         = $model;
        $this->_fieldsErrors = $fieldsErrors;
        $this->updateMessage();
    }

    protected function updateMessage()
    {
        foreach ($this->getFieldsErrors() as $field => $errors) {
            foreach ($errors as $error) {
                $this->message .= "<br>$field: $error";
            }
        }
    }

    public function getFieldsErrors()
    {
        return $this->_fieldsErrors;
    }

}