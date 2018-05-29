<?php
/**
 * Rad_Window_Controller_Abstract
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Window_Controller
 * @author Martin Alejandro Santangelo
 */
require_once 'Abstract.php';

/**
 * Esta Clase implementa una ventana dentro del Desktop
 * 
 * @author Martin Alejandro Santangelo
 * @package Rad
 * @subpackage Window_Controller
 */
class Rad_Window_Controller_Action extends Rad_Window_Controller_Abstract
{
    public function init()
    {
        if (APPLICATION_ENV != 'development') {
            $cache = Zend_Registry::get('slowCache');
            if ($cache) {
                $this->setCache($cache);
            }
        }
//        $this->_helper->cache(array('index'), array('indexAction'));

        parent::init();
    }
}
    
