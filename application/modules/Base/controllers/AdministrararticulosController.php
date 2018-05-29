<?php

/**
 * Base_AdministrarArticulosController
 *
 * Administrar Articulos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_AdministrarListasDePreciosController
 * @extends Rad_Window_Controller_Action
 */
class Base_AdministrarArticulosController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Articulos';

    public function initWindow()
    {
        $config->loadAuto               = false;
        $config->abmWindowConfig->modal = true;
        // $config->id         = $this->getName() . '_GridArticulosVersionesDetalles';
        $this->view->articulosVersionesDetalles = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosVersionesDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        // $config->tpl = '{Descripcion}';
        // $this->view->tree = $this->view->radTree(
        //     'Base_Model_DbTable_ArticulosVersiones',
        //     'ArticuloVersionPadre',
        //     $config,
        //     'abmeditor'
        // );

        $config->viewConfig = new Zend_Json_Expr("
        {
            forceFit:true,
            enableRowBody:false,
            getRowClass: function(record, rowIndex, p, store) {
                var tc = record.get('EnDesuso');
                return (tc == 1)? 'x-grid3-row-red' : '';
            }
        }");

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosGenericos',
            $config,
            'abmeditor'
        );
        unset($config);

        // $this->view->tree = $this->view->radTree(
        //     'Base_Model_DbTable_vArticulosArbol',
        //     'TreeArticulosArbol',
        //     $config,
        //     'abmeditor'
        // );

    }

    public function getarbolAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->id;

        $mArticulos = Service_TableManager::get('Base_Model_DbTable_Articulos');

        // traigo la estructura del articulo
        $estructura = $mArticulos->getEstructuraArbol($id, true);

        $estructura['success'] = true;

        echo json_encode($estructura);
    }
}
