<?php

/**
 * ConfiguracionController
 *
 * Ventana de Configuracion del escritorio para cada usuario
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class ConfiguracionController
 * @extends Rad_Window_Controller_Action
 */
class ConfiguracionController extends Rad_Window_Controller_Action
{
    protected $title = 'Configuración de Escritorio';

    public function authorize($request)
    {
        return true;
    }

    protected function getFolder()
    {
        return APPLICATION_PATH . '/../public/wallpapers';
    }
    
    public function initWindow()
    {
        $config->tpl          = '{Texto}';
        $config->buildToolbar = new Zend_Json_Expr('function(){}');
        $config->ddGroup      = 'MenuShortcut';
        $config->enableDD     =  true;

        $this->view->tree = $this->view->radTree(
            'Model_DbTable_MenuesPrincipales',
            'MenuesPrincipales1',
            $config
        );
    }

    protected function _generateThumb($file)
    {
        $archivo   = $this->getFolder().'/thumbs/'.$file;
        $original  = $this->getFolder().'/'.$file;

        if (!file_exists($archivo) && file_exists($original)){
            $imagine = new Imagine\Gd\Imagine();

            $mode    = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
            $size    = new Imagine\Image\Box(200, 200);

            $imagine->open($original)
                ->thumbnail($size, $mode)
                ->save($archivo);
        }
        
        return $archivo;
    }

    public function getwallpapersAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $rowset = array();

        if ($walldir = opendir($this->getFolder())) {

            while (false !== ($entrada = readdir($walldir))) {
                
                if ($entrada == '.' || $entrada == '..' || !is_file($this->getFolder().'/'.$entrada)) continue;
                $this->_generateThumb($entrada);
                $rowset[] = array(
                    'thumb' => '/wallpapers/thumbs/'.$entrada,
                    'image' => '/wallpapers/'.$entrada,
                    'name'  => $entrada
                );
            }

            closedir($walldir);
        }

        $rtn = array(
            'success' => true,
            'files'   => $rowset,
            'count'   => count($rowset)
        );

        echo json_encode($rtn);
    }
}