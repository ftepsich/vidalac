<?php
/**
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_DbTable_DeduccionesGanancias * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_DeduccionesGanancias extends Rad_Db_Table
{
    protected $_name = 'DeduccionesGanancias';

    protected $_sort = array('Descripcion asc');    

    protected $_referenceMap = array(
        
    'DeduccionesGananciasTipos' => array(
        'columns'           => 'Tipo',
        'refTableClass'     => 'Liquidacion_Model_DbTable_DeduccionesGananciasTipos',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'DeduccionesGananciasTipos',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
    );

    protected $_dependentTables = array('Liquidacion_Model_DbTable_PersonasDeduccionesGanancias');
}