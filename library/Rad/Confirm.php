<?php

/**
 * Rad_Confirm
 *
 * @category   Rad
 * @package    Util
 * @copyright  Copyright (c) 2009 Smart Software
 */
class Rad_Confirm
{

    public static function confirm($msg, $uid, $options=null)
    {
        $answer = self::getAnswer($uid);

        if ($answer === null) {
            throw new Rad_ConfirmationException($msg, $uid, $options);
        }

        return $answer;
    }

    public static function getAnswer($uid)
    {
        if (!function_exists('getallheaders')) {
            function getallheaders()
            {
                $all_headers=array();

                foreach($_SERVER as $name => $value){

                    if(substr($name,0,5)=='HTTP_'){

                        $name=substr($name,5);
                        $name=str_replace('_',' ',$name);
                        $name=strtolower($name);
                        $name=str_replace(' ', '-', $name);

                        $all_headers[$name] = $value; 
                    }
                    elseif($function_name=='apache_request_headers'){

                        $all_headers[$name] = $value; 
                    }
                }
                return $all_headers;
            }
        }

        $headers = getallheaders();

        if ($headers['confirm']) {
            $resp = json_decode($headers['confirm']);

            if ($resp->uid == md5($uid)) {
                return $resp->data;
            }
        }
        return null;
    }
}