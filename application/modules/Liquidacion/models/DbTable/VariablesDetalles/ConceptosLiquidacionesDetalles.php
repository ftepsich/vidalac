<?php
require_once 'Rad/Db/Table.php';

/**
 * Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
 *
 * Conceptos Liquidaciones Detalles
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles
 * @extends Liquidacion_Model_DbTable_VariablesDetallesAbstractas
 */
class Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles extends Liquidacion_Model_DbTable_VariablesDetallesAbstractas
{
    protected $_name = 'VariablesDetalles';

    protected $_sort = array('Variable asc', 'Variables.FechaAlta desc');

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
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'Convenios',
            'refColumns'        => 'Id',
        ),
        'Empresas' => array(
            'columns'           => 'Empresa',
            'refTableClass'     => 'Base_Model_DbTable_Empresas',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'Empresas',
            'refColumns'        => 'Id',
        ),
        'GruposDePersonas' => array(
            'columns'           => 'GrupoDePersona',
            'refTableClass'     => 'Liquidacion_Model_DbTable_GruposDePersonas',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'GruposDePersona',
            'refColumns'        => 'Id',
        ),
        'ConveniosCategorias' => array(
            'columns'           => 'ConvenioCategoria',
            'refTableClass'     => 'Rrhh_Model_DbTable_ConveniosCategorias',
            'refJoinColumns'    => array('Descripcion'),
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'ConveniosCategorias',
            'refColumns'        => 'Id',
        ),
        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Persona'),
        //    'comboBox'            => true,
        //    'comboSource'     => 'datagateway/combolist',
            'refTable'          => 'Servicio',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array('Liquidacion_Model_DbTable_ConceptosTiposDeLiquidaciones',
                                        'Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles'
                                        );

    /**
     * Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     * @return none
     */
    public function update($data, $where)
    {
        try {

            $this->_db->beginTransaction();

            if (!$this->estaUsado($where)) {
                parent::update($data, $where);
            } else {
                $txt            = "El detalle del concepto {$row->Descripcion} ya ha sido usado en una liquidacion, de modificarse se generaran los retroactivos correspondientes. Esta seguro de modificarlo?";
                $confirmacion   = Rad_Confirm::confirm( $txt ,_FILE_._LINE_, array('includeCancel' => false));

                if ($confirmacion == 'yes'){
                    /* buscar registro y comparar formula y update */
                    $rowHistorico   = $this->fetchRow($where);
                    $dataNuevo      = $rowHistorico->toArray();


                    if(($data['Formula'] && $data['Formula'] != $dataNuevo['Formula']) || ($data['Selector'] && $data['Selector'] != $dataNuevo['Selector'])){
                        if($data['FechaDesde'] && ($data['FechaDesde'] > $dataNuevo['FechaDesde'])){
                            //Resto un dia a la fecha desde para cerrar el registo anterior
                            $FechaHasta = new DateTime($data['FechaDesde']);
                            $FechaHasta->sub(new DateInterval('P1D'));
                            $dataNuevo['FechaHasta'] = $FechaHasta->format('Y-m-d');
                            parent::update($dataNuevo, $where);
                            unset($dataNuevo['Id']);
                            $rowHistorico = $this->createRow($dataNuevo);

                            $rowHistorico->Historico = 1;
                            $rowHistorico->FechaDesde = $data['FechaDesde'];
                            $rowHistorico->save();
                        } else {
                            $dataNuevo['Historico'] = 1;
                            $dataNuevo['Id'] = null;
                            unset($dataNuevo['Id']);
                            //Rad_Log::debug($dataNuevo);
                            //throw new Exception(print_r($dataNuevo, true));
                            parent::update($dataNuevo, $where);
                            unset($dataNuevo['Id']);
                            $dataNuevo['Historico'] = 0;
                        }

                        $dataNuevo['Id'] = null;
                        if($data['Descripcion']) $dataNuevo['Descripcion'] = $data['Descripcion'];
                        if($data['Convenio']) $dataNuevo['Convenio'] = $data['Convenio'];
                        if($data['Empresa']) $dataNuevo['Empresa'] = $data['Empresa'];
                        if($data['ConvenioCategoria']) $dataNuevo['ConvenioCategoria'] = $data['ConvenioCategoria'];
                        if($data['GrupoDePersona']) $dataNuevo['GrupoDePersona'] = $data['GrupoDePersona'];
                        if($data['Servicio']) $dataNuevo['Servicio'] = $data['Servicio'];
                        if($data['FechaHasta']) $dataNuevo['FechaHasta'] = $data['FechaHasta'];
                        if($data['FechaDesde']) $dataNuevo['FechaDesde'] = $data['FechaDesde'];
                        if($data['Formula']) $dataNuevo['Formula'] = $data['Formula'];
                        if($data['FormulaDetalle']) $dataNuevo['FormulaDetalle'] = $data['FormulaDetalle'];
                        if($data['Selector']) $dataNuevo['Selector'] = $data['Selector'];
                        if($data['Observaciones']) $dataNuevo['Observaciones'] = $data['Observaciones'];

                        /* Ojo... no debe logear este insert sino recalcula para todos ya que este detalle no lo tiene nadie */
                        parent::insert($dataNuevo);

                    } else {
                        parent::update($data, $where);
                    }
                }
            }

            $this->_db->commit();

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Verifica si un concepto esta usado en una liquidacion
     *
     * @param   array   $where  Registros que se deben modificar
     * @return  boolean
     *
     */
    public function estaUsado($where) {
        $usado  = false;
        $M_LRD  = new Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles;
        $reg    = $this->fetchAll($where);
        foreach ($reg as $row) {
            //Para confirmar si la modificacion del detalle del concepto ya se uso en alguna liquidacion
            $R_LRD = $M_LRD->fetchRow("VariableDetalle = {$row->Id}");
            // Si alguno esta usado en un recibo, seteo y salgo
            if(count($R_LRD)) {
                $usado = true;
                break;
            }
        }
        return $usado;
    }

    /**
     * Retorna el Codigo de un concepto
     * @param  int      variableDetalleId   Identificador de VariablesDetalles
     * @return varchar                      Codigo del Concepto
     */
    public function getCodigo($variableDetalleId) {

        $variableDetalleId  = $this->_db->quote($variableDetalleId, 'INTEGER');
        $sql    = " SELECT V.Codigo
                    FROM   Variables V
                    INNER JOIN VariablesDetalles VD on V.Id = VD.Variable
                    WHERE  VD.Id = $variableDetalleId";
        $codigo = $this->_db->fetchOne($sql);
        return $codigo;
    }

    /**
     * Retorna el Nombre de un concepto
     * @param  int      variableDetalleId   Identificador de VariablesDetalles
     * @return varchar                      Nombre del Concepto
     */
    public function getNombre($variableDetalleId) {

        $variableDetalleId  = $this->_db->quote($variableDetalleId, 'INTEGER');
        $sql    = " SELECT V.Descripcion
                    FROM   Variables V
                    INNER JOIN VariablesDetalles VD on V.Id = VD.Variable
                    WHERE  VD.Id = $variableDetalleId";
        $descripcion = $this->_db->fetchOne($sql);
        return $descripcion;
    }

    public function fetchSinHistoricos($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "VariablesDetalles.Historico = 0 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}