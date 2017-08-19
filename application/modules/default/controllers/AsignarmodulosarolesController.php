<?php
require_once 'Rad/Window/Controller/Action.php';

class AsignarModulosARolesController extends Rad_Window_Controller_Action
{
    protected $title = "Permisos";

    public function initWindow()
    {
        $grillaHija = $this->view->RadGridManyToMany(
                "Model_DbTable_Modulos",
                "Model_DbTable_RolesModulos",
                "Model_DbTable_Roles",
                array (
                    'id'    => 'AsignarModulosARoles758',
                    'fetch' => 'NoAbm'
                )
        );

        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------
        $detailGrid->id = 'AsignarModulosARoles758';
        $detailGrid->remotefield = 'Rol';
        $detailGrid->localfield = 'Id';

        $configHijaP->iniSection = '';
        $configHijaP->sm = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect:true})');
        $configHijaP->detailGrid = $detailGrid;
        $configHijaP->id = 'dsgfikudf4364';

        $grillaPadre = $this->view->radGrid('Model_DbTable_Roles', $configHijaP);

        // ----------------------------------------------------------------------------------------------------------
        // ENSAMBLO GRILLAS EN JSON
        // ----------------------------------------------------------------------------------------------------------

        $ventana = $this->view->JsonRender(APPLICATION_PATH . '/common/json/BorderWC.json',
            array('JsonGrillaOeste' => $grillaPadre,
                'JsonTituloOeste' => 'Seleccione un Rol',
                'JsonAnchoOeste' => '300',
                'JsonSplitOeste' => 'true',
                'JsonGrillaCentro' => $grillaHija,
                'JsonTituloCentro' => 'Modulos Asignados'
            )
        );

        // ----------------------------------------------------------------------------------------------------------
        // DIBUJO
        // ----------------------------------------------------------------------------------------------------------

         $this->view->contenido = "{
            layout: 'fit',
            title: '$this->title',
            width: 900,
            height: 500,
            items: $ventana
        }";
    }
}

