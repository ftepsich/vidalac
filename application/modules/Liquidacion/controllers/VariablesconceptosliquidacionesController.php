<?php

/**
 * Liquidacion_VariablesConceptosLiquidacionesController
 *
 * Administrar las variables de conceptos liquidaciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_VariablesConceptosLiquidacionesController
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_VariablesConceptosLiquidacionesController extends Rad_Window_Controller_Action
{

    protected $title = 'Administrar Conceptos de liquidaciones';

    public function initWindow()
    {
        $req = $this->getRequest();

        if ($req->Extras) {
            $class = 'Liquidacion_Model_DbTable_Variables_ConceptosLiquidacionesExtras';
        } else {
            $class = 'Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones';
        }

        /**
         * Grilla Detalle de conceptos de liquidaciones genericos
         */
        $config->abmWindowTitle         = 'Generico';
        $config->abmWindowWidth         = 650;
        $config->abmWindowHeight        = 300;
        $config->title                  = 'Generico';
        $config->fetch                  = 'SinHistoricos';        
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVCL_DetalleGenericos';

        $this->view->gridVCL_DetalleGenerico   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGenericos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Detalle de conceptos de liquidaciones Convenio
         */
        $config->abmWindowTitle         = 'Convenio';
        $config->abmWindowWidth         = 650;
        $config->abmWindowHeight        = 300;
        $config->title                  = 'Convenio';
        $config->fetch                  = 'SinHistoricos';             
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVCL_DetalleConvenio';

        $this->view->gridVCL_DetalleConvenio   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesConvenios',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Detalle de conceptos de liquidaciones Empresa
         */
        $config->abmWindowTitle         = 'Empresa';
        $config->abmWindowWidth         = 650;
        $config->abmWindowHeight        = 300;
        $config->title                  = 'Empresa';
        $config->fetch                  = 'SinHistoricos';             
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVCL_DetalleEmpresa';

        $this->view->gridVCL_DetalleEmpresa   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesEmpresas',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Detalle de conceptos de liquidaciones Categoria
         */
        $config->abmWindowTitle         = 'Categoria';
        $config->abmWindowWidth         = 650;
        $config->abmWindowHeight        = 300;
        $config->title                  = 'Categoria';
        $config->fetch                  = 'SinHistoricos';             
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVCL_DetalleCategoria';

        $this->view->gridVCL_DetalleCategoria   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesCategorias',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Detalle de conceptos de liquidaciones Grupo
         */
        $config->abmWindowTitle         = 'Grupo';
        $config->abmWindowWidth         = 650;
        $config->abmWindowHeight        = 300;
        $config->title                  = 'Grupo';
        $config->fetch                  = 'SinHistoricos';             
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVCL_DetalleGrupo';

        $this->view->gridVCL_DetalleGrupo   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGrupos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Grilla Detalle de conceptos de liquidaciones Puesto
         */
        $config->abmWindowTitle         = 'Puesto';
        $config->abmWindowWidth         = 650;
        $config->abmWindowHeight        = 300;
        $config->title                  = 'Puesto';
        $config->fetch                  = 'SinHistoricos';             
        $config->loadAuto               = false;
        $config->id                     = $this->getName() . '_GridVCL_DetallePuesto';

        $this->view->gridVCL_DetallePuesto   = $this->view->radGrid(
            'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesPuestos',
            $config,
            'abmeditor'
        );
        unset($config);

        /**
         * Relaciones Con Conceptos de liquidaciones
         */
        $detailGrids = array();

        $dg->id = $this->getName() . '_GridVCL_DetalleGenericos';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridVCL_DetalleConvenio';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridVCL_DetalleEmpresa';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridVCL_DetalleCategoria';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id = $this->getName() . '_GridVCL_DetalleGrupo';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $dg->id          = $this->getName() . '_GridVCL_DetallePuesto';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        $this->view->gridTipoLiquidaciones = $this->view->RadGridManyToMany(
            'Liquidacion_Model_DbTable_TiposDeLiquidaciones',
            'Liquidacion_Model_DbTable_VariablesTiposDeLiquidaciones',
            $class,
            array(
                'title'             => 'Tipos de Liquidaciones',
                'xtype'             => 'radformmanytomanyeditorgridpanel',
                'detailGrid'        => $detailGrid,
                'abmWindowTitle'    => 'Agregar remito',
                'abmWindowWidth'    => 940,
                'abmWindowHeight'   => 620,
                'withPaginator'     => false,
                'iniSection'        => 'wizard',
                'id'                => $this->getName() . '_GridTipoDeLiquidaciones'
            )
        );

        $dg->id          = $this->getName() . '_GridTipoDeLiquidaciones';
        $dg->remotefield = 'Variable';
        $dg->localfield  = 'Id';
        $detailGrids[]   = $dg;
        unset($dg);

        /**
         * Grilla de conceptos de liquidaciones
         */

        $config->abmWindowTitle  = 'Conceptos Liquidaciones';
        $config->abmWindowWidth  = 500;
        $config->abmWindowHeight = 500;
        $config->detailGrid      = $detailGrids;
        $config->loadAuto        = false;
        $config->pageSize        = 100;        

        $this->view->grid = $this->view->radGrid(
            $class,
            $config,
            'abmeditor'
        );
        unset($config);
    }

}
