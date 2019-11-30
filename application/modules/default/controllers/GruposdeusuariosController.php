<?php

/**
 * GruposDeUsuariosController
 *
 * Agregar grupos de usuarios y asignarles roles
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class GruposDeUsuariosController
 * @extends Rad_Window_Controller_Action
 */
class GruposDeUsuariosController extends Rad_Window_Controller_Action
{
    protected $title = 'Grupos de Usuarios';

    public function initWindow()
    {

        $this->view->gridRoles = $this->view->RadGridManyToMany(
            'Model_DbTable_Roles',
            'Model_DbTable_GruposDeUsuariosRoles',
            'Model_DbTable_GruposDeUsuarios',
            array(
                'withPaginator'	 => false,
                'withToolbar'	 => false,
                'loadAuto'           => false,
                //'iniSection'       => 'reducido',
                'id'                 => $this->getName().'_Roles'
            )
        );
		
        $detailGrid = array('remotefield'=> 'Grupo', 'localfield' => 'Id', 'id' => $this->getName().'_Roles');

        /**
         * Grilla Grupos de Usuarios
         */
        $this->view->grid = $this->view->radGrid(
           'Model_DbTable_GruposDeUsuarios',
           array(
               'detailGrid'=> $detailGrid,
               'sm' => new Zend_Json_Expr("
                     new Ext.grid.RowSelectionModel(
                     {
                        singleSelect: true,
//                        listeners: {
//                            'rowselect': function(i, rowIndex, r) {
//                                detailGrid = {remotefield: 'Grupo', localfield: 'Id'};
//                                gh = Ext.getCmp('{$this->getName()}_Roles');
//                                gh.loadAsDetailGrid(detailGrid, r.data.Id);
//                            }
//                        }
                     })"
               )
           ),                // Evitamos que radGrid cree automaticamente el formulario al no tenerlo
           'abmeditor',''
        );
    }

}