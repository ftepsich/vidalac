<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_GananciasConceptos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_GananciasConceptos extends Rad_Db_Table
{
    protected $_name = 'GananciasConceptos';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap = array(

    'GananciasDeduccionesTipos' => array(
        'columns'           => 'GananciaDeduccionTipo',
        'refTableClass'     => 'Liquidacion_Model_DbTable_GananciasDeduccionesTipos',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'GananciasDeduccionesTipos',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
,
    'GananciasConceptosTipos' => array(
        'columns'           => 'GananciaConceptoTipo',
        'refTableClass'     => 'Liquidacion_Model_DbTable_GananciasConceptosTipos',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'GananciasConceptosTipos',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
,
    'AfipGananciasDeducciones' => array(
        'columns'           => 'AfipGananciaDeduccion',
        'refTableClass'     => 'Afip_Model_DbTable_AfipGananciasDeducciones',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'AfipGananciasDeducciones',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasGananciasDeducciones');

    public function fetchDeduccionesPersonales($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "GananciasConceptos.GananciaConceptoTipo = 2";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }



}