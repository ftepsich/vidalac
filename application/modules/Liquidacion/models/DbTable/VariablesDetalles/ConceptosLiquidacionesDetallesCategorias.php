<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesCategorias
 *
 * Conceptos Liquidaciones Detalles Genericos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesCategorias
 * @extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
 */
class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesCategorias extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
{
    protected $_referenceMap    = array(
        'Variables' => array(
            'columns'           => 'Variable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Variables',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'            => true,
            'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'ConveniosCategorias' => array(
            'columns'           => 'ConvenioCategoria',
            'refTableClass'     => 'Rrhh_Model_DbTable_ConveniosCategorias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'            => true,
            'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'ConveniosCategorias',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_permanentValues = array(
        'VariableJerarquia' => 3
    );

    protected $_defaultValues = array(
        'Empresa' => null,
        'GrupoDePersona' => null,
        'Servicio' => null
    );

    /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 3; // 3: Categoria

}