<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesConvenios
 *
 * Conceptos Liquidaciones Detalles Genericos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesConvenios
 * @extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
 */
class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesConvenios extends Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
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
        )
    );

    protected $_permanentValues = array(
        'VariableJerarquia' => 5
    );

    protected $_defaultValues = array(
        'Empresa' => null,
        'ConvenioCategoria' => null,
        'GrupoDePersona' => null,
        'Servicio' => null
    );

    /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 5; // 5: Convenios

    /**
     * Insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();

            if (!isset($data['Convenio'])) throw new Rad_Db_Table_Exception('No se ingreso el convenio.');
            $condicion = " AND Convenio = ".$data['Convenio'];
            // inserto el registro --> el log se hace en VariablesAbstractas
            $id     = parent::insert($data);
            $row    = $this->find($id)->current();
            // Verifico que no se superponga con otro periodo
            $this->salirSi_superponeConOtroPeriodo($row,$condicion);
            /*
                OJO... VariableJerarquia es un valor default por lo tanto no viene en el data, pero es uno
                de los valores que necesitamos para referenciar en forma univoca a este registro.

                Por lo tanto los controles de superposicion los vamos a tener que hacer despues de insertar.
            */
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "VariablesDetalles.Convenio is not null and VariablesDetalles.Empresa is null and VariablesDetalles.ConvenioCategoria is null and VariablesDetalles.GrupoDePersona is null and VariablesDetalles.Servicio is null";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}