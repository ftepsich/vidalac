<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_AdministrarClientesController
 *
 * Direcciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_AdministrarClientesController
 * @extends Rad_Window_Controller_Action
 */
class Base_Model_DbTable_TiposDeDirecciones extends Rad_Db_Table
{

    protected $_name = 'TiposDeDirecciones';
    protected $_dependentTables = array('Base_Model_DbTable_Direcciones');

}