<?php

/**
 * Almacenes_RemitosDespachadosController
 *
 * Remitos Despachados
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Almacenes_RemitosDespachadosController
 * @extends Rad_Window_Controller_Action
 */
class Almacenes_RemitosDespachadosController extends Rad_Window_Controller_Action
{

    protected $title = 'Remitos Despachados';

    public function initWindow()
    {
        /**
         * Grilla Productos Listas de Preciso Detalle
         */
        $config->loadAuto       = false;
        $config->id             = $this->getName() . '_GridRD_Mmi';
   
        $this->view->GridRD_Mmi   = $this->view->radGrid(
            'Almacenes_Model_DbTable_Mmis',
            $config,
            '',
            'remitosDespachados'
        );
        unset($config);

        /**
         * Grilla Productos Listas de Preciso
         */
        $detailGrid->id             = $this->getName() . '_GridRD_Mmi';
        $detailGrid->remotefield    = 'RemitosArticulosSalidaComprobante';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;  
        $config->fetch              = 'Despachado';    

        $this->view->grid           = $this->view->radGrid(
            'Almacenes_Model_DbTable_RemitosDeSalidas',
            $config,
            ''
        );
        unset($config);
    }

}
