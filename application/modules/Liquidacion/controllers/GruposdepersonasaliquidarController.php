<?php

/**
 * Liquidacion_GruposDePersonasALiquidarController
 *
 * Crea grupos y asocia las personas para liquidar
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_GruposDePersonasALiquidarController
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_GruposDePersonasALiquidarController extends Rad_Window_Controller_Action
{
    protected $title = 'Asociar Personas a un grupo a liquidar';

    public function initWindow()
    {

        /**
         * Grilla de Grupos de Personas Detalles
         */
        $config->loadAuto                       = false;
        $config->id                             = $this->getName() . '_GridGP_Detalle';
        $this->view->gridGrupoDePersonaDetalle  = $this->view->radGrid(
            'Liquidacion_Model_DbTable_GruposDePersonasDetalles',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla de Grupos de Personas
         */
        $detailGrid->id             = $this->getName() . '_GridGP_Detalle';
        $detailGrid->remotefield    = 'GrupoDePersona';
        $detailGrid->localfield     = 'Id';

        $config->detailGrid         = $detailGrid;

        $this->view->grid           = $this->view->radGrid(
            'Liquidacion_Model_DbTable_GruposDePersonas',
            $config,
            'abmeditor'
        );
        unset($config);
   
    }

}