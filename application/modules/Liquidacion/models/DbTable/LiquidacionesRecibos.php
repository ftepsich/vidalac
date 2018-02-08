<?php
class Liquidacion_Model_DbTable_LiquidacionesRecibos extends Rad_Db_Table
{
    protected $_name = 'LiquidacionesRecibos';

    protected $_sort = array('PersonasLegajoNumero asc');

    protected $_referenceMap    = array(

	    'Liquidaciones' => array(
            'columns'           => 'Liquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_Liquidaciones',
            'refJoinColumns'    => array('LiquidacionPeriodo'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Liquidaciones',
            'refColumns'        => 'Id',
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial','LegajoNumero'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
        ),
        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Id','FechaAlta','FechaBaja'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Servicios',
            'refColumns'        => 'Id',
        ),
        'ApJubBancos' => array(
            'columns'           => 'ApJubBanco',
            'refTableClass'     => 'Base_Model_DbTable_Bancos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Bancos',
            'refColumns'        => 'Id',
        ),
        'ApJubPeriodos' => array(
            'columns'           => 'ApJubPeriodo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesPeriodos',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array('Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles','Liquidacion_Model_DbTable_LiquidacionesVariablesCalculadas');

    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('Servicios')
                ->joinRef('Convenios', array(
                    'Convenio' => 'TRIM({remote}.Descripcion)'
                ))
                ->joinRef('ConveniosCategorias', array(
                    'Categoria' => 'TRIM({remote}.Descripcion)'
                ))
                ->joinRef('Empresas', array(
                    'Empresa' => 'TRIM({remote}.Descripcion)'
                ));
        }

        // Armo un campo calculado para saber cual es el monto calculado
        $this->_calculatedFields['MontoCalculado']      = "fReciboSueldo_MontoCalculado(LiquidacionesRecibos.Id)";
        $this->_calculatedFields['MontoRetroactivos']   = "fReciboSueldo_MontoRetroactivos(LiquidacionesRecibos.Id)";
        $this->_calculatedFields['MontoPagado']         = "fReciboSueldo_MontoPagado(LiquidacionesRecibos.Id)";
        $this->_calculatedFields['CantidadAjustes']     = "(SELECT count(Id) FROM LiquidacionesRecibos lra WHERE lra.Servicio = LiquidacionesRecibos.Servicio AND lra.Periodo = LiquidacionesRecibos.Periodo and Ajuste > 0)";
        $this->_calculatedFields['PrimerAjuste']        = "(SELECT min(Ajuste) FROM LiquidacionesRecibos lra WHERE lra.Servicio = LiquidacionesRecibos.Servicio AND lra.Periodo = LiquidacionesRecibos.Periodo and Ajuste > 0)";
    }

    /**
     * Retorna los dos Id de recibos a comparar para un ajuste dado
     *
     * @param  [int] $numAjuste
     * @param  [int] $sercivio
     * @param  [int] $periodo
     * @return [array]          array los dos Id de recibos a comparar para un ajuste dado
     */
    public function getIdRecibosAjuste($numAjuste, $servicio, $periodo)
    {
        if ($numAjuste < 1) throw new Rad_Db_Table_Exception("El numero de ajuste debe ser igual o mayor a uno");

        $db = $this->_db;

        // Obtengo el tipo de la liquidacion
        $sql = "SELECT      L.TipoDeLiquidacion
                FROM        Liquidaciones L
                INNER JOIN  LiquidacionesRecibos LR ON L.ID = LR.Liquidacion
                AND         LR.Servicio    = $servicio
                AND         LR.Periodo     = $periodo
                AND         LR.Ajuste      = $numAjuste";
        $tipo = $db->fetchOne($sql);

        // Obtengo el ajuste actual
        $sql = "SELECT      LR.Id
                FROM        Liquidaciones L
                INNER JOIN  LiquidacionesRecibos LR ON L.ID = LR.Liquidacion
                WHERE       LR.Servicio         = $servicio
                AND         LR.Periodo          = $periodo
                AND         LR.Ajuste           = $numAjuste
                AND         L.TipoDeLiquidacion = $tipo
                LIMIT 1";
        $recAjActual    = $db->fetchOne($sql);

        //Obtengo el Ajuste anterior o el recibo Original
        $sql = "SELECT      LR.Id
                FROM        Liquidaciones L
                INNER JOIN  LiquidacionesRecibos LR ON L.ID = LR.Liquidacion
                WHERE       LR.Servicio         = $servicio
                AND         LR.Periodo          = $periodo
                AND         LR.Ajuste           < $numAjuste
                AND         L.TipoDeLiquidacion = $tipo
                ORDER BY    LR.Ajuste DESC
                LIMIT 1";
        $recAjAnterior  = $db->fetchOne($sql);

        if (!$recAjAnterior || !$recAjActual) throw new Rad_Db_Table_Exception("No se encontraron los recibos a comparar");
        return array($recAjAnterior, $recAjActual);
    }


    /**
     * Devuelve el proximo recibo de pago de una persona dentro del mismo año calendario y para el mismo tipo de liquidacion
     * @param  int $idRecibo    Identificador del Recibo
     * @return int              Identificador del proximo Recibo o 0 si no existe
     */
    public function proximoRecibo($idRecibo) {

        $sql = "    SELECT      LP.Anio,
                                LP.FechaDesde   as PeriodoFechaDesde,
                                LP.FechaHasta   as PeriodoFechaHasta,
                                LR.Persona,
                                L.TipoDeLiquidacion,
                                LP.Valor
                    FROM        LiquidacionesRecibos LR
                    INNER JOIN  LiquidacionesPeriodos LP    on LP.Id    = LR.Periodo
                    INNER JOIN  Liquidaciones L             on L.Id     = LR.Liquidacion
                    WHERE       LR.Id = $idRecibo
                    LIMIT 1
        ";
        $r = $this->_db->fetchRow($sql);

        if ($r) {
            $sql = "    SELECT      LR.Id as idProximoRecibo, 
                                    LP.Valor
                        FROM        LiquidacionesRecibos LR
                        INNER JOIN  LiquidacionesPeriodos LP    on LP.Id    = LR.Periodo
                        INNER JOIN  Liquidaciones L             on L.Id     = LR.Liquidacion
                        WHERE       LR.Id    <> $idRecibo       
                        AND         LP.Anio  =  {$r['Anio']}
                        AND         LP.Valor >  {$r['Valor']}
                        AND         L.TipoDeLiquidacion = {$r['TipoDeLiquidacion']}
                        AND         LR.Persona = {$r['Persona']}
                        ORDER BY    LP.Valor asc 
                        LIMIT 1
            ";
            $r = $this->_db->fetchRow($sql);

            if ($r) return $r['idProximoRecibo'];
        } 
        return 0;
    }


    /**
     * Trae solo los recibos que no son de ajustes
     *
     * @param  [type] $where  [description]
     * @param  [type] $order  [description]
     * @param  [type] $count  [description]
     * @param  [type] $offset [description]
     * @return [type]         [description]
     */
    public function fetchSinAjustes($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "LiquidacionesRecibos.Ajuste = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }
}