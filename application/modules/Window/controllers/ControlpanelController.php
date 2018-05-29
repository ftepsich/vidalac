<?php

require_once 'Rad/Window/Controller/Action.php';

define('DescktopBaseChannel', '/desktop/modules/');

class Window_ControlPanelController extends Rad_Window_Controller_Action
{

    protected $title = 'Panel de Control';
    
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
        $this->subMenu = $request->getParam('panel');

        $model = new Model_DbTable_MenuesPrincipales(array(), true);

        $db = Zend_Registry::get('db');
        $sub = $db->quote($this->subMenu, 'INTEGER');

        $panel = $model->find($sub)->current();

        if (!$panel)
            throw new Rad_Exception('No se encontro el panel ' . $this->subMenu);

        $this->title = $panel->Texto;

        $windowWidth = ($panel->PanelAncho) ? $panel->PanelAncho : '500';
        $windowHeight = ($panel->PanelAlto) ? $panel->PanelAlto : '300';

        // TODO usar fetchMenu
        $rowset = $model->getStartMenu($sub);

        $shortcutPanel = "{xtype:'panel', bodyStyle:'margin:10px;', border:false, autoScroll:true, html:\"";

        foreach ($rowset as $row) {
            $modulo = ( $row['Nombre'] ) ? $row['Nombre'] : 'SubMenu' . $row['menuId'];
            if ($row['TienePanel']) {
                $icono = ( file_exists("images/modulos/{$row['Icono']}64.png") ) ? $row['Icono'] : 'defaultFolder';
                $row['Url'] = 'Window/controlpanel/index/panel/' . $row['menuId'];
            } else {
                $icono = ( file_exists("images/modulos/{$row['Icono']}64.png") ) ? $row['Icono'] : 'default';
            }

            // TODO ver de hacer con un dataview
            $shortcutPanel .= "<div id='shortcut-{$modulo}' ondblclick=\\\"app.publish('" . DescktopBaseChannel . $row['Url'] . "',{action:'launch'});\\\" class='thumb-wrap ux-shortcut-item-btn' style='padding: 15px 25px 15px 0px; margin: 4px 10x 10x 10x; background: transparent url(images/modulos/{$icono}64.png) no-repeat top center'><div class='thumb ux-shortcut-btn' style='padding-left: 10px'><img title='{$row['Texto']}' src='images/s.gif'><span class='x-editable noshadow ux-shortcut-btn-text '>{$row['Texto']}</span></div></div>";
        }
        $shortcutPanel .= "\"}\n";

        $this->view->shortcuts = $shortcutPanel;
        $this->view->width = intval($windowWidth);
        $this->view->height = intval($windowHeight);
    }

}