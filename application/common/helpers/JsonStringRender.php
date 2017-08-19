<?php

/**
 * JsonStringRender
 *
 * @package Rad
 * @subpackage ViewHelpers
 * @class View_Helper_JsonStringRender
 * @extends Zend_View_Helper_Abstract
 * @author Martin Santangelo
 */
class View_Helper_JsonStringRender extends Zend_View_Helper_Abstract
{
    public function JsonStringRender($string, $data)
    {
        if (!isset($data['JsonParametrosAdicionales'])) $data['JsonParametrosAdicionales'] = '';
        
        
	    foreach ($data as $k => $item)
	    {
	       	$string = str_replace("<<$k>>",$item,$string);
	    }
        return $string;
    }
}
