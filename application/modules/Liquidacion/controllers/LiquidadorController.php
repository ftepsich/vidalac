<?php

/**
 * Liquidacion_LiquidadorController
 *
 * Administrador de Liquidaciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_LiquidadorController
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_LiquidadorController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Liquidaciones';

    public function initWindow()
    {
        /** 
         * ---------------------------------------------------------------------
         * -- GENERALES
         * ---------------------------------------------------------------------
         */

        /**
         * Grilla Cabecera
         */
        $config->abmWindowTitle  = 'Cabecera Recibo';
        $config->abmWindowWidth  = 450;
        $config->abmWindowHeight = 300;
        $config->withPaginator   = false;
        $config->title           = 'Cabecera Recibo';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridCabeceraRecibos';

        $this->view->gridCabeceraRecibos = $this->view->radGrid(
            'Liquidacion_Model_DbTable_LiquidacionesRecibos',
            $config,
            'abmeditor',
            'otraFecha'
        );
        unset($config);

        $detailGridRD = array();

        $dg->id          = $this->getName() . '_GridCabeceraRecibos';
        $dg->remotefield = 'Id';
        $dg->localfield  = 'Id';
        $detailGridRD[]  = $dg;
        unset($dg);

        /**
         * Grilla VariablesCalculadas
         */
        $config->abmWindowTitle  = 'Variables Calculadas';
        $config->abmWindowWidth  = 450;
        $config->abmWindowHeight = 180;
        $config->withPaginator   = false;
        $config->title           = 'Variables Calculadas';
        $config->loadAuto        = false;
        $config->id              = $this->getName() . '_GridVariablesCalculadas';

        $this->view->gridVariablesCalculadas = $this->view->radGrid(
            'Liquidacion_Model_DbTable_LiquidacionesVariablesCalculadas',
            $config,
            ''
        );
        unset($config);

        //$detailGridRD = array();

        $dg->id          = $this->getName() . '_GridVariablesCalculadas';
        $dg->remotefield = 'LiquidacionRecibo';
        $dg->localfield  = 'Id';
        $detailGridRD[]  = $dg;
        unset($dg);

        /**
         * Grilla RecibosDetalles
         */
        $config->abmWindowTitle  = 'Detalle';
        $config->abmWindowWidth  = 450;
        $config->abmWindowHeight = 180;
        $config->title           = 'Recibos Detalle';
        $config->loadAuto        = false;
        $config->withPaginator   = false;
        $config->autoSave        = true;
        $config->withRowEditor   = true;
        $config->buildToolbar    = new Zend_Json_Expr("function() {}");
        $config->pageSize        = 100;
        $config->view            = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,
                hideGroupedColumn: true,
                groupTextTpl: '<span style=\'font-size:15px;\'>{text}</span>'
            })
        ");
        $config->id = $this->getName() . '_GridRecibosDetalles';

        $this->view->gridRecibosDetalles = $this->view->radGrid(
            'Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles',
            $config,
            'editor',
            'editable'
        );
        unset($config);

        $dg->id             = $this->getName() . '_GridRecibosDetalles';
        $dg->remotefield    = 'LiquidacionRecibo';
        $dg->localfield     = 'Id';
        $detailGridRD[]     = $dg;
        unset($dg);

        /**
         * Grilla Personas Ganancias
         */
        $config->loadAuto        = false;
        $config->title           = 'Acumulado Ganancia';
        $config->withPaginator   = false;
        $config->id              = $this->getName() . '_GridPersonasGanancias';
        $config->view            = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,
                hideGroupedColumn: true,
                groupTextTpl: '<span style=\'font-size:15px;\'>{text}</span>'
            })
        ");

        $this->view->gridPersonasGanancias = $this->view->radGrid(
            'Rrhh_Model_DbTable_PersonasGananciasLiquidaciones',
            $config,
            ''
        );

        $dg->id          = $this->getName() . '_GridPersonasGanancias';
        $dg->remotefield = 'Persona';
        $dg->localfield  = 'Persona';
        $detailGridRD[]   = $dg;
        unset($dg);
        unset($config);

        /**
         * Grilla Recibos
         */
        $config->abmWindowTitle  = 'Recibos';
        $config->abmWindowWidth  = 800;
        $config->abmWindowHeight = 400;
        $config->withRowEditor   = true;
        $config->fetch           = 'SinAjustes';
        $config->loadAuto        = false;
        $config->detailGrid      = $detailGridRD;
        $config->pageSize        = 50;
        $config->id              = $this->getName() . '_GridRecibos';

        $this->view->gridRecibos = $this->view->radGrid(
            'Liquidacion_Model_DbTable_LiquidacionesRecibos',
            $config,
            'editor'
        );

        unset($detailGridRD);
        unset($config);

        $detailGrids = array();

        /**
         * ---------------------------------------------------------------------
         * -- Liquidaciones
         * ---------------------------------------------------------------------
         */

        $dg->id          = $this->getName() . '_GridRecibos';
        $dg->remotefield = 'Liquidacion';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        /**
         * ---------------------------------------------------------------------
         * -- Liquidaciones
         * ---------------------------------------------------------------------
         */
        $config->abmWindowTitle   = 'Liquidaciones';
        $config->abmWindowWidth   = 500;
        $config->abmWindowHeight  = 350;
        $config->pageSize         = 100;
        $config->detailGrid       = $detailGrids;
        $config->topButtons->add  = false;
        $config->topButtons->del  = false;
        $config->topButtons->edit = false;
        $config->view             = new Zend_Json_Expr("
            new Ext.grid.GroupingView({
                forceFit:true,
                hideGroupedColumn: true,
                groupTextTpl: '<span style=\'font-size:15px;\'>{text}</span>'
            })
        ");
        $this->view->grid = $this->view->radGrid(
            'Liquidacion_Model_DbTable_Liquidaciones',
            $config,
            'abmeditor'
        );
    }

}
