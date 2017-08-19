<?php

/**
 * Base_ArticulosCompraVentaController
 *
 * Administrar Articulos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_ArticulosCompraVentaController
 * @extends Rad_Window_Controller_Action
 */
class Base_ArticulosCompraVentaController extends Rad_Window_Controller_Action
{
    protected $title = 'Administrar Articulos';

    public function initWindow()
    {
        $config['id']       = $this->getName() . '_GridPrecios';
        $config['flex']     = 1;
        $config['loadAuto'] = false;
        $config['title']    = 'Precios';
        $config['section']  = 'reducido';
        $this->view->gridPrecios = $this->view->radGrid(
            'Base_Model_DbTable_PersonasRegistrosDePrecios',
            $config,
            '',
            'reducido'
        );

        unset($config);

        $config['viewConfig'] = new Zend_Json_Expr("
        {
            forceFit:true,
            enableRowBody:false,
            getRowClass: function(record, rowIndex, p, store) {
                var tc = record.get('EnDesuso');
                return (tc == 1)? 'x-grid3-row-red' : '';
            }
        }");


        $dg = array();
        $dg['id']          = $this->getName() . '_GridPrecios';
        $dg['remotefield'] = 'Articulo';
        $dg['localfield']  = 'Id';

        $config['detailGrid'] = array($dg);
        $config['flex']       = 2;

        $this->view->grid = $this->view->radGrid(
            'Base_Model_DbTable_ArticulosFinales',
            $config,
            'abmeditor',
            'finales'
        );
    }
}