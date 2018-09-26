<?php

require_once 'Rad/Window/Controller/Action.php';

/**
 * Controlador generico ABM
 */
class Almacenes_LotesPropiosController extends Rad_Window_Controller_Action
{
    /**
     * Arma la grilla usando el view helper radGrid
     */
    protected function buildGrid()
    {
        $parametrosAdc->autoSave      = true;
        $parametrosAdc->withRowEditor = false;

        //$parametrosAdc->abmForm         = new Zend_Json_Expr($ambForm);
        $parametrosAdc->view = new Zend_Json_Expr("
        	new Ext.grid.GroupingView({
                    enableNoGroups: false,
                    forceFit: true,
                    hideGroupedColumn: true,
                    groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Registros\" : \"Registro\"]})'
	        })
    	");

        $this->grid = $this->view->radGrid('Almacenes_Model_DbTable_LotesPropios', $parametrosAdc, 'abmeditor');
    }

    public function initWindow()
    {
       
        $this->title = 'Lotes Propios';

        $this->buildGrid();
        
        $this->view->grid = $this->grid;
    }

}
