<?php

require_once 'Exception.php';

/**
 * Rad_ConfirmationException
 *
 * @category   Rad
 * @package    Rad_Exception
 * @copyright  Copyright (c) 2009 Smart Software
 */
class Rad_ConfirmationException extends Rad_Exception
{

    protected $_options = array(
        'includeCancel' => false
    );
    protected $_uid;

    function getOptions()
    {
        return $this->_options;
    }

    function getUid()
    {
        return $this->_uid;
    }

    public function __construct($msg, $uid, $options)
    {
        $this->_uid = md5($uid);

        if ($options)
            $this->_options = $options;

        $session = new Zend_Session_Namespace('Rad_Confirm');

        parent::__construct($msg);
    }

}