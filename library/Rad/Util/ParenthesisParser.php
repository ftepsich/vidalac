<?php
/**
 * Parsea parentesis y retorna un array con la jerarquia de los mismos
 * 
 *
 * @package     Rad
 * @subpackage  Util
 * @class       Rad_Util_ParenthesisParser
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Rad_Util_ParenthesisParser
{
    var $buf = array();

    const NAME_EXP = '[a-z][a-z0-9_]*';

    function put_to_buf($x) {
        $this->buf[] = $x[0];
        return '@' . (count($this->buf) - 1) . '@';
    }

    function get_from_buf($x) {
        return $this->buf[intval($x[1])];
    }

    function replace_all($re, $str, $callback) {
        while(preg_match($re, $str))
            $str = preg_replace_callback($re, array($this, $callback), $str);
        return $str;
    }

    function run($text) {
        $this->replace_all('~\([^\(\)]*\)~', $text, 'put_to_buf');
        foreach($this->buf as &$s)
            $s = $this->replace_all('~@(\d+)@~', $s, 'get_from_buf');
        return $this->buf;
    }
}
