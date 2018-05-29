<?php

require_once 'Rad/Window/Controller/Action.php';

class Develop_AsignarModelosAModulosController extends Rad_Window_Controller_Action
{

    protected $title = 'Modelos a Modulos';

    public function initWindow()
    {

        $configGrillaRelacion = array(
            'id' => 'AsignarModelosAModulos',
            'title' => 'Modelos',
            'listeners' => array(// En caso de guardarse se recarga la grilla de permisos
                'saverelation' => new Zend_Json_Expr("
                    function() {
                            Ext.getCmp('bfbGridHija153244').store.reload();
                    }
            ")
            )
        );
        $grillaHija = $this->view->RadGridManyToMany(
                        'Model_DbTable_Modelos',
                        'Model_DbTable_ModulosModelos',
                        'Model_DbTable_Modulos',
                        $configGrillaRelacion
        );



        $grillaHija1 = $this->view->radGrid(
                        'Model_DbTable_ModulosModelos',
                        array(
                            'title' => 'Permisos',
                            'autoSave' => true,
                            'id' => 'bfbGridHija153244'
                        ),
                        'fasteditor'
        );
        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------
        $detailGrid->id = 'AsignarModelosAModulos';
        $detailGrid->remotefield = 'Modulo';
        $detailGrid->localfield  = 'Id';

        $detailGrid1->id = 'bfbGridHija153244';
        $detailGrid1->remotefield = 'Modulo';
        $detailGrid1->localfield  = 'Id';

        $configHijaP->iniSection = 'reducido';
        $configHijaP->sm = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect:true})');
        $configHijaP->detailGrid = array($detailGrid, $detailGrid1);
        $configHijaP->id = 'bfbGridPadre';
        $configHijaP->fetch = NoAbm;


        $grillaPadre = $this->view->radGrid('Model_DbTable_Modulos', $configHijaP);

        // ----------------------------------------------------------------------------------------------------------
        // ENSAMBLO GRILLAS EN JSON
        // ----------------------------------------------------------------------------------------------------------

        $tab = "{
        	xtype : 'tabpanel',
        	id	  : 'tabAsignarmodelosamodulos',
        	items : [$grillaHija, $grillaHija1],
        	deferredRender : false,
        	activeTab : 0,
                enableTabScroll: true
        }";

        $ventana = $this->view->JsonRender(
            APPLICATION_PATH . '/common/json/BorderWC.json',
            array(
                'JsonGrillaOeste' => $grillaPadre,
                'JsonTituloOeste' => 'Seleccione un modulos',
                'JsonAnchoOeste' => '300',
                'JsonSplitOeste' => 'true',
                'JsonGrillaCentro' => $tab,
                'JsonTituloCentro' => 'Modelos Asignados'
            )
        );

        // ----------------------------------------------------------------------------------------------------------
        // DIBUJO
        // ----------------------------------------------------------------------------------------------------------
        $this->view->contenido = "{
            layout: 'fit',
            title: '$this->title',
            width: 850,
            height: 500,
            id: 'asignarModelosAModulos-win',
            items: $ventana
                
        }";
    }

}