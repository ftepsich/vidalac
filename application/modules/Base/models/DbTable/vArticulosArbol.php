<?php

class Base_Model_DbTable_vArticulosArbol extends Rad_Db_Table
{
    protected $_name = 'vArticulosArbol';

    protected $_primary = 'Id';

    protected $_referenceMap    = array(
        'TreeArticulosArbol' => array(
            'columns'        => 'Padre',
            'refTableClass'  => 'Base_Model_DbTable_vArticulosArbol',
            'refJoinColumns' => array('Descripcion'),
            //'comboBox'     => false,
            //'comboSource'  => 'datagateway/combolist',
            'refTable'       => 'vArticulosArbol',
            'refColumns'     => 'Id'
        )
    );

    protected $_dependentTables = array();
}