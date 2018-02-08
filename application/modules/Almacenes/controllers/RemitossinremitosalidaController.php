<?php
/**
*   Controlador de Remitos de Ingreso
*/
class Almacenes_RemitosSinRemitoSalidaController extends Rad_Window_Controller_Action
{
    protected $title = 'Remito Interno';

    /**
    * Inicializa la ventana del modulo
    */
    public function initWindow ()
    {
        /**
         * Formulario Principal Remitos de Ingreso (Paso 1)
         */
        $this->view->form = $this->view->radForm(
            'Almacenes_Model_DbTable_RemitosSinRemitoSalida',
            'datagateway',
            'sinremito'
        );

        /**
         * Articulos del Remito (Paso 2)
         */
        $config->abmWindowTitle     = 'Artículo';
        $config->abmWindowWidth     = 650;
        $config->abmWindowHeight    = 200;
        $config->withPaginator      = false;
        $config->title              = 'Artículo';
        $config->loadAuto           = false;
        $config->autoSave           = true;
        $config->layout             = 'fit';

        $this->view->gridRemitosArticulos = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosArticulosDeSalidas',
            $config,
            'abmeditor',
            'wizard'
        );
        unset($config);

        /**
         * Grilla Remitos de Ingresos
         */
        $this->view->grid = $this->view->radGrid(
           'Almacenes_Model_DbTable_RemitosSinRemitoSalida',
           array(
                'abmForm' => '',
                'iniSection' => 'sinremito'
            ),                // Evitamos que radGrid cree automaticamente el formulario al no tenerlo
           'abmeditor'
        );
    }
}