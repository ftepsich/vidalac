<?php
require_once 'Rad/Window/Controller/Action.php';

class AsignarModelosARolesController extends Rad_Window_Controller_Action
{

    protected $title = "Asig. Modelos a Roles";

    public function initWindow()
    {

        $configGrillaRelacion = array(
            'id' => 'AsignarModelosARoles6efg',
            'title' => 'Modelos',
            'listeners' => array(// En caso de guardarse se recarga la grilla de permisos
                'saverelation' => new Zend_Json_Expr("
					function() {
						Ext.getCmp('bfbGridHijah56ghgjhd').store.reload();
					}
				")
            )
        );
        
        $grillaHija = $this->view->RadGridManyToMany(
                "Model_DbTable_Modelos",
                "Model_DbTable_RolesModelos",
                "Model_DbTable_Roles",
                $configGrillaRelacion
        );


        $grillaHija1 = $this->view->radGrid(
                'Model_DbTable_RolesModelos',
                array(
                    'title' => 'Permisos',
                    'autoSave' => true,
                    'id' =>  'bfbGridHijah56ghgjhd'
                ),
                'fasteditor'
        );
        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------
        $detailGrid->id = 'AsignarModelosARoles6efg';
        $detailGrid->remotefield = 'Rol';
        $detailGrid->localfield = 'Id';

        $detailGrid1->id = 'bfbGridHijah56ghgjhd';
        $detailGrid1->remotefield = 'Rol';
        $detailGrid1->localfield = 'Id';

        $configHijaP->iniSection = 'reducido';
        $configHijaP->sm = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect:true})');
        $configHijaP->detailGrid = array($detailGrid, $detailGrid1);
        $configHijaP->id =  'bfbGridPadre556sdsf';



        $grillaPadre = $this->view->radGrid('Model_DbTable_Roles', $configHijaP);

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

        $ventana = $this->view->JsonRender(APPLICATION_PATH . '/common/json/BorderWC.json',
                        array('JsonGrillaOeste' => $grillaPadre,
                            'JsonTituloOeste' => 'Seleccione un Rol',
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
                width: 900,
                height: 500,
                id: 'asignarModelosARoles-win',
                items: $ventana

        }";
    }

}

