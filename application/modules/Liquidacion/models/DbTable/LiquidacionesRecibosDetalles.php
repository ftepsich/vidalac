<?php
class Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles extends Rad_Db_Table
{
    protected $_name            = 'LiquidacionesRecibosDetalles';
    protected $_gridGroupField  = 'VariablesTipoDeConcepto';
    protected $_sort            = array('VariablesTipoDeConcepto', 'PeriodoDevengado desc', 'ConceptoCodigo asc');

    protected $_referenceMap    = array(

	    'LiquidacionesRecibos' => array(
            'columns'           => 'LiquidacionRecibo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesRecibos',
            'refJoinColumns'    => array('Liquidacion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'LiquidacionesRecibos',
            'refColumns'        => 'Id',
        ),
	    'VariablesDetalles' => array(
            'columns'           => 'VariableDetalle',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles',
            'refJoinColumns'    => array('Formula'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'VariablesDetalles',
            'refColumns'        => 'Id',
        ),
        'Periodos' => array(
            'columns'           => 'PeriodoDevengado',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'VariablesDetalles',
            'refColumns'        => 'Id',
        )
        );

    protected $_dependentTables = array();

    protected $_readOnlyFields = array(
        'MontoCalculado'
    );

    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('VariablesDetalles')
                ->joinRef('Variables', array(
                    'Concepto'          => 'TRIM({remote}.Descripcion)',
                    'Codigo'            => 'TRIM({remote}.Codigo)',
                    'TipoDeConcepto'    => '(CASE TRIM({remote}.TipoDeConceptoLiquidacion) WHEN 1 THEN "1: Remunerativos" WHEN 2 then "1: Remunerativos" WHEN 3 THEN "2: No Remunerativos" WHEN 4 THEN "3: Descuentos" WHEN 5 THEN "2: No Remunerativos" END)'
                ));
        }
    }


    public function update($data,$where) {
        try {
            $this->_db->beginTransaction();
            $rows = $this->fetchAll($where);

            if (!$rows) throw new Rad_Db_Table_Exception('No se encuentra recibos para modificar.');

            $logAccion      = 'Cambio en Recibo';
            $logDetalle     = '';
            $logDescripcion = '';

            foreach ($rows as $row) {

                if (isset($data['Monto']) && $data['Monto'] != $row['Monto']) {
                    $logDescripcion .= 'Monto: de '.$row['Monto'].' a '. $data['Monto'];

                    /*
                    -----------------------------------------------------------------------------
                    Veo si se modifico el concepto pago o devolucion de ganancia
                    de ser asi arreglo los acumulados de los meses sucesivos si existen hasta el mes 12
                    -----------------------------------------------------------------------------
                    */

                        // Recupero la variable desde la variable detalle
                        $M_VD       = Service_TableManager::get('Liquidacion_Model_DbTable_VariablesDetallesAbstractas');
                        $variable   = $M_VD->getVariableDesdeDetalle($row['VariableDetalle']);

                        // Verifico que sea un pago o descuento de ganancia
                        /* TODO : harcodeo de codigo */

                        // throw new Rad_Db_Table_Exception($variable);

                        if ($variable == 95 || $variable == 97) {
                            // Disparo el proceso de updatear los acumulados
                            $M_PGL  = Service_TableManager::get('Rrhh_Model_DbTable_PersonasGananciasLiquidaciones');
                            $r      = $M_PGL->updateAcumuladoGanancia($row['LiquidacionRecibo'], $data['Monto']);
                        }

                }

                if (isset($data['ConceptoCodigo']) && $data['ConceptoCodigo'] != $row['ConceptoCodigo']) {
                    $logDescripcion .= 'Codigo: de '.$row['ConceptoCodigo'].' a '. $data['ConceptoCodigo'];
                }

                if (isset($data['ConceptoNombre']) && $data['ConceptoNombre'] != $row['ConceptoNombre']) {
                    $logDescripcion .= 'Concepto: de '.$row['ConceptoNombre'].' a '. $data['ConceptoNombre'];
                }

                if ($logDescripcion) {
                    //instancio log
                    //armo el array para insertar
                    //inserto
                }

            }

            // Updateo todo junto
            parent::update($data,$where);
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    public function getSumRemunerativos($idRecibo) {
        return $this->getSumPorTipoConcepto($idRecibo,1);
    }

    public function getSumRemunerativosAgrupados($idRecibo) {
        return $this->getSumPorTipoConcepto($idRecibo,2);
    }

    public function getSumNoRemunerativos($idRecibo) {
        return $this->getSumPorTipoConcepto($idRecibo,3);
    }

    public function getSumNoRemunerativosAgrupados($idRecibo) {
        return $this->getSumPorTipoConcepto($idRecibo,5);
    }

    public function getSumDescuentos($idRecibo) {
        return $this->getSumPorTipoConcepto($idRecibo,4);
    }

    public function getSumBruto($idRecibo) {

        $sql = "    SELECT      sum(LRD.Monto)
                    FROM        LiquidacionesRecibosDetalles LRD
                    INNER JOIN  VariablesDetalles VD        on VD.Id    = LRD.VariableDetalle
                    INNER JOIN  Variables V                 on V.Id     = VD.Variable
                    WHERE       LRD.LiquidacionRecibo       = $idRecibo
                    AND         V.TipoDeConceptoLiquidacion in (1,2,3,5) /* R,RA,NR y NRA */
                ";

        $suma = $this->_db->fetchOne($sql);
        if($suma) {
            return $suma;
        } else {
            return 0;
        };
    }

   /**
     * Retorna la suma de los conceptos variables DEVENGADOS en un año sin importar si el año ya termino
     * TODO:  Ver que pasa con el plus de licencia ahora no esta agregado pero me parece que es no habitual
     *
     * @param  int              $anio       anio a revisar
     * @param  int              $idPersona  identificador de persona
     * @return decimal          monto variable devengado
     */
    public function getSumVariablesAnio(int $anio, int $idPersona) {

        $sql = "    SELECT  Sum(LRD.Monto)
                    FROM    LiquidacionesRecibosDetalles LRD
                    INNER JOIN VariablesDetalles VD     on VD.Id = LRD.VariableDetalle
                    INNER JOIN Variables V              on V.Id  = VD.Variable
                    INNER JOIN LiquidacionesRecibos LR  on LR.Id = LRD.LiquidacionRecibo
                    WHERE   V.TipoDeConcepto    = 9 -- Conceptos Variables
                    AND     LR.Ajuste           = 0
                    AND     V.NoHabitual        = 1
                    AND     ifnull(V.EsSAC,99)  <> 1
                    AND     LR.Persona          = $idPersona
                    AND     LRD.PeriodoDevengado in ( SELECT Id FROM LiquidacionesPeriodos WHERE Anio = $anio) ";

        $suma = $this->_db->fetchOne($sql);
        if($suma) {
            return $suma;
        } else {
            return 0;
        };

    }


    public function getSumPorTipoConcepto($idRecibo,$tipo) {
        $sql = "    SELECT      sum(LRD.Monto)
                    FROM        LiquidacionesRecibosDetalles LRD
                    INNER JOIN  VariablesDetalles VD        on VD.Id    = LRD.VariableDetalle
                    INNER JOIN  Variables V                 on V.Id     = VD.Variable
                    WHERE       LRD.Id              = $idRecibo
                    AND         V.TipoDeConcepto    = $tipo
                ";

        $suma = $this->_db->fetchOne($sql);
        if($suma) {
            return $suma;
        } else {
            return 0;
        };
    }


}