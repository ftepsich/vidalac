<?php

class Rad_Db_Table_ValidatorException extends Rad_Db_Table_Exception
{

    protected $_fieldsErrors;

    public function __construct($msg, $fieldsErrors, $model)
    {
        $this->message = $msg;
        $this->model = $model;
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