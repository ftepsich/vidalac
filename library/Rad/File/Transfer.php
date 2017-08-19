<?php
require_once ('Zend/File/Transfer.php');

class Rad_File_Transfer extends Zend_File_Transfer
{
    public function __construct($adapter = 'Http', $direction = false, $options = array())
    {
		parent::setAdapter($adapter, $direction, $options);
		$this->addValidator('ExcludeExtension', false, array('php', 'php3', 'php4', 'exe', 'bat', 'com'))
			 ->addValidator('Count', false, array('min' =>1, 'max' => 1));
    }
}