<?php
/**
 * JsonRender
 *
 * @package Rad
 * @subpackage ViewHelpers
 * @class View_Helper_JsonRender
 * @extends Zend_View_Helper_Abstract
 * @author Martin Santangelo
 */
class View_Helper_JsonRender extends Zend_View_Helper_Abstract
{
    public function JsonRender($fileName, $data)
    {
        if (!file_exists($fileName)) {
            throw new Zend_Exception("No se encontro el archivo $fileName");
        }

        if (!isset($data['JsonParametrosAdicionales'])) {
            $data['JsonParametrosAdicionales'] = '';
        }
        
        $template = file_get_contents($fileName);
        
        foreach ($data as $k => $item) {
           	$template = str_replace("<<$k>>", $item, $template);
        }
        
        return $template;
    }
}