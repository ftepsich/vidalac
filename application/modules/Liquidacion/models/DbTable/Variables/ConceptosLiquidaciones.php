<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones
 *
 * Conceptos de liquidaciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones
 * @extends Liquidacion_Model_DbTable_VariablesAbstractas
 */
class Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones extends Liquidacion_Model_DbTable_VariablesAbstractas
{

    protected $_referenceMap    = array(
	    'TiposDeVariables' => array(
            'columns'           => 'TipoDeVariable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeVariables',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeVariables',
            'refColumns'        => 'Id',
        ),
	    'TiposDeConceptosLiquidaciones' => array(
            'columns'           => 'TipoDeConceptoLiquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeConceptosLiquidaciones',
            'refJoinColumns'    => array('Descripcion','DescripcionR'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeConceptosLiquidaciones',
            'refColumns'        => 'Id',
        ),
        'VariablesTiposDeConceptos' => array(
            'columns'           => 'TipoDeConcepto',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesTiposDeConceptos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'VariablesTiposDeConceptos',
            'refColumns'        => 'Id',
        ),
        'CaracteristicasGanancias' => array(
            'columns'           => 'CaracteristicaGanancia',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesCaracteristicasGanancias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'VariablesCaracteristicasGanancias',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array(
        'Liquidacion_Model_DbTable_VariablesTiposDeLiquidaciones'
    );

    protected $_permanentValues  = array(
        'TipoDeVariable'    => 1,
        'VariableCategoria' => 1
    );
    protected $_defaultValues    = array(
        'TipoDeVariable'            => 1,
        'VariableCategoria'         => 1,
        'Desactivado'               => 0,
        'CaracteristicaGanancia'    => 1
    );

    /**
     * Inserta un registro
     *
     * @param array $data
     *
     */
    public function insert($data) {

        // Si no es descuento blanqueo TipoDeDeduccion
        //$data['TipoDeDeduccion'] = ($data['TipoDeConceptoLiquidacion'] == 4) ? $data['TipoDeDeduccion'] : null;

        //Si es una deduccion verifico que este el Tipo
        if (!$data['TipoDeConcepto']) throw new Liquidacion_Model_Exception("Debe indicar el tipo de Deduccion para ser tenida en cuanrta en Ganancias.");

        $data['Nombre'] = str_replace(" ","",ucwords(strtolower($data['Descripcion'])));
        $data['Nombre'] = str_replace("/","",ucwords(strtolower($data['Nombre'])));
        $data['Nombre'] = str_replace("*","",ucwords(strtolower($data['Nombre'])));
        $data['Nombre'] = str_replace("+","",ucwords(strtolower($data['Nombre'])));
        return $id = parent::insert($data);
    }

    /**
     * Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {

        if (!$data['TipoDeConcepto']) throw new Liquidacion_Model_Exception("Debe indicar la Categoria del Concepto.");

        $this->_db->beginTransaction();
        try {

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {

                // Verifico el tipo y reviso que si es un descuento este indicado
                //$TipoDeConcepto = (isset($data['TipoDeConceptoLiquidacion'])) ? $data['TipoDeConceptoLiquidacion'] : $row->TipoDeConceptoLiquidacion;
                //if ($TipoDeConcepto == 4) {
                //}

                /*
                if (isset($data['Descripcion'])) {
                    $data['Nombre'] = str_replace(" ","",ucwords(strtolower($data['Descripcion'])));
                }
                */
                //no se puede modificar el nombre del concepto(variable)

                if($data['Nombre'] && $data['Nombre'] != $row->Nombre){
                    throw new Liquidacion_Model_Exception("No se puede modificar el Nombre del concepto.");
                }

                //confirmar la modificacion si el concepto ya se uso en alguna liquidacion
                $sqlConceptoLiquidado = "   SELECT LRD.Id
                                            FROM LiquidacionesRecibosDetalles LRD
                                            INNER JOIN VariablesDetalles VD ON LRD.VariableDetalle = VD.Id
                                            WHERE VD.Variable = $row->Id";

                $conceptoLiquidado = $this->_db->fetchAll($sqlConceptoLiquidado);
                if($conceptoLiquidado){
                    if (Rad_Confirm::confirm( "El concepto $row->Descripcion ya ha sido usado en una liquidacion. Esta seguro de modificarlo?", _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
                        parent::update($data, 'Id ='.$row->Id);
                    }
                } else {
                    parent::update($data, 'Id ='.$row->Id);
                }
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Borra los registros indicados
     *
     * @param array $where
     *
     */
    public function delete($where)
    {

        try {
            $this->_db->beginTransaction();

            $reg = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg)) {
                foreach ($reg as $row) {
                    //no se permite eliminar el concepto si esta usado en alguna liquidacion
                    $sqlConceptoLiquidado = "SELECT LRD.Id
                                            FROM LiquidacionesRecibosDetalles LRD
                                                INNER JOIN VariablesDetalles VD ON LRD.VariableDetalle = VD.Id
                                            WHERE VD.Variable = $row->Id";

                    $conceptoLiquidado = $this->_db->fetchAll($sqlConceptoLiquidado);
                    if($conceptoLiquidado){
                        throw new Liquidacion_Model_Exception("No se puede eliminar un concepto cuando tiene liquidaciones de sueldo.");
                    } else {
                        parent::delete('Id ='.$row->Id);
                    }
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}