<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_PermisosExportaciones * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_PermisosExportaciones extends Rad_Db_Table
{
    protected $_name = 'PermisosExportaciones';

    protected $_referenceMap = array(
        
        'Comprobantes' => array(
            'columns'           => 'Comprobante',
            'refTableClass'     => 'Facturacion_Model_DbTable_Comprobantes',
            'refTable'          => 'Comprobantes',
            'refColumns'        => 'Id'
        ),
        'Paises' => array(
            'columns'           => 'PaisDestino',
            'refTableClass'     => 'Base_Model_DbTable_Paises',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Paises',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array();
}