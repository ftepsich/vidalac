<?php
class Model_DbTable_CronProgramaciones extends Rad_Db_Table
{
    protected $_name = 'CronProgramaciones';

    protected $_referenceMap    = array(
        'CronTareas' => array(
            'columns'           => 'CronTarea',
            'refTableClass'     => 'Model_DbTable_CronTareas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'CronTareas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'Tipos' => array(
            'columns'           => 'Tipo',
            'refTableClass'     => 'Model_DbTable_CronTiposProgramaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'CronTiposProgramaciones',
            'refColumns'        => 'Id'
        )
    );

    protected $_dependentTables = array();  
}