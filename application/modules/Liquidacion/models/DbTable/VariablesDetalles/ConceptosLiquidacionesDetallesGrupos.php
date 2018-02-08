<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGrupos
 *
 * Conceptos Liquidaciones Detalles Genericos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGrupos
 * @extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
 */
class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGrupos extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
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
        'GruposDePersonas' => array(
            'columns'           => 'GrupoDePersona',
            'refTableClass'     => 'Liquidacion_Model_DbTable_GruposDePersonas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'            => true,
            'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'GruposDePersona',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_permanentValues = array(
        'VariableJerarquia' => 2
    );

    protected $_defaultValues = array(
        'Convenio' => null,
        'Empresa' => null,
        'ConvenioCategoria' => null,
        'Servicio' => null
    );

    /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 2; // 2: Grupos
}