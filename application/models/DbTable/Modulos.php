<?php
require_once('Rad/Db/Table.php');
/**
 * Model_DbTable_Modulos
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Model_DbTable_Modulos
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_Modulos extends Rad_Db_Table
{
    protected $_name = 'Modulos';
    protected $_sort = array ("Nombre asc","Titulo asc");
    
    protected $_dependentTables = array("Model_DbTable_ModulosModelos");
    
    
    public function fetchNoAbm ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Controlador <> 'abm' and  Controlador <> 'list'";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }       
}