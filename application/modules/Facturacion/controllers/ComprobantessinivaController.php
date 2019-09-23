<?php

/**
 * Facturacion_ComprobantesSinIVAController
 *
 * Controlador Facturas Compras
 *
 *
 * @package Aplicacion
 * @subpackage Facturacion
 * @class Facturacion_ComprobantesSinIVAController
 * @extends Rad_Window_Controller_Action
 * @copyright SmartSoftware Argentina 2010
 */
class Facturacion_ComprobantesSinIVAController extends Rad_Window_Controller_Action {

    protected $title = 'Ingreso de Comprobantes Sin IVA';

    /**
     * Inicializa la ventana del modulo
     */
    public function initWindow() {
        /**
         * Formulario del Comprobante (Paso 1)
         */
        $abmFormCF = $this->view->radForm(
                'Facturacion_Model_DbTable_ComprobantesSinIVA', 'datagateway', 'wizard'
        );

        $this->view->form = $abmFormCF;

        /**
         * Articulos del Comprobante (Paso 2)
         */

        $config->withPaginator = false;
        $config->loadAuto = false;
        $config->id = $this->getName() . '_GridArticulos';
        $config->abmWindowTitle = 'Artículo';
        $config->abmWindowWidth = 840;
        $config->abmWindowHeight = 260;
        $config->withPaginator = false;
        $config->title = 'Artículos';
        $config->loadAuto = false;
        $config->autoSave = true;
        $config->iniSection = 'wizard';
        $this->view->gridArticulos = $this->view->radGrid(
                'Facturacion_Model_DbTable_ComprobantesSinIVAArticulos', $config, 'abmeditor', 'wizard'
        );
        unset($config);

        $this->view->grid = $this->view->radGrid(
                'Facturacion_Model_DbTable_ComprobantesSinIVA', $config, 'abmeditor'
        );
        unset($config);
    }

}
