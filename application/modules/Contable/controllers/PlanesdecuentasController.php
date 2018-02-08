<?php

/**
 * Contable_PlanesDeCuentasController
 *
 * Planes de Cuentas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_PlanesDeCuentasController
 * @extends Rad_Window_Controller_Action
 */
class Contable_PlanesDeCuentasController extends Rad_Window_Controller_Action
{

    protected $title = 'Planes de Cuentas';
    
    public function initWindow()
    {
        $config->abmForm = new Zend_Json_Expr($this->view->radFormTree(
            'Contable_Model_DbTable_PlanesDeCuentas',
            'datagateway'
        ));
        $config->tpl = '{Descripcion} - {Jerarquia}';
        
        $this->view->tree = $this->view->radTree(
            'Contable_Model_DbTable_PlanesDeCuentas',
            'TreePlanesDeCuentas',
            $config,
            'abmeditor'
        );
        
        //parent::init();
    }

}
