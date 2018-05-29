<?php
class Rad_Window_Controller_Exception extends Zend_Exception 
{
    protected $path;
    
    public function Rad_Window_Controller_Exception($msg, $path="")
    {
        parent::__construct($msg);
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
}