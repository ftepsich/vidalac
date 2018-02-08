<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_ArchivosAdjuntos * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Model_DbTable_ArchivosAdjuntos extends Rad_Db_Table
{
    protected $_name = 'ArchivosAdjuntos';

    protected $_referenceMap = array(
        'Modelos' => array(
            'columns'           => 'Modelo',
            'refTableClass'     => 'Model_DbTable_Modelos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Modelos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array();
}