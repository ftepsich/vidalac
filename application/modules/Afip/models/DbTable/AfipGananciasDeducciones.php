<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_AfipGananciasDeducciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipGananciasDeducciones extends Rad_Db_Table
{
    protected $_name = 'AfipGananciasDeducciones';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap = array(
        'AfipGananciasDeduccionesTipos' => array(
            'columns'           => 'TipoDeduccion',
            'refTableClass'     => 'Afip_Model_DbTable_AfipGananciasDeduccionesTipos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipGananciasDeduccionesTipos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_dependentTables = array('Afip_Model_DbTable_AfipGananciasDeduccionesDetalles');
}