<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesPuestos
 *
 * Conceptos Liquidaciones Detalles Genericos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesPuestos
 * @extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
 */
class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesPuestos extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
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
        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Id'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Servicio',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_permanentValues = array(
        'VariableJerarquia' => 1
    );

    protected $_defaultValues = array(
        'Convenio' => null,
        'Empresa' => null,
        'ConvenioCategoria' => null,
        'GrupoDePersona' => null
    );

    /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 1; // 1: Servicio

    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('Servicios')
                ->joinRef('Personas', array(
                    'RazonSocial'
                ))
                ->joinRef('Empresas', array(
                    'Descripcion' => 'TRIM({remote}.Descripcion)'
                ));
        }
    }

}