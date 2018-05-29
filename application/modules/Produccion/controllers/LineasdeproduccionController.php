<?php
require_once 'Rad/Window/Controller/Action.php';

class Produccion_LineasDeProduccionController extends Rad_Window_Controller_Action
{
    protected $title = "Lineas de Producción";

    public function initWindow()
    {
        $configGrillaRelacion = array(
            'id' => 'AsignarModelosARoles6efg',
            'title' => 'Procesos',
            'flex' => 1,
            'listeners' => array(// En caso de guardarse se recarga la grilla de permisos
                'saverelation' => new Zend_Json_Expr("
                    function() {
                            Ext.getCmp('AsignarModelosARoles6efg').store.reload();
                    }
            ")
            )
        );
        
        $this->view->gridConfiguracion = $this->view->RadGridManyToMany(
                "Produccion_Model_DbTable_Actividades",
                "Produccion_Model_DbTable_LineasDeProduccionesActividades",
                "Produccion_Model_DbTable_ActividadesConfiguraciones",
                $configGrillaRelacion
        );

        $detailGrid->id = 'AsignarModelosARoles6efg';
        $detailGrid->remotefield = 'ActividadConfiguracion';
        $detailGrid->localfield  = 'Id';


        $this->view->gridActividadesConf = $this->view->radGrid(
                'Produccion_Model_DbTable_ActividadesConfiguraciones',
                array(
                    'detailGrid' => array($detailGrid),
                    'loadAuto' => false,
                    'title' => 'Configuracion Por Tipo',
                    'flex' => 1,
                    'id' =>  'prodActConfigGridId734'
                ),
                'abmeditor'
        );
        
        // ----------------------------------------------------------------------------------------------------------
        // GRILLA PADRE
        // ----------------------------------------------------------------------------------------------------------
        $detailGrid->id = 'prodActConfigGridId734';
        $detailGrid->remotefield = 'TipoDeLineaDeProduccion';
        $detailGrid->localfield  = 'TipoDeLineaDeProduccion';

        $configHijaP->sm = new Zend_Json_Expr('new Ext.grid.RowSelectionModel({singleSelect:true})');
        $configHijaP->detailGrid = array($detailGrid);
        $configHijaP->id =  'bfbGridPadre556sdsf';
        
        $this->view->grid = $this->view->radGrid('Produccion_Model_DbTable_LineasDeProducciones', $configHijaP, 'abmeditor');
    }

}

