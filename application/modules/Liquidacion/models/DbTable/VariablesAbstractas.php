<?php
class Liquidacion_Model_DbTable_VariablesAbstractas extends Rad_Db_Table
{
    protected $_name = 'Variables';

    protected $_sort = array('Codigo asc');

    protected $_referenceMap    = array(
	    'TiposDeVariables' => array(
            'columns'           => 'TipoDeVariable',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeVariables',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeVariables',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'TiposDeConceptosLiquidaciones' => array(
            'columns'           => 'TipoDeConceptoLiquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeConceptosLiquidaciones',
            'refJoinColumns'    => array('Descripcion','DescripcionR'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeConceptosLiquidaciones',
            'refColumns'        => 'Id',
        ),
        'VariablesCategorias' => array(
            'columns'           => 'VariableCategoria',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesCategorias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'VariablesCategorias',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_dependentTables = array(
        'Liquidacion_Model_DbTable_LiquidacionesVariablesCalculadas',
        'Liquidacion_Model_DbTable_LiquidacionesVariablesDesactivadas',
        'Liquidacion_Model_DbTable_VariablesDetallesAbstractas'
    );

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'Variables',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('La descripcion que intenta ingresar ya se encuentra en uso.')
            ),
            'Nombre' => array(
                array(  'Db_NoRecordExists',
                        'Variables',
                        'Nombre',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('La descripcion que intenta ingresar ya se encuentra en uso.')
            ),
            'FechaBaja'=> array(
                array( 'GreaterThan',
                        '{FechaAlta}'
                ),
                'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
            )
        );
        parent::init();
    }

    /**
     * Inserta un registro
     *
     * @param array $data
     *
     */
    public function insert($data)
    {

        if (isset($data['Formula']))            $data['Formula']        = trim($data['Formula']);
        if (isset($data['FormulaDetalle']))     $data['FormulaDetalle'] = trim($data['FormulaDetalle']);
        if (isset($data['Selector']))           $data['Selector']       = trim($data['Selector']);

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
        if (isset($data['Formula']))            $data['Formula']        = trim($data['Formula']);
        if (isset($data['FormulaDetalle']))     $data['FormulaDetalle'] = trim($data['FormulaDetalle']);
        if (isset($data['Selector']))           $data['Selector']       = trim($data['Selector']);

        parent::update($data, $where);
    }

    /**
     * Retorna la
     * @param string                         $nombre  nombre de la variable
     * @param int|Liquidacion_Model_Periodo  $periodo Id del perido o instancia de Liquidacion_Model_Periodo
     */
    public function getVariablePeriodo($nombre, $periodo)
    {
        //$fa = $periodo->getDesde()->format('Y-m-d');
        $fb = $periodo->getHasta()->format('Y-m-d');

        $nombre   = $this->_db->quote($nombre);
        $variable = $this->fetchRow("Nombre = $nombre AND FechaAlta <= '$fb' and ifnull(FechaBaja,'2199-01-01') >= '$fb'");

        if ($variable) {
            $mvd = new Liquidacion_Model_DbTable_VariablesDetallesAbstractas;
            return $mvd->getVariableDetallePeriodo($variable->Id, $periodo);
        } else {
            return null;
        }
    }


    /**
     * Revisa si el periodo ingresado no se superpone con uno existente
     * Esta funcion se ejecuta dentro de la transaccion de updat/insert pero despues de que
     * modifico o inserto el registro.
     *
     * @param array $data   Valores que se cambiaran o insertan
     * @param int   $Id     identificador del registro a modificar. No se usa en el insert.
     * @return boolean
     */
    public function superponeConOtroPeriodo($tabla, $fechaDesde, $fechaHasta, $nombreFechaDesde, $nombreFechaHasta, $condicion = null) {

        if (!$nombreFechaDesde || $nombreFechaHasta)  throw new Rad_Db_Table_Exception('Error de Sistema. Faltan los nombres de los campos Fecha en el control.');

        $FH = ($fechaHasta) ? $fechaHasta : '2999-01-01';
        $FD = $fechaDesde;

        // controlo por si viene un null y lo cambio a un string vacio
        $condicion = ($condicion) ? $condicion : "";

        $sql = "    SELECT  Id
                    FROM    $tabla
                    WHERE
                        (
                            /* Que las fechas esten dentro de un periodo existente */
                            (
                                (   FechaDesde <= '$FD' AND ifnull(FechaHasta,'2999-01-01') >= '$FD' )
                                OR
                                (   FechaDesde <= '$FH' AND ifnull(FechaHasta,'2999-01-01') >= '$FH' )
                            )
                            OR
                            /* Que las fechas contengan un periodo existente */
                            (
                                (   '$FD' <= FechaDesde AND '$FD' >= ifnull(FechaHasta,'2999-01-01') )
                                OR
                                (   '$FH' <= FechaDesde AND '$FH' >= ifnull(FechaHasta,'2999-01-01') )
                            )
                        )
                    $condicion
                    limit 1";

        $R = $this->_db->fetchRow($sql);

        if ($R) {
            return true;
        } else {
            return false;
        }
    }

}