<?php
require_once 'Zend/Controller/Action.php';

define('DesktopBaseChannel', '/desktop/modules/');

/**
 * DesktopController
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Desktop
 * @author Martin Alejandro Santangelo
 */
class DesktopController extends Zend_Controller_Action
{

    public function flushcacheAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Rad_Exception('No se encuentra logueado en el sistema');
        }


        $this->_helper->viewRenderer->setNoRender(true);

        $bootstrap = $this->getInvokeArg('bootstrap');

        $cache = $bootstrap->getResource('fastCache');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        $cache = $bootstrap->getResource('slowCache');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        $msg['success'] = true;
        //$this->_helpers->json->sendJson($msg);
        echo "cache clean";
    }

    /**
     * Retorna la version del Sistema
     */
    public function versionAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        echo $this->getVersion();
    }

    protected function getVersion()
    {
        return file_get_contents(APPLICATION_PATH . '/../public/VERSION');
    }

    /**
     * Devuelve el HTML del escritorio
     */
    public function indexAction()
    {
        //TODO: Ver si esto no hay q hacerlo con un plugin
        // if (!Zend_Auth::getInstance()->hasIdentity()) {
        //     $this->_helper->redirector('index', 'auth');
        // }

        //$this->view->headScript()->appendFile('/js/all.js', 'text/javascript');
        if (APPLICATION_ENV == 'development') {

            $this->view->headScript()->appendFile('/js/PubSub.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/Common.js', 'text/javascript');

            $this->view->headScript()->appendFile('/js/System.js', 'text/javascript');

            $this->view->headScript()->appendFile('/js/Desktop.js', 'text/javascript');

            $this->view->headScript()->appendFile('/js/StartMenu.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/TaskBar.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/Shortcut.js', 'text/javascript');

            $this->view->headScript()->appendFile('/js/ux/Reorderer.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/ToolbarReorderer.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/ToolbarDroppable.js', 'text/javascript');

            //$this->view->headScript()->appendFile('/js/ux/grid/CheckSelect.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/CheckColumn.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.autogrid.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.RadForm.js', 'text/javascript');

            $this->view->headScript()->appendFile('/js/ux/grid/menu/RangeMenu.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/menu/ListMenu.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/GridFilters.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/filter/Filter.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/filter/BooleanFilter.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/filter/DateFilter.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/filter/ListFilter.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/filter/StringFilter.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/filter/NumericFilter.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/xdatefield.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/datetime.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/xcombo.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/advcombo.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/xcheckbox.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/maskfieldplugin.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/GridSummary.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/GroupSummary.js', 'text/javascript');

            $this->view->headScript()->appendFile('/js/ux/renderers/basic.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.IFrame.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.RadMap.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.RadParameterTable.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/DragSelector.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.form.LinkTriggerField.js', 'text/javascript');
            //$this->view->headScript()->appendFile('/js/ux/Ext.ux.UploadDialog.js', 'text/javascript');
            //$this->view->headScript()->appendFile('/js/ux/Rad.form.Carousel.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.RadTree.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.RadWizard.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.StatusBar.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/grid/RowEditor.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/erp/depositos.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/erp/depositopanel.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/erp/resaltador.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/radtemplates.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/form/FileUploadField.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.RadRemoteProvider.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.form.SuperBoxSelect.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.PanelCollapsedTitle.js', 'text/javascript');
            $this->view->headScript()->appendFile('/js/ux/Ext.ux.Wamp.js', 'text/javascript');

        } else {
            $this->view->headScript()->appendFile('/js/all.js', 'text/javascript');
        }
        $this->view->headScript()->appendFile('/direct/UsuariosEscritorio?javascript', 'text/javascript');
        $this->view->headScript()->appendFile('/js/ws/autobahn.min.js', 'text/javascript');

        // El usuario no esta logueado
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->headScript()->appendFile('/auth/index', 'text/javascript');
            $this->view->login = true;
            $this->view->fondo = 'background: #000000 url(/images/pattern_40.gif)';
            $this->view->loading = '

                <div id="loading">
                    <div class="loadinganim-content">
                        <div class="loadinganim-ball"></div>
                        <div class="loadinganim-ball1"></div>
                    </div>
                    <div class="loading-indicator">
                        <img src="/images/cargando.jpg" alt="Cargando..." /><br />
                        <span id="loading-msg">Cargando...</span><br />
                    </div>
                </div>';


        } else {
            $this->view->headScript()->appendFile('/desktop/alljs', 'text/javascript');
            $fondo = $this->getFondo();

            $this->view->fondo     = $fondo['bkg'];
            $this->view->classBody = $fondo['body'];

        }
        $this->view->version = $this->getVersion();


    }

    protected function getFondo()
    {
        $m = new Model_DbTable_UsuariosConfiguracionesEscritorios;

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        $conf = $m->fetchAll('Usuario = '.$usuario)->current();

        $r = array('bkg' => '');

        if ($conf->ImagenFondo) {
            $r['bkg'] .= "background-image: url($conf->ImagenFondo); ";
        }

        if ($conf->ColorFondo) {
            $r['bkg'] .= "background-color:#{$conf->ColorFondo}";
        }

        if ($conf->ImagenFondoPosicion == 'center') {
            $r['body'] = 'wallpaper-center';
        } else {
            $r['body'] = 'wallpaper-tile';
        }

        return $r;
    }

    /**
     * Retorna el javascript que arma y maneja el escritorio
     *
     */
    public function alljsAction()
    {
        $response = $this->getResponse();
        $response->setHeader('content-type', 'application/x-javascript; charset=utf-8');

        $this->view->usuario = $this->view->escape(Zend_Auth::getInstance()->getIdentity()->Nombre);
        $this->view->modules = $this->getModules();
    }

    /**
     * Retorna el json para el store del dataview que muestra los iconos del desktop
     */
    public function getdesktopiconsAction()
    {
        // no se renderiza
        $this->_helper->viewRenderer->setNoRender(true);

        // traemos la conf del escritorio
        $usuariosEscritorio = new Model_DbTable_UsuariosEscritorio();
        $rowset = $usuariosEscritorio->fetchConfig(Zend_Auth::getInstance()->getIdentity()->Id);

        $rtn = array(
            'success' => true,
            'files'   => array(),
            'count'   => count($rowset)
        );

        foreach ($rowset as $row) {
            $t = array();

            if ($row['TienePanel'])
                $t['icon'] = 'defaultFolder';
            else
                $t['icon'] = (file_exists("images/modulos/{$row['Icono']}64.png")) ? $row['Icono'] : 'default';

            if ($row['Url'])
                $t['channel'] = DesktopBaseChannel . $row['Url'];
            else if ($row['TienePanel'])
                $t['channel'] = DesktopBaseChannel . 'Window/menupanel/index/menu/' . $row['Id'];

            //'id', 'channel', 'text', 'icon'
            $t['id']      = $row['Id'];
            $t['text']    = $row['Texto'];

            // id, channel, icon, text, ev
            $rtn['files'][] = $t;
        }

        echo json_encode($rtn);
    }

    protected function getModules()
    {
        $fastCache = Zend_Registry::get('fastCache');

        $identity = Zend_Auth::getInstance()->getIdentity();

        $menu = $fastCache->load('User_Menu_'.$identity->GrupoDeUsuario);

        if (!$menu) {
            $model = new Model_DbTable_MenuesPrincipales(array(), true);
            $rowset = $model->getStartMenu();

            $menu =  array();

            foreach ($rowset as $row) {
                $menu[$row['menuId']] = $row;
            }
            $this->constructTree($menu);

            $fastCache->save(array_keys($this->allowedMenu), 'User_AllowedMenu_'.$identity->GrupoDeUsuario);

            $fastCache->save($menu, 'User_Menu_'.$identity->GrupoDeUsuario, array('permisos'));

        }

        return json_encode($menu);
    }

    /**
     * Construye el arbol de menu
     *
     */
    private function constructTree(&$tree)
    {
        //Rad_Log::debug($tree);
        // ACL MODELOS
        $db = Zend_Registry::get('db');
        $modelACL = new Rad_ModelAcl($db);
        // ACL controladores
        $ACL = new Rad_Acl($db);
        $identity = Zend_Auth::getInstance()->getIdentity();

        $leaf = $top = array();
        foreach ($tree as $id => $row) {
            if (!$row['Activo'])
                continue;
            if (!$row['MenuPrincipal'])
                $top[] = & $leaf[$id];
            else
                $leaf[$row['MenuPrincipal']]['menu'][] = & $leaf[$id];

            $imgMenu = (file_exists("images/modulos/{$row['Icono']}16.png")) ? $row['Icono'] : 'default';

            $menu = array(
                'menuId' => $row['menuId'],
                'text' => $row['Texto'],
                'icon' => "images/modulos/{$imgMenu}16.png"
            );

            // PERMISOS DE MODELOS
            if (in_array($row['Controlador'], array('abm', 'list'))) {
                $path = $modelACL->analyzePath($row['Parametros']);
                $model = ($path['m'] ? ($path['m'].'_') : '') . 'Model_DbTable_'.$path['model'];
                $menu['allowed'] = ($modelACL->allowMenu($model)) ? true : false;
                //Rad_Log::debug('Modelo: '.($menu['allowed']?'Si':'No').' '.$model);
            // PERMISOS DE CONTROLADORES
            } elseif ($row['Modulo']) {
                try {
                    $isAllowed = $ACL->isAllowed(
                        $identity->GrupoDeUsuario,
                        $row['Modulo'].'/'.$row['Controlador']
                    );
                    //Rad_Log::debug('Controlador: '.($isAllowed?'Si':'No').' '.$row['Controlador']);
                } catch (Zend_Acl_Exception $e) {
                    $isAllowed = false;
                }
                $menu['allowed'] = $isAllowed;
            } else {
                $menu['allowed'] = null;
            }
            // EO

            // -----------------------------------------------------------------

            if ($row['Url'])
                $menu['channel'] = DesktopBaseChannel . $row['Url'];
            else if ($row['TienePanel'])
                $menu['channel'] = DesktopBaseChannel . 'Window/menupanel/index/menu/' . $id;
            $leaf[$id] = (array) $leaf[$id] + $menu;
        }
        $tree = $top;

        // Rad_Log::debug($tree);

        foreach ($tree as $key => &$menu) {
            $this->permissionTree($menu);
        }
        foreach ($tree as $key => &$menu) {
            if($menu['allowed'] === false) {
                unset($tree[$key]);
            }
        }
        $tree = array_values($tree);
    }


    private function permissionTree (&$item,$n=0)
    {

        // $esp = str_pad('',$n+1,'-');
        // Rad_Log::debug($esp. '>'.$item['text']);

        $t = $item['text'];
        $allowed = false;
        foreach ($item['menu'] as $k => &$subitem) {
            // Rad_Log::debug( $esp.'"'. $subitem['text']);
            // tiene permisos
            if ($subitem['allowed'] === true) {
                // Rad_Log::debug($esp.'permitido');
                $allowed = true;
            // si no tiene permisos definidos (es solo menu)
            } elseif ($subitem['allowed'] === null) {

                $subAllow = $this->permissionTree($subitem,$n+1);
                if ($subAllow) {
                    $allowed = true;
                    // Rad_Log::debug($esp.$item['text'].' permitido por hijos');
                    $subitem['allowed'] = true;
                } else {
                    $subitem['allowed'] = false;
                    // Rad_Log::debug($esp.'no permitido');
                }

            }
            if ($allowed) {
                $this->allowedMenu[$subitem['menuId']] = $allowed;
            }
        }

        $borrados = false;

        $cp = $item['menu'];

        // borramos los no permitidos
        foreach ($cp as $k => &$subitem) {
            if ($subitem['allowed']===false) {
                unset($item['menu'][$k]);
                $borrados = true;

            }
        }
        if ($borrados) $item['menu'] = array_values($item['menu']);

        if ($allowed) {
            $this->allowedMenu[$item['menuId']] = $allowed;
        }
        $item['allowed'] = $allowed;
        //Rad_Log::debug($esp. '<'.$item['text']);
        return $allowed;
    }
}
