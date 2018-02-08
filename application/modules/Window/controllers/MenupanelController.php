<?php

require_once 'Rad/Window/Controller/Action.php';

define('DesktopBaseChannel', '/desktop/modules/');

class Window_MenuPanelController extends Rad_Window_Controller_Action
{

    protected $title = 'Panel de Menu';
    
    public function authorize($request)
    {
        return true;
    }

    public function init()
    {
        $this->_disableCache = true;
        parent::init();
    }

    public function initWindow()
    {
        $request = $this->getRequest();
        $this->subMenu = $request->getParam('menu');

        $identity = Zend_Auth::getInstance()->getIdentity();

        $model = new Model_DbTable_MenuesPrincipales(array(), true);

        $db = Zend_Registry::get('db');
        $sub = $db->quote($this->subMenu, 'INTEGER');

        $panel = $model->find($sub)->current();

        if (!$panel)
            throw new Rad_Exception('No se encontro el menu ' . $this->subMenu);

        $this->title = $panel->Texto;

        $windowWidth = ($panel->PanelAncho) ? $panel->PanelAncho : '500';
        $windowHeight = ($panel->PanelAlto) ? $panel->PanelAlto : '300';

        // TODO usar fetchMenu (cuando ande :P)
        $rowset = $model->getStartMenu($sub);
                
        $fastCache = Zend_Registry::get('fastCache');
        $permisosMenu = $fastCache->load('User_AllowedMenu_'.$identity->GrupoDeUsuario);
        
        $menuData = array();
        foreach ($rowset as $row) {
            if (in_array($row['menuId'], $permisosMenu)) {
                $modulo = ( $row['Nombre'] ) ? $row['Nombre'] : 'SubMenu' . $row['menuId'];
                if ($row['TienePanel']) {
                    $icono = 'defaultFolder';
                    $row['Url'] = 'Window/menupanel/index/menu/' . $row['menuId'];
                } else {
                    $icono = ( file_exists("images/modulos/{$row['Icono']}64.png") ) ? $row['Icono'] : 'default';
                }

                $menuData[] = array(
                    'id'        => $row['menuId'],
                    'modulo'    => $modulo,
                    'url'       => $row['Url'],
                    'icono'     => $icono,
                    'texto'     => $row['Texto']
                );
            }
        }
        
        $this->view->menuData = Zend_Json::encode(array('menu' => $menuData));
        $this->view->width = intval($windowWidth);
        $this->view->height = intval($windowHeight);
    }

}
